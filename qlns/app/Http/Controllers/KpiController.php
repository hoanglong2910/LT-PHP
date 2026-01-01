<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\NhanVien; // Phải có dòng này để lấy dữ liệu nhân viên
use Illuminate\Http\Request;
use Inertia\Inertia;

class KpiController extends Controller
{
    public function index()
    {
        return Inertia::render('Kpi/Index', [
        'nhanviens' => \App\Models\NhanVien::all()->map(function ($nv) {
            return [
                'id' => $nv->id,
                'ten' => $nv->hovaten, 
            ];
        }),
        'kpis' => \App\Models\Kpi::with('nhanvien')->orderBy('created_at', 'desc')->get(),
    ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nhanvien_id' => 'required',
            'chi_so_kpi' => 'required|numeric|min:0|max:100',
            'thang' => 'required|integer|between:1,12',
            'nam' => 'required|integer',
        ]);

        Kpi::create($request->all());

        return redirect()->back()->with('success', 'Đã lưu chỉ số KPI thành công!');
    }
}