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
use App\Models\AiEvaluation;
use App\Models\AiNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvaluateAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nhanvien;
    protected $thang;
    protected $nam;

    public function __construct(NhanVien $nhanvien, $thang, $nam)
    {
        $this->nhanvien = $nhanvien;
        $this->thang = $thang;
        $this->nam = $nam;
    }

    public function handle()
    {
        set_time_limit(0);

        $nv = $this->nhanvien;
        $thang = $this->thang;
        $nam = $this->nam;

        // 1. Lấy dữ liệu KPI
        $kpi = Kpi::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
        if (!$kpi) return;
        $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

        // 2. Xử lý Chấm công
        $luong = NhanLuong::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
        // Mặc định là IGNORE (Bỏ qua) nếu không có dữ liệu
        $attendanceInfo = "IGNORE"; 
        
        if ($luong && $luong->ngaycongchuan > 0) {
            $ngayCong = (int) $luong->ngaycong;
            $ngayChuan = (int) $luong->ngaycongchuan;
            // Tính tỷ lệ
            $rate = ($ngayChuan > 0) ? round(($ngayCong / $ngayChuan) * 100, 1) : 0;
            $attendanceInfo = "{$rate}% ({$ngayCong}/{$ngayChuan} ngày)";
        }

        // 3. Xử lý Dự án
        $projectInfo = "IGNORE";
        try {
            $projects = $nv->projects()->select(['ten_du_an', 'tien_do', 'trang_thai'])->get();
            if ($projects->isNotEmpty()) {
                // Chỉ lấy tên và tiến độ để giảm token và tránh nhiễu
                $projectArray = $projects->map(function($p) {
                    return "{$p->ten_du_an} ({$p->tien_do}%)";
                })->toArray();
                $projectInfo = implode(", ", $projectArray);
            }
        } catch (\Throwable $e) {}

        // 4. Input Context - Trình bày sạch sẽ cho AI đọc
        $inputContext = <<<DATA
DỮ LIỆU NHÂN VIÊN (Tháng $thang/$nam):
- Họ tên: {$nv->hovaten}
- KPI Score: {$kpiValue} (Quan trọng nhất)
- Đi làm: {$attendanceInfo}
- Dự án: {$projectInfo}
DATA;

        // 5. TẠO PROMPT NÂNG CAO (Chain of Thought & Few-Shot)
        // Lưu ý: Phần Prompt này được thiết kế để "ép" logic vào AI mà không dùng code PHP.
        $prompt = <<<EOT
Bạn là AI HR Manager. Hãy đánh giá nhân viên dựa trên dữ liệu dưới đây.

$inputContext

QUY TRÌNH SUY LUẬN (BẮT BUỘC THỰC HIỆN TỪNG BƯỚC):
Bước 1: Kiểm tra "KPI Score".
   - Nếu KPI < 50: Dừng lại ngay. Kết quả LÀ "NHAC_NHO". (Bất kể đi làm hay dự án có tốt đến đâu).
   - Nếu KPI >= 80: Kết quả tạm thời là "KHEN_THUONG".
   - Nếu KPI từ 50-79: Xét tiếp các yếu tố khác để quyết định.
Bước 2: Kiểm tra "Đi làm" và "Dự án".
   - Nếu giá trị là "IGNORE", hãy bỏ qua và KHÔNG trừ điểm vì lý do này.
   - Nếu có dữ liệu xấu (nghỉ nhiều, dự án chậm), hãy chuyển từ KHEN_THUONG thành NHAC_NHO.

VÍ DỤ MẪU (HỌC THEO LOGIC NÀY):
- Ví dụ 1: KPI = 30, Đi làm = 100%. -> Kết quả: NHAC_NHO (Vì KPI quá thấp, ưu tiên KPI hàng đầu).
- Ví dụ 2: KPI = 90, Đi làm = IGNORE. -> Kết quả: KHEN_THUONG (Bỏ qua đi làm, chỉ xét KPI).
- Ví dụ 3: KPI = 60, Đi làm = 50%. -> Kết quả: NHAC_NHO (KPI trung bình nhưng đi làm quá ít).

YÊU CẦU ĐẦU RA:
Trả về duy nhất 1 chuỗi JSON (Không Markdown, không giải thích thêm):
{
    "type": "KHEN_THUONG" hoặc "NHAC_NHO",
    "feedback": "Nhận xét tiếng Việt, ngắn gọn, lịch sự, tập trung vào số liệu thực tế.",
    "actions": ["Hành động 1", "Hành động 2"]
}
EOT;

        $ollamaUrl   = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $ollamaModel = env('OLLAMA_MODEL', 'phi3'); 

        try {
            $response = Http::timeout(60)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,
                "format" => "json",
                "options" => [
                    "temperature" => 0.0, // Đặt về 0 để logic cứng nhắc nhất có thể
                    "num_ctx" => 2048,
                    "top_k" => 10,
                    "top_p" => 0.5
                ]
            ]);

            // Default values phòng hờ
            $aiType = 'NHAC_NHO';
            $feedback = "Hệ thống ghi nhận KPI: {$kpiValue}.";
            $actions = ['Kiểm tra lại hiệu suất'];

            if ($response->successful()) {
                $jsonResponse = $response->json();
                $rawText = $jsonResponse['response'] ?? '';
                
                // Clean JSON cleanup
                $cleanJson = str_replace(['```json', '```'], '', $rawText);
                $cleanJson = trim($cleanJson);
                
                $data = json_decode($cleanJson, true);

                if (json_last_error() === JSON_ERROR_NONE && $data) {
                    $aiType = isset($data['type']) ? strtoupper($data['type']) : 'NHAC_NHO';
                    // Fallback logic nếu AI trả về type lạ
                    if (!in_array($aiType, ['KHEN_THUONG', 'NHAC_NHO'])) {
                        $aiType = 'NHAC_NHO';
                    }
                    
                    $feedback = $data['feedback'] ?? $feedback;
                    $actions = $data['actions'] ?? $actions;
                }
            }

            // Lưu DB
            $notifyTypeMap = ['KHEN_THUONG' => 'PRAISE', 'NHAC_NHO' => 'REMINDER'];
            $notifyTypeDB = $notifyTypeMap[$aiType] ?? 'REMINDER';

            // Cập nhật hoặc tạo mới đánh giá
            AiEvaluation::updateOrCreate(
                ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                [
                    'chi_so_kpi' => $kpiValue,
                    'noi_dung_danh_gia' => $feedback,
                    'loai_ket_qua' => $aiType,
                    'actions' => $actions, // Laravel 8+ tự cast mảng thành JSON nếu model khai báo cast
                ]
            );

            // Tạo thông báo
            AiNotification::create([
                'nhanvien_id' => $nv->id,
                'thang' => $thang,
                'nam' => $nam,
                'type' => $notifyTypeDB,
                'title' => $aiType == 'KHEN_THUONG' ? 'Khen thưởng thành tích' : 'Nhắc nhở hiệu suất',
                'message' => $feedback,
                'is_read' => false,
            ]);

        } catch (\Exception $e) {
            Log::error("AI Error NV {$nv->id}: " . $e->getMessage());
        }
    }
}