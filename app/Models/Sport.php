<?php

namespace App\Models;

use App\Models\DailySport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_olahraga',
        'berat',
        'kalori',
    ];

    public function dailySport()
    {
        return $this->hasMany(DailySport::class);
    }
}