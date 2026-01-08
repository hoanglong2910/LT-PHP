<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\EvaluateAiJob; // Gọi Job vừa tạo

class AiController extends Controller
{
    public function evaluateAll(Request $request)
    {
        $thang = $request->thang ?? Carbon::now()->month;
        $nam   = Carbon::now()->year;

        // Lấy danh sách nhân viên có KPI tháng này
        $nhanViens = NhanVien::whereHas('kpis', function($q) use ($thang, $nam) {
            $q->where('thang', $thang)->where('nam', $nam);
        })->get();

        if ($nhanViens->isEmpty()) {
            return back()->with('error', "Không có nhân viên nào có KPI tháng $thang/$nam để đánh giá.");
        }

        // Đẩy từng nhân viên vào hàng đợi (Queue) để chạy ngầm
        foreach ($nhanViens as $nv) {
            EvaluateAiJob::dispatch($nv, $thang, $nam);
        }

        return back()->with('success', "Hệ thống đang xử lý ngầm cho " . $nhanViens->count() . " nhân viên. Bạn có thể làm việc khác, kết quả sẽ xuất hiện sau vài phút.");
    }
}