<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'phone', 'address', 'city',
        'opening_balance', 'current_balance',
        'is_active', 'notes'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}