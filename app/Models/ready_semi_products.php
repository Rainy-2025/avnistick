<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ready_semi_products extends Model
{

    protected $fillable = [
        'name',
        'type', // readymade or semi_customizable
        'category_id',
        'subcategory_id',
        'short_description',
        'long_description',
        'images',
        'sizes',
        'attributes',
        'properties',
    ];

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'attributes' => 'array',
    ];

     public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}

