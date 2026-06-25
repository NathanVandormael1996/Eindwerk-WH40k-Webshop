<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'attributes' => 'array',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getStockAttribute()
    {
        return $this->productStocks()
            ->whereHas('physicalStore', function ($query) {
                $query->where('is_central_warehouse', true);
            })->sum('quantity');
    }

    public function getTotalStockAttribute()
    {
        return $this->productStocks()->sum('quantity');
    }
}
