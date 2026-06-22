<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_no', 'customer_id', 'invoice_date',
        'gross_total', 'discount', 'tax', 'net_total',
        'paid', 'balance', 'payment_mode', 'price_type', 'memo'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}