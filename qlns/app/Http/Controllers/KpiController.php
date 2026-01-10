<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\NhanVien; 
use Illuminate\Http\Request;
use Inertia\Inertia;

class KpiController extends Controller
{
    public function index()
    {
        // Fix lỗi PHP6601: Sử dụng trực tiếp tên Class vì đã có 'use' ở trên đầu
        $kpis = Kpi::with('nhanvien')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Kpi/Index', [
            // Fix lỗi PHP6601: Đã simplified đường dẫn App\Models\NhanVien
            'nhanvien' => NhanVien::all()->map(function ($nv) {
                return [
                    'id' => $nv->id,
                    'ten' => $nv->hovaten, 
                ];
            }),
            'kpis' => $kpis,
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

        // SỬA LỖI TẠI ĐÂY:
        // Thay vì dùng Kpi::create() luôn tạo mới, ta dùng updateOrCreate()
        Kpi::updateOrCreate(
            [
                // Điều kiện tìm kiếm: 
                // Nếu tìm thấy bản ghi trùng Nhân viên + Tháng + Năm này...
                'nhanvien_id' => $request->nhanvien_id,
                'thang'       => $request->thang,
                'nam'         => $request->nam,
            ],
            [
                // ... thì cập nhật các thông tin này (hoặc tạo mới với các thông tin này)
                'chi_so_kpi' => $request->chi_so_kpi,
                'ghi_chu'    => $request->ghi_chu, // Cập nhật cả ghi chú nếu có
            ]
        );

        return redirect()->back()->with('success', 'Đã lưu chỉ số KPI thành công!');
    }
}