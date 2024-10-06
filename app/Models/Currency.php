<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['code','name'];
    protected $hidden = ['created_at', 'updated_at'];

    public function Properties(){
        return $this->hasMany(Property::class);
    }
}
