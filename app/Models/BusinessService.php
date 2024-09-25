<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function partnerServices()
    {
        return $this->hasMany(PartnerService::class);
    }
}