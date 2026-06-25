<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subclass extends Model
{
    protected $fillable = ['class_id', 'name'];

    public function lensClass()
    {
        return $this->belongsTo(LensClass::class, 'class_id');
    }

    public function powers()
    {
        return $this->hasMany(Power::class);
    }
}