<?php

namespace App\Models;

use App\Models\Lunch;
use App\Models\Snack;
use App\Models\Dinner;
use App\Models\Breakfast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_makanan',
        'berat_makanan',
        'kalori',
        'protein',
        'lemak',
        'karbohidrat',
        'ukuran'
    ];

    public function breakfasts()
    {
        return $this->hasMany(Breakfast::class);
    }

    public function lunches()
    {
        return $this->hasMany(Lunch::class);
    }

    public function dinners()
    {
        return $this->hasMany(Dinner::class);
    }

    public function snacks()
    {
        return $this->hasMany(Snack::class);
    }
}
