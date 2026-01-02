<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiNotification extends Model
{
    protected $table = 'ai_notifications';

    protected $fillable = [
        'nhanvien_id', 'thang', 'nam', 'type', 'title', 'message', 'is_read'
    ];
}
