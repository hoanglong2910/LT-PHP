<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiEvaluation extends Model
{
    protected $table = 'ai_evaluations';

    protected $fillable = [
        'nhanvien_id',
        'thang',
        'nam',
        'noi_dung_danh_gia',
        'loai_ket_qua'
    ];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'nhanvien_id');
    }
}