<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['ten_du_an', 'ngay_bat_dau', 'ngay_ket_thuc', 'tien_do', 'trang_thai', 'nhanvien_id'];

    // Liên kết: Một dự án thuộc về một nhân viên quản lý
    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'nhanvien_id');
    }
}