<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Power extends Model
{
    protected $fillable = ['sph', 'cyl', 'category', 'subclass_id'];

    public function getLabel(): string
    {
        return $this->cyl ? $this->sph . '/' . $this->cyl : $this->sph;
    }

    public function subclass()
    {
        return $this->belongsTo(Subclass::class);
    }
}