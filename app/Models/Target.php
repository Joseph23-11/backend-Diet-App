<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level_aktivitas',
        'target_berat_badan',
        'target_diet',
        'target_waktu_diet',
        'kebutuhan_kalori_harian',
        'target_kalori_harian',
        'total_pengurangan_berat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    protected $casts = [
        'target_diet' => 'string',
    ];
}
