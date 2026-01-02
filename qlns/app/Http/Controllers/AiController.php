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

            // KPI value (int)
            $kpiValue = (int) $kpi->chi_so_kpi;

            // ✅ CODE tự phân loại (AI không được quyết định type nữa)
            if ($kpiValue >= 80) {
                $type = 'KHEN_NGOI';
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

            // ✅ Prompt: AI chỉ viết feedback + actions, trả JSON KHÔNG có type/tag
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

            // Gọi Ollama local
            $response = Http::timeout(120)->post(rtrim($ollamaUrl, '/') . "/api/generate", [
                "model"  => $ollamaModel,
                "prompt" => $prompt,
                "stream" => false,
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
                $text = $response->json()['response'] ?? '';
                $text = trim(str_replace(['```json', '```'], '', $text));
                $result = json_decode($text, true);

                if (!is_array($result) || !isset($result['feedback']) || !isset($result['actions']) || !is_array($result['actions'])) {
                    throw new \Exception('Ollama trả về không đúng JSON (thiếu feedback/actions)');
                }

                // Giới hạn độ dài cho gọn UI
                $feedback = trim((string) $result['feedback']);
                $feedback = mb_substr($feedback, 0, 240);

                // Lấy đúng 2 actions
                $actions = array_values(array_filter(array_map('trim', $result['actions'])));
                $actions = array_slice($actions, 0, 2);

                // Nếu AI trả thiếu actions thì bổ sung mặc định
                while (count($actions) < 2) {
                    $actions[] = $type === 'KHEN_NGOI' ? 'Duy trì tiến độ hiện tại' : 'Tập trung cải thiện hiệu suất';
                }

                // Lưu: type/tag do CODE quyết định
                AiEvaluation::updateOrCreate(
                    ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                    [
                        'noi_dung_danh_gia' => $feedback,
                        'loai_ket_qua' => $type,
                        // Nếu DB bạn có cột tag/actions thì lưu thêm, còn không thì bỏ 2 dòng dưới:
                        // 'tag' => $tag,
                        // 'actions' => json_encode($actions, JSON_UNESCAPED_UNICODE)
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
