<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Define the table associated with the model
    protected $table = 'products';

    // Define the fillable attributes
    protected $fillable = [
        'name',
        'price',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

     public function galleries()
    {
        return $this->hasMany(Gallery::class, 'product_id');
    }
}
