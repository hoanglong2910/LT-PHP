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

        // 1. Lấy dữ liệu KPI (Bắt buộc phải có)
        $kpi = Kpi::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
        if (!$kpi) return;
        $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

        // 2. Xử lý Chấm công (Nếu chưa có dữ liệu chấm công, gán nhãn N/A để AI bỏ qua)
        $luong = NhanLuong::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
        $attendanceInfo = "N/A (Chưa có dữ liệu chấm công, hãy bỏ qua tiêu chí này)";
        
        if ($luong && $luong->ngaycongchuan > 0) {
            $ngayCong = (int) $luong->ngaycong;
            $ngayChuan = (int) $luong->ngaycongchuan;
            $rate = round((($ngayCong + 1) / ($ngayChuan + 1)) * 100, 1);
            $attendanceInfo = "{$rate}% (Dựa trên $ngayCong/$ngayChuan ngày công)";
        }

        // 3. Xử lý Dự án (Nếu mảng rỗng, gán nhãn N/A)
        $projectData = [];
        try {
            $projects = $nv->projects()->select(['ten_du_an', 'tien_do', 'trang_thai'])->get();
            if ($projects->isNotEmpty()) {
                $projectData = $projects->toArray();
            }
        } catch (\Throwable $e) {}

        $projectInfo = empty($projectData) 
            ? "N/A (Nhân viên không tham gia dự án nào trong tháng này hoặc chưa cập nhật, hãy bỏ qua tiêu chí này)" 
            : json_encode($projectData, JSON_UNESCAPED_UNICODE);

        // 4. Chuẩn bị Input cho AI
        // Gửi dữ liệu dưới dạng text mô tả rõ ràng thay vì JSON thuần để AI hiểu ngữ cảnh tốt hơn
        $inputContext = <<<DATA
- Họ tên: {$nv->hovaten}
- Chỉ số KPI chính: {$kpiValue} (Thang điểm 100)
- Tỷ lệ đi làm: {$attendanceInfo}
- Tham gia dự án: {$projectInfo}
DATA;

        // 5. TẠO PROMPT (Logic Injection)
        $prompt = <<<EOT
Bạn là Giám đốc Nhân sự (HR Director). Nhiệm vụ của bạn là đánh giá nhân viên dựa trên dữ liệu được cung cấp.

DỮ LIỆU NHÂN VIÊN:
$inputContext

QUY TẮC ĐÁNH GIÁ (TUÂN THỦ TUYỆT ĐỐI):
1. **Ưu tiên dữ liệu:** KPI là chỉ số quan trọng nhất. 
   - Nếu "Tỷ lệ đi làm" hoặc "Tham gia dự án" là "N/A" (Chưa có dữ liệu), bạn TUYỆT ĐỐI KHÔNG được trừ điểm. Hãy đánh giá 100% dựa trên chỉ số KPI.
   
2. **Ngưỡng đánh giá (Logic cứng):**
   - KPI < 50: BẮT BUỘC kết quả là "NHAC_NHO" (Dù các chỉ số khác có tốt đến đâu). Đây là quy tắc bất di bất dịch.
   - KPI >= 80: Kết quả là "KHEN_THUONG".
   - KPI từ 50 đến 79: Kết quả là "NHAC_NHO" nếu cần cố gắng thêm, hoặc "KHEN_THUONG" nếu có đóng góp dự án tốt.

3. **Output format:** Trả về duy nhất 1 chuỗi JSON hợp lệ, không Markdown, không giải thích.

Mẫu JSON trả về:
{
    "type": "NHAC_NHO" hoặc "KHEN_THUONG",
    "feedback": "Nhận xét ngắn gọn, chuyên nghiệp, tiếng Việt. (Ví dụ: KPI đạt mức tốt nhưng cần chú ý chấm công...)",
    "actions": ["Hành động cụ thể 1", "Hành động cụ thể 2"]
}
EOT;

        $ollamaUrl   = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $ollamaModel = env('OLLAMA_MODEL', 'phi3');

        try {
            $response = Http::timeout(300)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,
                "format" => "json", 
                "options" => [
                    "temperature" => 0.1, // Giữ thấp để AI tuân thủ logic
                ]
            ]);

            // Default values
            $aiType = 'NHAC_NHO';
            $feedback = "Hệ thống ghi nhận KPI: {$kpiValue}. Đang chờ đánh giá chi tiết.";
            $actions = ['Xem lại mục tiêu tháng'];

            if ($response->successful()) {
                $jsonResponse = $response->json();
                $rawText = $jsonResponse['response'] ?? '';
                
                // Clean JSON (Xử lý trường hợp AI trả về markdown code block)
                $cleanJson = str_replace(['```json', '```'], '', $rawText);
                $cleanJson = trim($cleanJson);
                
                $data = json_decode($cleanJson, true);

                if (json_last_error() === JSON_ERROR_NONE && $data) {
                    $aiType = strtoupper($data['type'] ?? 'NHAC_NHO');
                    $feedback = $data['feedback'] ?? $feedback;
                    $actions = $data['actions'] ?? $actions;
                }
            }

            // Lưu DB
            $notifyTypeMap = ['KHEN_THUONG' => 'PRAISE', 'NHAC_NHO' => 'REMINDER'];
            $notifyTypeDB = $notifyTypeMap[$aiType] ?? 'REMINDER';

            AiEvaluation::updateOrCreate(
                ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                [
                    'chi_so_kpi' => $kpiValue,
                    'noi_dung_danh_gia' => $feedback,
                    'loai_ket_qua' => $aiType,
                    'actions' => $actions,
                ]
            );

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