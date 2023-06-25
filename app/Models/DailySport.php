<?php

namespace App\Models;

use App\Models\User;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailySport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sport_id',
        'durasi',
        'kalori',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    protected function serializeDate($date)
    {
        return $date->setTimezone('Asia/Jakarta')->format($this->getDateFormat());
    }
}
