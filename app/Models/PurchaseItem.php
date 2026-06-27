<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'power_id',
        'quantity', 'returned_quantity', 'unit_price', 'total_price', 'discount'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function power()
    {
        return $this->belongsTo(Power::class);
    }

    public function returnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    protected function returnableQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity - $this->returned_quantity,
        );
    }
}