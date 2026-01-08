<?php

namespace App\Http\Controllers;
use App\Models\ChamCong;
use App\Models\ThuongPhat;
use App\Models\NhanVien;
use App\Models\AiEvaluation;
use App\Models\Kpi;
use App\Models\NhanLuong;
use App\Models\AiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiController extends Controller
{
    /**
     * Cắt JSON object đầu tiên trong 1 chuỗi (phòng AI trả thêm chữ ngoài JSON)
     */
    private function extractJsonObject(string $text): ?string
    {
        $text = trim($text);
        $text = str_replace(["```json", "```"], "", $text);
        $text = trim($text);

        $start = strpos($text, '{');
        if ($start === false) return null;

        $level = 0;
        $inString = false;
        $escape = false;

        $len = strlen($text);
        for ($i = $start; $i < $len; $i++) {
            $ch = $text[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                } elseif ($ch === '\\') {
                    $escape = true;
                } elseif ($ch === '"') {
                    $inString = false;
                }
                continue;
            }

            if ($ch === '"') {
                $inString = true;
                continue;
            }

            if ($ch === '{') $level++;
            if ($ch === '}') {
                $level--;
                if ($level === 0) {
                    return substr($text, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private function normalizeFeedback($feedback): string
    {
        if (is_array($feedback)) {
            $feedback = implode(' ', array_map(fn($x) => trim((string)$x), $feedback));
        } elseif (is_object($feedback)) {
            $feedback = json_encode($feedback, JSON_UNESCAPED_UNICODE);
        } else {
            $feedback = (string)$feedback;
        }

        $feedback = trim((string)$feedback);
        $feedback = preg_replace('/\s+/', ' ', $feedback);
        return mb_substr($feedback, 0, 300);
    }

    private function normalizeActions($actions): array
    {
        $arr = is_array($actions) ? $actions : [];
        $arr = array_values(array_filter(array_map(fn($x) => trim((string)$x), $arr)));

        // pad đủ 2 action (không if-else)
        $defaults = ['Báo cáo tiến độ mỗi tuần', 'Đúng deadline công việc'];
        $arr = array_slice(array_merge($arr, $defaults), 0, 2);

        // giới hạn mỗi action cho đẹp UI
        return array_map(fn($x) => mb_substr($x, 0, 80), $arr);
    }

    public function evaluateAll(Request $request)
    {
        // ✅ chống timeout PHP khi chạy nhiều nhân viên
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        $thang = $request->thang ?? Carbon::now()->month;
        $nam   = Carbon::now()->year;

        $countHasKpi = 0;
        $countSaved  = 0;
        $countFailed = 0;

        $ollamaUrl   = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $ollamaModel = env('OLLAMA_MODEL', 'phi3');

        // Chạy theo chunk để đỡ nặng
        NhanVien::chunk(20, function ($nhanviens) use (
            $thang, $nam, $ollamaUrl, $ollamaModel,
            &$countHasKpi, &$countSaved, &$countFailed
        ) {
            foreach ($nhanviens as $nv) {

                // 1) KPI
                $kpi = Kpi::where('nhanvien_id', $nv->id)
                    ->where('thang', $thang)
                    ->where('nam', $nam)
                    ->first();

                if (!$kpi) continue;
                $countHasKpi++;

                $kpiValue = (float) ($kpi->chi_so_kpi ?? 0);

                // 2) Lương / chấm công (nếu có)
                $luong = NhanLuong::where('nhanvien_id', $nv->id)
                    ->where('thang', $thang)
                    ->where('nam', $nam)
                    ->first();

                $ngayCong      = (int) data_get($luong, 'ngaycong', 0);
                $ngayChuan     = (int) data_get($luong, 'ngaycongchuan', 0);
                $nghiCoPhep    = (int) data_get($luong, 'nghihl', 0);
                $nghiKhongPhep = (int) data_get($luong, 'nghikhl', 0);
                $thuclinh      = (int) data_get($luong, 'thuclinh', 0);
                $phat          = (int) data_get($luong, 'phat', 0);

                // ✅ “làm mượt” để không có dữ liệu thì không bị 0%
                $attendanceRate = round((($ngayCong + 1) / ($ngayChuan + 1)) * 100, 1);

                // 3) Dự án (nếu relation có)
                $projectData = [];
                try {
                    $projectData = $nv->projects()
                        ->select(['ten_du_an', 'tien_do', 'trang_thai', 'ngay_ket_thuc'])
                        ->get()
                        ->map(function ($p) {
                            return [
                                'ten_du_an'     => (string) data_get($p, 'ten_du_an', ''),
                                'tien_do'       => (float) data_get($p, 'tien_do', 0),
                                'trang_thai'    => (string) data_get($p, 'trang_thai', ''),
                                'ngay_ket_thuc' => (string) data_get($p, 'ngay_ket_thuc', ''),
                            ];
                        })
                        ->values()
                        ->all();
                } catch (\Throwable $e) {
                    $projectData = [];
                }

                // ====== 4) HỆ THỐNG TỰ TÍNH ĐIỂM & TỰ QUYẾT ĐỊNH KHEN/NHẮC (KHÔNG PHỤ THUỘC AI) ======
                $statusMap = [
                    'hoàn thành' => 100, 'hoan thanh' => 100, 'done' => 100, 'completed' => 100,
                    'đang làm' => 70, 'dang lam' => 70, 'in progress' => 70, 'processing' => 70,
                    'trễ' => 40, 'tre' => 40, 'overdue' => 40, 'late' => 40,
                    'tạm dừng' => 50, 'tam dung' => 50, 'paused' => 50,
                    'chưa bắt đầu' => 60, 'chua bat dau' => 60, 'not started' => 60,
                ];

                $projCount = count($projectData);
                $hasProj = (int)($projCount > 0);

                $projScores = array_map(function ($p) use ($statusMap) {
                    $tienDo = (float)($p['tien_do'] ?? 0);
                    $st = mb_strtolower(trim((string)($p['trang_thai'] ?? '')));
                    $statusScore = $statusMap[$st] ?? 70; // không rõ trạng thái => trung tính
                    return 0.7 * $tienDo + 0.3 * $statusScore;
                }, $projectData);

                $avgProj = array_sum($projScores) / max(1, $projCount);

                // Không có dự án => project_score trung tính 70 (không phạt vì thiếu dữ liệu)
                $projectScore = $hasProj * $avgProj + (1 - $hasProj) * 70;

                $totalScore = round(0.4 * $kpiValue + 0.25 * $attendanceRate + 0.35 * $projectScore, 1);

                // Không if-else: dùng index mảng 0/1
                $idx = (int)($totalScore >= 70);
                $type = ['NHAC_NHO', 'KHEN_THUONG'][$idx];
                $notifyTypeFixed = ['REMINDER', 'PRAISE'][$idx];

                // ====== 5) Prompt: AI CHỈ VIẾT LỜI, KHÔNG ĐƯỢC QUYẾT ĐỊNH KẾT QUẢ ======
                $prompt =
                    "Bạn là trợ lý HR viết nhận xét hàng tháng.\n" .
                    "CHỈ dùng dữ liệu được cung cấp, KHÔNG bịa thêm.\n" .
                    "BẮT BUỘC trả về DUY NHẤT 1 JSON hợp lệ, không markdown, không giải thích.\n\n" .

                    "Dữ liệu:\n" .
                    "- Tên: {$nv->hovaten}\n" .
                    "- Tháng/Năm: {$thang}/{$nam}\n" .
                    "- KPI: {$kpiValue}%\n" .
                    "- Chấm công: {$ngayCong}/{$ngayChuan} (tỷ lệ {$attendanceRate}%)\n" .
                    "- Nghỉ có phép: {$nghiCoPhep}, nghỉ không phép: {$nghiKhongPhep}\n" .
                    "- Thực lĩnh: {$thuclinh}, Phạt: {$phat}\n" .
                    "- Dự án (JSON): " . json_encode($projectData, JSON_UNESCAPED_UNICODE) . "\n\n" .

                    "Kết quả hệ thống (KHÔNG được thay đổi):\n" .
                    "- total_score: {$totalScore}\n" .
                    "- loai_ket_qua: {$type}\n" .
                    "- notify.type: {$notifyTypeFixed}\n\n" .

                    "Schema JSON bắt buộc:\n" .
                    "{\"feedback\":\"(đúng 2 câu)\",\"actions\":[\"...\",\"...\"],\"notify\":{\"type\":\"PRAISE|REMINDER\",\"title\":\"...\",\"message\":\"...\"}}\n\n" .

                    "Ràng buộc:\n" .
                    "- feedback: đúng 2 câu tiếng Việt tự nhiên, lịch sự, bám số liệu.\n" .
                    "- actions: đúng 2 gợi ý, <= 8 từ/gợi ý.\n" .
                    "- notify.type phải đúng y chang: {$notifyTypeFixed}\n";

                // 6) Gọi Ollama (Laravel cũ: dùng withOptions thay connectTimeout)
                $response = Http::withOptions([
                        'connect_timeout' => 30,
                    ])
                    ->timeout(300)
                    ->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                        "model"  => $ollamaModel,
                        "prompt" => $prompt,
                        "stream" => false,
                        "format" => "json",
                        "options" => [
                            "temperature" => 0.2,
                            "top_p" => 0.9,
                        ]
                    ]);

                if (!$response->successful()) {
                    $countFailed++;
                    Log::error('Ollama API failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'nhanvien_id' => $nv->id,
                    ]);
                    continue;
                }

                try {
                    $rawText = (string) data_get($response->json(), 'response', '');
                    $rawText = trim($rawText);

                    $result = null;

                    // thử decode trực tiếp
                    try {
                        $result = json_decode($rawText, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\Throwable $e) {
                        $jsonStr = $this->extractJsonObject($rawText);
                        if ($jsonStr) {
                            $result = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);
                        }
                    }

                    // ====== Fallback an toàn (không lưu văn rác) ======
                    $fallbackFeedback = [
                        "Tháng {$thang}/{$nam}, KPI {$kpiValue}% và điểm tổng {$totalScore}. Bạn cần cải thiện tính ổn định và bám sát kế hoạch tháng tới.",
                        "Tháng {$thang}/{$nam}, KPI {$kpiValue}% và điểm tổng {$totalScore}. Bạn đang làm tốt, hãy duy trì nhịp độ và chất lượng công việc."
                    ][$idx];

                    $fallbackActions = [
                        ['Ưu tiên việc quan trọng', 'Cập nhật tiến độ hằng tuần'],
                        ['Duy trì hiệu suất hiện tại', 'Chia sẻ cách làm cho nhóm']
                    ][$idx];

                    $feedback = $fallbackFeedback;
                    $actions = $fallbackActions;
                    $notifyTitle = $type === 'KHEN_THUONG' ? 'Khen thưởng tháng' : 'Nhắc nhở tháng';
                    $notifyMessage = $fallbackFeedback;

                    if (is_array($result)) {
                        $feedback = $this->normalizeFeedback(data_get($result, 'feedback', $fallbackFeedback));
                        $actions = $this->normalizeActions(data_get($result, 'actions', $fallbackActions));

                        $notifyTitle = trim((string) data_get($result, 'notify.title', $notifyTitle));
                        $notifyTitle = mb_substr($notifyTitle ?: ($type === 'KHEN_THUONG' ? 'Khen thưởng tháng' : 'Nhắc nhở tháng'), 0, 150);

                        // ép notify.type theo hệ thống (AI không được đổi)
                        $notifyMessage = trim((string) data_get($result, 'notify.message', $notifyMessage));
                        $notifyMessage = mb_substr($notifyMessage ?: $feedback, 0, 500);
                    }

                    // 7) Lưu ai_evaluations (loai_ket_qua do HỆ THỐNG quyết định)
                    AiEvaluation::updateOrCreate(
                        ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                        [
                            'chi_so_kpi' => (float) $kpiValue,
                            'noi_dung_danh_gia' => mb_substr($feedback, 0, 240),
                            'loai_ket_qua' => $type, // KHEN_THUONG | NHAC_NHO
                            'actions' => $actions,
                        ]
                    );

                    // 8) Lưu ai_notifications
                    AiNotification::create([
                        'nhanvien_id' => $nv->id,
                        'thang' => $thang,
                        'nam' => $nam,
                        'type' => $notifyTypeFixed, // PRAISE | REMINDER (do hệ thống cố định)
                        'title' => $notifyTitle,
                        'message' => $notifyMessage,
                        'is_read' => false,
                    ]);

                    $countSaved++;
                } catch (\Throwable $e) {
                    $countFailed++;
                    Log::error('Parse/Save AI failed', [
                        'error' => $e->getMessage(),
                        'nhanvien_id' => $nv->id,
                        'raw' => $response->body(),
                    ]);
                }
            }
        });

        if ($countHasKpi === 0) {
            return back()->with('error', "Không tìm thấy KPI tháng {$thang}/{$nam}. Vui lòng nhập KPI trước khi chạy AI!");
        }

        if ($countSaved === 0) {
            return back()->with('error', "Có {$countHasKpi} nhân viên có KPI nhưng AI không lưu được. Xem storage/logs/laravel.log để biết lỗi.");
        }

        return back()->with('success', "AI đã lưu kết quả cho {$countSaved}/{$countHasKpi} nhân viên (thất bại {$countFailed}).");
    }
}
