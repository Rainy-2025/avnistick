<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    protected $fillable = ['image', 'description'];

    public function subservices()
    {
        return $this->hasMany(Subservice::class);
    }
    
}
