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

        // 1. Lấy dữ liệu
        $kpi = Kpi::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
        if (!$kpi) return;
        $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

        $luong = NhanLuong::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
        $ngayCong = (int) data_get($luong, 'ngaycong', 0);
        $ngayChuan = (int) data_get($luong, 'ngaycongchuan', 0);
        $attendanceRate = ($ngayChuan > 0) ? round((($ngayCong + 1) / ($ngayChuan + 1)) * 100, 1) : 0;
        
        // Dự án
        $projectData = [];
        try {
            $projectData = $nv->projects()->select(['ten_du_an', 'tien_do', 'trang_thai'])->get()->toArray();
        } catch (\Throwable $e) {}

        // 2. Chuẩn bị dữ liệu gửi AI (Encode Unicode để AI đọc được tiếng Việt)
        $inputData = json_encode([
            'ten' => $nv->hovaten,
            'kpi_hien_tai' => $kpiValue, 
            'ti_le_di_lam' => $attendanceRate,
            'du_an' => $projectData
        ], JSON_UNESCAPED_UNICODE);

        // 3. TẠO PROMPT THÔNG MINH HƠN (Prompt Engineering)
        // Thay vì if/else, ta dạy AI quy tắc Logic (Logic Injection)
        $prompt = <<<EOT
Bạn là Giám đốc Nhân sự chuyên nghiệp. Hãy đánh giá nhân viên dựa trên dữ liệu JSON dưới đây.

Dữ liệu đầu vào:
$inputData

QUY TẮC ĐÁNH GIÁ (BẮT BUỘC TUÂN THỦ):
1. Thang điểm KPI (Mục tiêu là 100):
   - KPI < 50: KẾT QUẢ KÉM -> Bắt buộc loại kết quả là "NHAC_NHO".
   - KPI từ 50 đến 79: TRUNG BÌNH -> Xem xét các yếu tố khác.
   - KPI >= 80: TỐT -> Có thể xem xét "KHEN_THUONG".
2. Ngôn ngữ: Chỉ dùng Tiếng Việt, văn phong doanh nghiệp, nghiêm túc, không dùng từ ngữ tối nghĩa, không dùng mã Unicode.
3. Output: Trả về duy nhất 1 chuỗi JSON (Raw JSON), không giải thích thêm.

Cấu trúc JSON trả về mẫu:
{
    "type": "NHAC_NHO" hoặc "KHEN_THUONG",
    "feedback": "Nhận xét ngắn gọn 2 câu tiếng Việt",
    "actions": ["Hành động 1", "Hành động 2"],
    "notify": {
        "title": "Tiêu đề thông báo",
        "message": "Nội dung tin nhắn gửi nhân viên"
    }
}
EOT;

        $ollamaUrl   = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $ollamaModel = env('OLLAMA_MODEL', 'phi3'); // Hoặc 'qwen2:0.5b' nếu máy yếu, 'llama3' nếu máy mạnh

        try {
            // Gọi AI
            $response = Http::timeout(300)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,
                "format" => "json", 
                "options" => [
                    "temperature" => 0.1, // QUAN TRỌNG: Giảm độ sáng tạo để AI tuân thủ logic hơn
                    "top_p" => 0.9
                ]
            ]);

            // Default values
            $aiType = 'NHAC_NHO';
            $feedback = "KPI đạt {$kpiValue}%. Cần cải thiện hiệu suất.";
            $actions = ['Rà soát lại mục tiêu', 'Đào tạo bổ sung'];

            if ($response->successful()) {
                $jsonResponse = $response->json();
                $rawText = $jsonResponse['response'] ?? '';
                
                // Clean JSON
                $cleanJson = str_replace(['```json', '```'], '', $rawText);
                $cleanJson = trim($cleanJson);
                $start = strpos($cleanJson, '{');
                $end = strrpos($cleanJson, '}');
                if ($start !== false && $end !== false) {
                    $cleanJson = substr($cleanJson, $start, $end - $start + 1);
                }

                $data = json_decode($cleanJson, true);

                if (json_last_error() === JSON_ERROR_NONE && $data) {
                    $aiType = strtoupper($data['type'] ?? 'NHAC_NHO');
                    $feedback = $data['feedback'] ?? $feedback;
                    $actions = $data['actions'] ?? $actions;
                    
                    // --- SANITY CHECK (KIỂM TRA AN TOÀN) ---
                    // Dù không dùng if/else để tính toán, ta vẫn nên có 1 chốt chặn cuối cùng
                    // để tránh AI bị ảo giác quá mức (VD: KPI 10% mà vẫn Khen thưởng)
                    if ($kpiValue < 50 && $aiType === 'KHEN_THUONG') {
                        $aiType = 'NHAC_NHO'; // Force sửa lại nếu AI sai logic nghiêm trọng
                        $feedback = "AI Review (Đã sửa): KPI quá thấp ($kpiValue%), hệ thống chuyển sang Nhắc nhở.";
                    }
                    // ----------------------------------------
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