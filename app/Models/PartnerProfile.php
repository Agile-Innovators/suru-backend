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
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'currency_id',
        'partner_category_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

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
