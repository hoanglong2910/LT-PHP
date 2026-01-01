<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    // Khai báo các cột mà bạn cho phép nhập dữ liệu vào
    protected $fillable = [
        'nhanvien_id',
        'thang',
        'nam',
        'chi_so_kpi',
        'ghi_chu'
    ];

    // Định nghĩa mối quan hệ: 1 dòng KPI thuộc về 1 nhân viên
    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'nhanvien_id');
    }
}