<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'deposit_price',
        'availability_date',
        'size_in_m2',
        'bedrooms',
        'bathrooms',
        'floors',
        'garages',
        'pools',
        'pets_allowed',
        'green_area',
        'property_category_id',
        'property_transaction_type_id',
        'city_id',
        'currency_id',
        'user_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyImages()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function propertyTransactionType()
    {
        return $this->belongsTo(PropertyTransactionType::class);
    }

    public function propertyPaymentFrequency()
    {
        return $this->belongsTo(PaymentFrequency::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function utilities()
    {
        return $this->belongsToMany(Utility::class, 'property_utilities', 'property_id', 'utility_id');
    }
}
