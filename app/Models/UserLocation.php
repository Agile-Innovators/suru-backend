<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'address',
    ];

    public function partnerProfile()
    {
        return $this->belongsTo(PartnerProfile::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
