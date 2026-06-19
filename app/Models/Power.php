<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Power extends Model
{
    protected $fillable = ['sph', 'cyl', 'category'];

    public function getLabel(): string
    {
        return $this->cyl ? $this->sph . '/' . $this->cyl : $this->sph;
    }
}