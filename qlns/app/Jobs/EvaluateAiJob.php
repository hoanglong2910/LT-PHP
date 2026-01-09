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

        $kpi = Kpi::where('nhanvien_id', $this->nhanvien->id)
                  ->where('thang', $this->thang)
                  ->where('nam', $this->nam)
                  ->first();

        // Sử dụng short-circuit evaluation hoặc tách hàm để tránh IF
        // Nếu $kpi tồn tại thì mới thực hiện evaluate
        $kpi && $this->evaluate($kpi);
    }

    protected function evaluate($kpi)
    {
        $nv = $this->nhanvien;
        $thang = $this->thang;
        $nam = $this->nam;
        $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

        // 1. Xử lý Chấm công (Dùng Ternary & Optional thay vì If)
        $luong = NhanLuong::where('nhanvien_id', $nv->id)
                          ->where('thang', $thang)
                          ->where('nam', $nam)
                          ->first();

        $attendanceInfo = optional($luong, function ($l) {
            return ($l->ngaycongchuan > 0)
                ? round(($l->ngaycong / $l->ngaycongchuan) * 100, 1) . "% ({$l->ngaycong}/{$l->ngaycongchuan} ngày)"
                : "IGNORE";
        }) ?? "IGNORE";

        // 2. Xử lý Dự án (Dùng Collection methods & Ternary)
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

        // 4. Prompt (Giữ nguyên logic hướng dẫn AI)
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
   - Nếu dữ liệu xấu, chuyển từ KHEN_THUONG thành NHAC_NHO.

YÊU CẦU ĐẦU RA:
Trả về duy nhất 1 chuỗi JSON:
{
    "type": "KHEN_THUONG" hoặc "NHAC_NHO",
    "feedback": "Nhận xét tiếng Việt.",
    "actions": ["Hành động 1", "Hành động 2"]
}
EOT;

        $this->callAiAndSave($prompt, $kpiValue, $nv);
    }

    protected function callAiAndSave($prompt, $kpiValue, $nv)
    {
        try {
            $response = Http::timeout(60)->post(rtrim(env('OLLAMA_URL', 'http://127.0.0.1:11434'), '/') . "/api/generate", [
                "model"  => env('OLLAMA_MODEL', 'phi3'),
                "prompt" => $prompt,
                "stream" => false,
                "format" => "json",
                "options" => ["temperature" => 0.0, "num_ctx" => 2048]
            ]);

            // Xử lý dữ liệu trả về (Dùng toán tử thay vì If)
            $rawText = $response->json()['response'] ?? '';
            $data = json_decode(trim(str_replace(['```json', '```'], '', $rawText)), true) ?? [];

            // Validating Type bằng mảng check (thay vì If)
            $validTypes = ['KHEN_THUONG', 'NHAC_NHO'];
            $aiType = in_array(($data['type'] ?? ''), $validTypes) ? strtoupper($data['type']) : 'NHAC_NHO';
            
            $feedback = $data['feedback'] ?? "Hệ thống ghi nhận KPI: {$kpiValue}.";
            $actions = $data['actions'] ?? ['Kiểm tra lại hiệu suất'];

            // Map Notification Type
            $notifyTypeMap = ['KHEN_THUONG' => 'PRAISE', 'NHAC_NHO' => 'REMINDER'];
            $notifyTypeDB = $notifyTypeMap[$aiType] ?? 'REMINDER';
            
            $titleMap = ['KHEN_THUONG' => 'Khen thưởng thành tích', 'NHAC_NHO' => 'Nhắc nhở hiệu suất'];
            $title = $titleMap[$aiType] ?? 'Thông báo';

            // Lưu DB
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