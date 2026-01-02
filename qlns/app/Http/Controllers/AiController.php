<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\AiEvaluation;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AiController extends Controller
{
    public function evaluateAll(Request $request)
    {
        $thang = $request->thang ?? Carbon::now()->month;
        $nam = Carbon::now()->year;
        $nhanviens = NhanVien::all();
        
        $countData = 0; // Biến đếm xem có dữ liệu hay không

        foreach ($nhanviens as $nv) {
            $kpi = Kpi::where('nhanvien_id', $nv->id)->where('thang', $thang)->where('nam', $nam)->first();
            
            // Nếu không có KPI, bỏ qua nhân viên này
            if (!$kpi) continue;

            $countData++; // Có dữ liệu thì tăng biến đếm

            $projects = $nv->projects->pluck('ten_du_an')->implode(', ');
            $prompt = "Đánh giá nhân viên {$nv->hovaten} tháng {$thang}/{$nam}: KPI " . ($kpi->chi_so_kpi) . "%, Dự án: " . ($projects ?: 'Không'). ". Trả về JSON: {\"type\": \"Khen ngợi/Nhắc nhở\", \"feedback\": \"nội dung\"}";

            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . env('GEMINI_API_KEY'), [
                "contents" => [["parts" => [["text" => $prompt]]]]
            ]);

            if ($response->successful()) {
                $result = json_decode(trim(str_replace(['```json', '```'], '', $response->json()['candidates'][0]['content']['parts'][0]['text'])), true);
                AiEvaluation::updateOrCreate(
                    ['nhanvien_id' => $nv->id, 'thang' => $thang, 'nam' => $nam],
                    ['noi_dung_danh_gia' => $result['feedback'], 'loai_ket_qua' => $result['type']]
                );
            }
        }

        // NẾU KHÔNG CÓ DỮ LIỆU THÌ BÁO LỖI
        if ($countData === 0) {
            return back()->with('error', "Không tìm thấy dữ liệu KPI của tháng {$thang}/{$nam}. Vui lòng nhập KPI trước khi chạy AI!");
        }

        return back()->with('success', "AI đã hoàn tất đánh giá cho {$countData} nhân viên!");
    }
}