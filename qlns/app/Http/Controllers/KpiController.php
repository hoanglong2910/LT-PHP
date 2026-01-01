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
        $kpis = \App\Models\Kpi::with('nhanvien')->orderBy('created_at', 'desc')->get();

    return Inertia::render('Kpi/Index', [
        'nhanvien' => \App\Models\NhanVien::all()->map(function ($nv) {
            return [
                'id' => $nv->id,
                'ten' => $nv->hovaten, 
            ];
        }),
        'kpis' => $kpis,
        // Thêm phần lọc danh sách trực tiếp từ server
        'kpiThap' => $kpis->where('chi_so_kpi', '<', 50)->values(),
        'kpiCao' => $kpis->where('chi_so_kpi', '>=', 80)->values(),
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