<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function partnerProfiles()
    {
        return $this->hasMany(PartnerProfile::class);
    }
}
