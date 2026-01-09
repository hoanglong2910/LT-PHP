<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\EvaluateAiJob;

class AiController extends Controller
{
    public function evaluateAll(Request $request)
    {
        $thang = $request->thang ?? Carbon::now()->month;
        $nam   = Carbon::now()->year;

        $nhanViens = NhanVien::whereHas('kpis', function($q) use ($thang, $nam) {
            $q->where('thang', $thang)->where('nam', $nam);
        })
        ->with(['projects', 'nhanluong' => function($q) use ($thang, $nam) {
            $q->where('thang', $thang)->where('nam', $nam);
        }])
        ->get();

        // Sử dụng Ternary Operator để tính điểm ưu tiên (Không dùng If)
        $sortedNhanViens = $nhanViens->sortByDesc(fn($nv) => 
            ($nv->projects->isNotEmpty() ? 10 : 0) + 
            ($nv->nhanluong->isNotEmpty() ? 5 : 0)
        );

        $nhanViens->isNotEmpty() && $sortedNhanViens->each(fn($nv) => EvaluateAiJob::dispatch($nv, $thang, $nam));

        return back()->with(
            $nhanViens->isEmpty() ? 'error' : 'success', 
            $nhanViens->isEmpty() 
                ? "Không có nhân viên nào có KPI tháng $thang/$nam." 
                : "Đang xử lý đánh giá cho " . $nhanViens->count() . " nhân viên (Ưu tiên đủ dữ liệu)."
        );
    }
}