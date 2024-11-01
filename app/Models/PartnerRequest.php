<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerRequest extends Model
{
    use HasFactory;

    protected $table = 'partners_requests';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'image_public_id',
        'description',
        'website_url',
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'city_id',
        'address',
        'currency_id',
        'partner_category_id',
        'partner_comments',
        'admin_id',
        'reviewd_at',
        'status',
        'admin_comments',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function partnerCategory()
    {
        return $this->belongsTo(PartnerCategory::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}