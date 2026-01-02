<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiEvaluation extends Model
{
    use HasFactory;

    protected $table = 'ai_evaluations';

    // Khai báo các cột được phép lưu
    protected $fillable = [
        'nhanvien_id',
        'thang',
        'nam',
        'chi_so_kpi',       // <--- Đừng quên cột này
        'noi_dung_danh_gia',
        'loai_ket_qua',
        'actions',          // <--- Đừng quên cột này
    ];

    // QUAN TRỌNG NHẤT: Tự động chuyển JSON trong Database thành Array khi lấy ra
    protected $casts = [
        'actions' => 'array', 
    ];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'nhanvien_id');
    }
}