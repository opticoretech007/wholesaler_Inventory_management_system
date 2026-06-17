<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Power extends Model
{
    protected $fillable = ['sph', 'cyl'];

    // Display helper: "-1.00/-0.50" or just "-1.00"
    public function getLabel(): string
    {
        return $this->cyl ? $this->sph . '/' . $this->cyl : $this->sph;
    }
}