<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'phone', 'address', 'city',
        'opening_balance', 'current_balance',
        'is_active', 'notes'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}