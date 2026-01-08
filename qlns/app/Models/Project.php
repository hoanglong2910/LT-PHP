<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // Phải có 'ngay_ket_thuc' ở đây thì mới lưu được vào database
    protected $fillable = [
        'ten_du_an', 
        'ngay_bat_dau', 
        'ngay_ket_thuc', 
        'tien_do', 
        'trang_thai', 
        'nhanvien_id'
    ];

    public function nhanvien()
    {
        // Sử dụng withDefault để tránh lỗi web khi nhân viên bị xóa
        return $this->belongsTo(NhanVien::class, 'nhanvien_id')->withDefault([
            'hovaten' => 'N/A'
        ]);
    }
}