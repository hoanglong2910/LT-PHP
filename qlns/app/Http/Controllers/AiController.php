<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\EvaluateAiJob; // Đã sửa tên class đúng (bỏ s)

class AiController extends Controller
{
    public function evaluateAll(Request $request)
    {
        $thang = $request->thang ?? Carbon::now()->month;
        $nam   = Carbon::now()->year;

        // Tìm nhân viên có KPI trong tháng
        $nhanViens = NhanVien::whereHas('kpis', function($q) use ($thang, $nam) {
            $q->where('thang', $thang)->where('nam', $nam);
        })->get();

        if ($nhanViens->isEmpty()) {
            return back()->with('error', "Không có nhân viên nào có KPI tháng $thang/$nam để đánh giá.");
        }

        // Đẩy vào hàng đợi xử lý ngầm
        foreach ($nhanViens as $nv) {
            EvaluateAiJob::dispatch($nv, $thang, $nam);
        }

        return back()->with('success', "Hệ thống đang xử lý AI đánh giá cho " . $nhanViens->count() . " nhân viên.");
    }
}