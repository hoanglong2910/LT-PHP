<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\AiEvaluation;
use App\Models\Kpi;
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

        // Bắt object { ... } theo kiểu "balanced braces" đơn giản
        $start = strpos($text, '{');
        if ($start === false) return null;

        $level = 0;
        $inString = false;
        $escape = false;

        for ($i = $start; $i < strlen($text); $i++) {
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
            } else {
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
        }

        return null;
    }

    /**
     * Fallback: nếu AI không trả JSON chuẩn, cố gắng lấy 2 actions từ text
     */
    private function extractTwoActionsFromText(string $text): array
    {
        $text = trim($text);
        $lines = preg_split("/\r\n|\n|\r/", $text);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        $actions = [];

        foreach ($lines as $ln) {
            // loại prefix kiểu: "- ", "• ", "1. ", "1) "
            $ln = preg_replace('/^(\-|\•)\s*/u', '', $ln);
            $ln = preg_replace('/^\d+[\.\)]\s*/u', '', $ln);
            $ln = trim($ln);

            if ($ln !== '') $actions[] = $ln;
            if (count($actions) >= 2) break;
        }

        return array_slice($actions, 0, 2);
    }

    public function evaluateAll(Request $request)
    {
        $thang = $request->thang ?? Carbon::now()->month;
        $nam   = Carbon::now()->year;

        $nhanviens = NhanVien::all();

        $countHasKpi = 0;
        $countSaved  = 0;
        $countFailed = 0;

        $ollamaUrl   = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $ollamaModel = env('OLLAMA_MODEL', 'phi3');

        foreach ($nhanviens as $nv) {

            $kpi = Kpi::where('nhanvien_id', $nv->id)
                ->where('thang', $thang)
                ->where('nam', $nam)
                ->first();

            if (!$kpi) continue;
            $countHasKpi++;

            $kpiValue = (int) $kpi->chi_so_kpi;

            // ✅ CODE tự phân loại (AI không được quyết định type/tag)
            if ($kpiValue >= 80) {
                $type = 'KHEN_THUONG';
                $tag  = 'KPI_CAO';
            } elseif ($kpiValue >= 60) {
                $type = 'NHAC_NHO';
                $tag  = 'KPI_TB';
            } else {
                $type = 'NHAC_NHO';
                $tag  = 'KPI_THAP';
            }

            // Lấy danh sách dự án (nếu relation lỗi thì coi như không)
            $projects = '';
            try {
                $projects = $nv->projects->pluck('ten_du_an')->implode(', ');
            } catch (\Throwable $e) {
                $projects = '';
            }

            $prompt =
                "Bạn là trợ lý HR. Chỉ dùng đúng dữ liệu sau, KHÔNG bịa thêm.\n" .
                "- Tên: {$nv->hovaten}\n" .
                "- Tháng/Năm: {$thang}/{$nam}\n" .
                "- KPI: {$kpiValue}%\n" .
                "- Dự án: " . ($projects ?: 'Không') . "\n\n" .
                "Hãy trả về DUY NHẤT 1 JSON hợp lệ, không thêm chữ khác.\n" .
                "Schema:\n" .
                "{\"feedback\":\"...\",\"actions\":[\"...\",\"...\"]}\n\n" .
                "Quy tắc viết:\n" .
                "- feedback: đúng 2 câu, tiếng Việt tự nhiên, lịch sự, bám KPI và dự án.\n" .
                "- actions: đúng 2 gợi ý ngắn (tối đa 8 từ/gợi ý).\n";
                "- feedback: BẮT BUỘC là string, KHÔNG dùng mảng [].\n" .


            $response = Http::timeout(120)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,

                // ✅ ép JSON nếu Ollama/model hỗ trợ
                "format" => "json",

                "options" => [
                    "temperature" => 0.2,
                    "top_p" => 0.9
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
                $rawText = $response->json()['response'] ?? '';
                $jsonStr = $this->extractJsonObject((string)$rawText);

                $result = null;
                if ($jsonStr) {
                    $result = json_decode($jsonStr, true);
                }

                // Nếu decode fail => fallback
                if (!is_array($result)) {
                    // fallback cố lấy feedback/actions từ text
                    $fallbackActions = $this->extractTwoActionsFromText((string)$rawText);
                    $result = [
                        'feedback' => (string)$rawText,
                        'actions' => $fallbackActions,
                    ];
                }

                // Chuẩn hoá actions
                // Chuẩn hoá feedback (AI đôi khi trả feedback dạng array)
                $feedbackVal = $result['feedback'] ?? '';

                if (is_array($feedbackVal)) {
                    // Ghép các câu lại thành 1 chuỗi
                    $feedbackVal = implode(' ', array_map(function ($x) {
                        return trim((string)$x);
                    }, $feedbackVal));
                } elseif (is_object($feedbackVal)) {
                    $feedbackVal = json_encode($feedbackVal, JSON_UNESCAPED_UNICODE);
                } else {
                    $feedbackVal = (string)$feedbackVal;
                }

                $feedback = trim($feedbackVal);

                if ($feedback === '') {
                    throw new \Exception('AI thiếu feedback');
                }
                $feedback = mb_substr($feedback, 0, 240);


                $actions = $result['actions'] ?? [];
                if (!is_array($actions)) $actions = [];
                $actions = array_values(array_filter(array_map('trim', $actions)));
                $actions = array_slice($actions, 0, 2);

                // Nếu AI trả thiếu actions thì bổ sung mặc định
                while (count($actions) < 2) {
                    $actions[] = $type === 'KHEN_THUONG' ? 'Duy trì tiến độ hiện tại' : 'Tập trung cải thiện hiệu suất';
                }

                // ✅ LƯU (actions là array -> model cast sẽ tự json_encode)
                AiEvaluation::updateOrCreate(
                    ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                    [
                        'chi_so_kpi' => $kpiValue,
                        'noi_dung_danh_gia' => $feedback,
                        'loai_ket_qua' => $type,
                        'actions' => $actions,

                        // nếu DB có cột tag thì mở dòng này:
                        // 'tag' => $tag,
                    ]
                );

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

        if ($countHasKpi === 0) {
            return back()->with('error', "Không tìm thấy KPI tháng {$thang}/{$nam}. Vui lòng nhập KPI trước khi chạy AI!");
        }

        if ($countSaved === 0) {
            return back()->with('error', "Có {$countHasKpi} nhân viên có KPI nhưng AI không lưu được. Xem storage/logs/laravel.log để biết lỗi.");
        }

        return back()->with('success', "AI đã lưu kết quả cho {$countSaved}/{$countHasKpi} nhân viên (thất bại {$countFailed}).");
    }
}
