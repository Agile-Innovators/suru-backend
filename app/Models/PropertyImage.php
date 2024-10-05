<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

class PropertyImage extends Model
{
    use MediaAlly;
    use HasFactory;

    protected $fillable = [
        'property_id',
        'url',
        'public_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
