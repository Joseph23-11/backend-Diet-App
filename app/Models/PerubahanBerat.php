<?php

namespace App\Models;

use App\Models\PersonalDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerubahanBerat extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_detail_id',
        'berat_sebelumnya',
        'berat_sekarang',
        'jumlah_pengurangan',
    ];
    
    public function personalDetail()
    {
        return $this->belongsTo(PersonalDetail::class);
    }

    protected function serializeDate($date)
    {
        return $date->setTimezone('Asia/Jakarta')->format($this->getDateFormat());
    }
}
