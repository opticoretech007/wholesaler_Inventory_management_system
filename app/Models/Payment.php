<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['payable_type', 'payable_id', 'amount', 'method', 'notes', 'date'];

    public function payable()
    {
        return $this->morphTo();
    }
}
