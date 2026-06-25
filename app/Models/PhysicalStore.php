<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalStore extends Model
{
    protected $guarded = [];

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }
}
