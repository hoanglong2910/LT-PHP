<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Projects/Index', [
            'projects' => Project::with('nhanvien')->orderBy('created_at', 'desc')->get(),
            'nhanviens' => NhanVien::all()->map(function ($nv) {
                return [
                    'id' => $nv->id,
                    'ten' => $nv->hovaten,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ten_du_an' => 'required|string|max:255',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'nullable|date',
            'nhanvien_id' => 'required|exists:nhanvien,id',
            'tien_do' => 'required|integer|min:0|max:100',
            'trang_thai' => 'required|string',
        ]);

        Project::create($data);

        return redirect()->back()->with('success', 'Tạo dự án mới thành công!');
    }
}