<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'invoice_no', 'supplier_id', 'invoice_date',
        'gross_total', 'discount', 'tax', 'net_total',
        'paid', 'balance', 'payment_mode', 'status', 'memo'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}