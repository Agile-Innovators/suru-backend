<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyUtility extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'utility_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function utility()
    {
        return $this->belongsTo(Utility::class);
    }
}
