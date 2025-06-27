<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subservice extends Model
{
    protected $fillable = ['service_id', 'image', 'description'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
