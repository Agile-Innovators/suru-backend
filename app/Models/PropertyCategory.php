<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
