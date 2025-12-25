<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhongBan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'phongban';

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function mucluong()
    {
        return $this->hasMany(MucLuong::class, 'id', 'phongban_id');
    }

    public function phucaps()
    {
    return $this->hasMany(PhuCap::class, 'phongban_id', 'id');
    }
    public function nhanViensQuaPhuCap() 
    {
        return $this->hasManyThrough(NhanVien::class, PhuCap::class, 'phongban_id', 'phucap_id', 'id', 'id');
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->Where('tenpb', 'like', '%'.$search.'%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
    
}
