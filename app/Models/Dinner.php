<?php

namespace App\Models;

use App\Models\Food;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_id',
        'porsi_makanan',
        'kalori',
        'protein',
        'lemak',
        'karbohidrat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    protected function serializeDate($date)
    {
        return $date->setTimezone('Asia/Jakarta')->format($this->getDateFormat());
    }
}
