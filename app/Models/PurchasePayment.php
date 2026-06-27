<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'purchase_id', 'amount', 'payment_mode', 'notes', 'paid_by'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}