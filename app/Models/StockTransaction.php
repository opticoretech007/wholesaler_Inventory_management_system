<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = ['product_id', 'power_id', 'type', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function power()
    {
        return $this->belongsTo(Power::class);
    }
}