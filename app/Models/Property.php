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
        'user_id',
    ];

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

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function utilities()
    {
        return $this->belongsToMany(PropertyUtility::class, 'properties_utilities_assignment');
    }
}