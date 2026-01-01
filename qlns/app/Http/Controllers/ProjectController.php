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
            'nhanvien' => NhanVien::all()->map(function ($nv) {
                return [
                    'id' => $nv->id,
                    'ten' => $nv->hovaten,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role < 1) {
            return redirect()->back()->with('error', 'Bạn không có quyền tạo dự án!');
        }
        $data = $request->validate([
            'ten_du_an' => 'required|string|max:255',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'nullable|date',
            'nhanvien_id' => 'required|exists:nhanvien,id',
            'tien_do' => 'required|integer|min:0|max:100',
            'trang_thai' => 'required|string',
        ]);

        \App\Models\Project::create($data);

        return redirect()->back()->with('success', 'Tạo dự án mới thành công!');
    }
    public function update(Request $request, Project $project)
    {
        // Kiểm tra quyền: Chỉ Admin (2) và Quản lý (1) mới được sửa
        if (auth()->user()->role < 1) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật tiến độ!');
        }

        $data = $request->validate([
            'tien_do' => 'required|integer|min:0|max:100',
            'trang_thai' => 'required|string',
        ]);

        $project->update($data);

        return redirect()->back()->with('success', 'Cập nhật tiến độ thành công!');
    }
}
