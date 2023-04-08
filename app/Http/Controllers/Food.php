<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_makanan',
        'berat_makanan',
        'kalori',
        'karbohidrat',
        'protein',
        'lemak',
    ];
}
