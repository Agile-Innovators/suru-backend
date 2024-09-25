<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'website_url',
        'partner_category_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partnerCategory()
    {
        return $this->belongsTo(PartnerCategory::class);
    }

    public function partnerServices()
    {
        return $this->hasMany(PartnerService::class);
    }
}
