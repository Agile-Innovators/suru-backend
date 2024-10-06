<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_utilities', 'utility_id', 'property_id');
    }
}
