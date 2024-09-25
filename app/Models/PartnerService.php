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

    public function partnerProfile()
    {
        return $this->belongsTo(PartnerProfile::class);
    }

    public function businessService()
    {
        return $this->belongsTo(BusinessService::class);
    }
}
