<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function classes()
    {
        return $this->hasMany(LensClass::class, 'category_id');
    }
}