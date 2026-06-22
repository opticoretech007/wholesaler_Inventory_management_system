<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'invoice_no', 'supplier_id', 'invoice_date',
        'gross_total', 'discount', 'tax', 'net_total',
        'paid', 'balance', 'payment_mode', 'memo'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}