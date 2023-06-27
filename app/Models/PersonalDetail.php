<?php

namespace App\Models;

use App\Models\User;
use App\Models\PerubahanBerat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_kelamin',
        'berat_badan',
        'tinggi_badan',
        'usia',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'jenis_kelamin' => 'string',
    ];

    public function perubahabBerat()
    {
        return $this->hasMany(PerubahanBerat::class);
    }
}
