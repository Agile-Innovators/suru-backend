<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerService extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'price',
        'price_max',
        'business_service_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

    public function partnerProfile()
    {
        return $this->belongsTo(PartnerProfile::class);
    }

    public function businessService()
    {
        return $this->belongsTo(BusinessService::class);
    }
}
