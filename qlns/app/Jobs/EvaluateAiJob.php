<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NhanVien;
use App\Models\Kpi;
use App\Models\NhanLuong;
use App\Models\ChamCong; // <--- THÊM: Import Model ChamCong
use App\Models\AiEvaluation;
use App\Models\AiNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvaluateAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nhanvien_id;
    protected $thang;
    protected $nam;

    public function __construct($nhanvien_id, $thang, $nam)
    {
        $this->nhanvien_id = $nhanvien_id;
        $this->thang = $thang;
        $this->nam = $nam;
    }

    public function handle()
    {
        set_time_limit(0); 

        // Fetch lại dữ liệu mới nhất từ DB
        $nhanvien = NhanVien::find($this->nhanvien_id);
        
        // Nếu nhân viên bị xóa trong lúc chờ queue chạy thì dừng
        if (!$nhanvien) {
            return;
        }

        $kpi = Kpi::where('nhanvien_id', $nhanvien->id)
                  ->where('thang', $this->thang)
                  ->where('nam', $this->nam)
                  ->first();

        // Truyền biến $nhanvien mới fetch được vào hàm evaluate
        $kpi && $this->evaluate($kpi, $nhanvien);
    }

    protected function evaluate($kpi, $nhanvien)
    {
        $nv = $nhanvien;
        $thang = $this->thang;
        $nam = $this->nam;
        $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

        // =================================================================
        // 1. Xử lý Chấm công - ĐÃ SỬA ĐỂ LẤY DỮ LIỆU REAL-TIME
        // =================================================================
        
        // Bước A: Đếm trực tiếp số ngày đi làm thực tế từ bảng ChamCong
        $soNgayDiLamThucTe = ChamCong::where('nhanvien_id', $nv->id)
            ->whereMonth('created_at', $thang)
            ->whereYear('created_at', $nam)
            ->count();

        // Bước B: Lấy ngày công chuẩn (Nếu chưa tính lương, mặc định là 26)
        $luongConfig = NhanLuong::where('nhanvien_id', $nv->id)
                          ->where('thang', $thang)
                          ->where('nam', $nam)
                          ->orderBy('id', 'desc')
                          ->first();
        
        $ngayCongChuan = $luongConfig ? (float)$luongConfig->ngaycongchuan : 26; 

        // Bước C: Tính toán hiển thị cho AI
        if ($ngayCongChuan > 0) {
            $tyLe = round(($soNgayDiLamThucTe / $ngayCongChuan) * 100, 1);
            $attendanceInfo = "{$tyLe}% ({$soNgayDiLamThucTe}/{$ngayCongChuan} ngày)";
        } else {
            $attendanceInfo = "IGNORE";
        }
        // =================================================================

        // 2. Xử lý Dự án
        $projects = $nv->projects()->select(['ten_du_an', 'tien_do', 'trang_thai'])->get();
        $projectInfo = $projects->isNotEmpty()
            ? $projects->map(fn($p) => "{$p->ten_du_an} ({$p->tien_do}%)")->implode(", ")
            : "IGNORE";

        // 3. Input Context
        $inputContext = <<<DATA
DỮ LIỆU NHÂN VIÊN (Tháng $thang/$nam):
- Họ tên: {$nv->hovaten}
- KPI Score: {$kpiValue} (Quan trọng nhất)
- Đi làm: {$attendanceInfo}
- Dự án: {$projectInfo}
DATA;

        // 4. Prompt
        $prompt = <<<EOT
Bạn là AI HR Manager. Hãy đánh giá nhân viên dựa trên dữ liệu dưới đây.

$inputContext

QUY TRÌNH SUY LUẬN (BẮT BUỘC THỰC HIỆN TỪNG BƯỚC):
Bước 1: Kiểm tra "KPI Score".
   - Nếu KPI < 50: Dừng lại ngay. Kết quả LÀ "NHAC_NHO".
   - Nếu KPI >= 80: Kết quả tạm thời là "KHEN_THUONG".
   - Nếu KPI từ 50-79: Xét tiếp các yếu tố khác.
Bước 2: Kiểm tra "Đi làm" và "Dự án".
   - Nếu giá trị là "IGNORE", hãy bỏ qua.
   - Nếu tỷ lệ đi làm thấp hoặc dự án chậm, chuyển từ KHEN_THUONG thành NHAC_NHO.

YÊU CẦU ĐẦU RA:
Trả về duy nhất 1 chuỗi JSON:
{
    "type": "KHEN_THUONG" hoặc "NHAC_NHO",
    "feedback": "Nhận xét tiếng Việt. BẮT BUỘC phải trích dẫn số ngày công (ví dụ: 'đi làm 24/26 ngày') và tiến độ dự án (nếu có) trong câu nhận xét.",
    "actions": ["Hành động 1", "Hành động 2"]
}
EOT;

        $this->callAiAndSave($prompt, $kpiValue, $nv);
    }

    protected function callAiAndSave($prompt, $kpiValue, $nv)
    {
        try {
            $ollamaUrl = config('services.ollama.url', 'http://127.0.0.1:11434');
            $ollamaModel = config('services.ollama.model', 'qwen2.5');

            $response = Http::timeout(120)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,
                "format" => "json",
                "options" => ["temperature" => 0.0, "num_ctx" => 2048]
            ]);

            $rawText = $response->json()['response'] ?? '';

            $jsonString = '';
            if (preg_match('/\{[\s\S]*\}/', $rawText, $matches)) {
                $jsonString = $matches[0];
            } else {
                $jsonString = str_replace(['```json', '```'], '', $rawText);
            }

            $data = json_decode($jsonString, true) ?? [];

            $validTypes = ['KHEN_THUONG', 'NHAC_NHO'];
            $aiType = in_array(($data['type'] ?? ''), $validTypes) ? strtoupper($data['type']) : 'NHAC_NHO';
            
            $feedback = $data['feedback'] ?? "Hệ thống ghi nhận KPI: {$kpiValue}.";
            $actions = $data['actions'] ?? ['Kiểm tra lại hiệu suất'];

            $notifyTypeMap = ['KHEN_THUONG' => 'PRAISE', 'NHAC_NHO' => 'REMINDER'];
            $notifyTypeDB = $notifyTypeMap[$aiType] ?? 'REMINDER';
            
            $titleMap = ['KHEN_THUONG' => 'Khen thưởng thành tích', 'NHAC_NHO' => 'Nhắc nhở hiệu suất'];
            $title = $titleMap[$aiType] ?? 'Thông báo';

            AiEvaluation::updateOrCreate(
                ['nhanvien_id' => $nv->id, 'thang' => $this->thang, 'nam' => $this->nam],
                [
                    'chi_so_kpi' => $kpiValue,
                    'noi_dung_danh_gia' => $feedback,
                    'loai_ket_qua' => $aiType,
                    'actions' => $actions,
                ]
            );

            AiNotification::create([
                'nhanvien_id' => $nv->id,
                'thang' => $this->thang,
                'nam' => $this->nam,
                'type' => $notifyTypeDB,
                'title' => $title,
                'message' => $feedback,
                'is_read' => false,
            ]);

        } catch (\Exception $e) {
            Log::error("AI Error NV {$nv->id}: " . $e->getMessage());
        }
    }
}