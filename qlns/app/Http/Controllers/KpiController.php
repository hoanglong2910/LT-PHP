<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KpiController extends Controller
{
    // Hàm này để hiển thị trang nhập KPI
    public function index()
    {
        return Inertia::render('Kpi/Index', [
            'nhanviens' => NhanVien::all()->map(function ($nv) {
                return [
                    'id' => $nv->id,
                    'ten' => $nv->ten_nhanvien, // Tên cột này phải khớp với bảng nhanviens của bạn
                ];
            }),
            'kpis' => Kpi::with('nhanvien')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    // Hàm này để lưu dữ liệu khi nhấn nút "Lưu"
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