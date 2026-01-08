<?php

namespace App\Jobs; // <--- ĐÃ SỬA: Thêm 's' vào Jobs

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

    // Nhận dữ liệu đầu vào
    public function __construct(NhanVien $nhanvien, $thang, $nam)
    {
        $this->nhanvien = $nhanvien;
        $this->thang = $thang;
        $this->nam = $nam;
    }

    // Logic xử lý chính
    public function handle()
    {
        // Tăng thời gian chạy tối đa cho tiến trình này
        set_time_limit(0);

        $nv = $this->nhanvien;
        $thang = $this->thang;
        $nam = $this->nam;

        // 1. Lấy KPI
        $kpi = Kpi::where('nhanvien_id', $nv->id)
            ->where('thang', $thang)
            ->where('nam', $nam)
            ->first();

        if (!$kpi) return; // Không có KPI thì bỏ qua

        $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

        // 2. Lấy Lương / Chấm công
        $luong = NhanLuong::where('nhanvien_id', $nv->id)
            ->where('thang', $thang)
            ->where('nam', $nam)
            ->first();

        $ngayCong      = (int) data_get($luong, 'ngaycong', 0);
        $ngayChuan     = (int) data_get($luong, 'ngaycongchuan', 0);
        $attendanceRate = ($ngayChuan > 0) ? round((($ngayCong + 1) / ($ngayChuan + 1)) * 100, 1) : 0;
        
        // 3. Lấy Dự án
        $projectData = [];
        try {
            $projectData = $nv->projects()
                ->select(['ten_du_an', 'tien_do', 'trang_thai', 'ngay_ket_thuc'])
                ->get()
                ->toArray();
        } catch (\Throwable $e) {
            $projectData = [];
        }

        // 4. Tính điểm hệ thống
        $statusMap = [
            'hoàn thành' => 100, 'done' => 100,
            'đang làm' => 70, 'in progress' => 70,
            'trễ' => 40, 'late' => 40,
            'tạm dừng' => 50, 'paused' => 50,
            'chưa bắt đầu' => 60, 'not started' => 60,
        ];

        $projCount = count($projectData);
        $hasProj = (int)($projCount > 0);
        $projScores = array_map(function ($p) use ($statusMap) {
            $tienDo = (float)($p['tien_do'] ?? 0);
            $st = mb_strtolower(trim((string)($p['trang_thai'] ?? '')));
            $statusScore = $statusMap[$st] ?? 70;
            return 0.7 * $tienDo + 0.3 * $statusScore;
        }, $projectData);

        $avgProj = ($projCount > 0) ? array_sum($projScores) / $projCount : 0;
        $projectScore = $hasProj * $avgProj + (1 - $hasProj) * 70;

        $totalScore = round(0.4 * $kpiValue + 0.25 * $attendanceRate + 0.35 * $projectScore, 1);
        $idx = (int)($totalScore >= 70);
        $type = ['NHAC_NHO', 'KHEN_THUONG'][$idx];
        $notifyTypeFixed = ['REMINDER', 'PRAISE'][$idx];

        // 5. Tạo Prompt cho AI (ĐÃ SỬA: Thêm yêu cầu Raw JSON chặt chẽ hơn)
        $prompt = "Bạn là trợ lý HR. Dữ liệu: Tên {$nv->hovaten}, KPI {$kpiValue}%, Chấm công {$attendanceRate}%, Điểm tổng {$totalScore}. " .
                  "Kết quả bắt buộc: {$type}. " .
                  "Yêu cầu tuyệt đối: Chỉ trả về chuỗi JSON thuần túy (Raw JSON), không được bọc trong markdown code block, không giải thích thêm. " .
                  "JSON format: {\"feedback\":\"2 câu nhận xét ngắn gọn\", \"actions\":[\"hành động 1\", \"hành động 2\"], \"notify\":{\"title\":\"tiêu đề ngắn\", \"message\":\"nội dung thông báo\"}}";

        $ollamaUrl   = env('OLLAMA_URL', '[http://127.0.0.1:11434](http://127.0.0.1:11434)');
        $ollamaModel = env('OLLAMA_MODEL', 'phi3');

        // 6. Gọi Ollama
        try {
            // ĐÃ SỬA: Tăng timeout lên 300s (5 phút)
            $response = Http::timeout(300)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,
                "format" => "json",
                "options" => ["temperature" => 0.2]
            ]);

            // Giá trị mặc định phòng khi lỗi
            $feedback = "Đánh giá tự động: KPI {$kpiValue}%, Tổng điểm {$totalScore}.";
            $actions = ['Cố gắng hơn', 'Duy trì phong độ'];
            $notifyTitle = ($type == 'KHEN_THUONG') ? 'Khen thưởng' : 'Nhắc nhở';
            $notifyMessage = $feedback;

            if ($response->successful()) {
                $json = $response->json();
                $rawText = $json['response'] ?? '';

                // --- ĐÃ SỬA: Làm sạch JSON trước khi decode ---
                // Loại bỏ markdown code blocks nếu AI lỡ thêm vào
                $cleanJson = str_replace(['```json', '```'], '', $rawText);
                $cleanJson = trim($cleanJson);
                
                // Cắt lấy phần nằm giữa { và } đầu tiên và cuối cùng
                $start = strpos($cleanJson, '{');
                $end = strrpos($cleanJson, '}');
                
                if ($start !== false && $end !== false) {
                    $cleanJson = substr($cleanJson, $start, $end - $start + 1);
                }

                $data = json_decode($cleanJson, true);

                // Kiểm tra nếu decode thành công
                if (json_last_error() === JSON_ERROR_NONE && $data) {
                    $feedback = $data['feedback'] ?? $feedback;
                    $actions = $data['actions'] ?? $actions;
                    if (isset($data['notify'])) {
                        $notifyTitle = $data['notify']['title'] ?? $notifyTitle;
                        $notifyMessage = $data['notify']['message'] ?? $notifyMessage;
                    }
                } else {
                    Log::warning("EvaluateAiJob: JSON lỗi từ AI cho NV {$nv->id}. Raw: " . $rawText);
                }
                // ---------------------------------------------
            }

            // 7. Lưu DB
            AiEvaluation::updateOrCreate(
                ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                [
                    'chi_so_kpi' => $kpiValue,
                    'noi_dung_danh_gia' => $feedback,
                    'loai_ket_qua' => $type,
                    'actions' => $actions, // Model sẽ tự cast sang JSON nhờ $casts
                ]
            );

            AiNotification::create([
                'nhanvien_id' => $nv->id,
                'thang' => $thang,
                'nam' => $nam,
                'type' => $notifyTypeFixed,
                'title' => $notifyTitle,
                'message' => $notifyMessage,
                'is_read' => false,
            ]);

        } catch (\Exception $e) {
            Log::error("Lỗi Job AI NV {$nv->id}: " . $e->getMessage());
        }
    }
}