<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LensClass extends Model
{
    protected $table = 'classes';
    protected $fillable = ['category_id', 'name'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subclasses()
    {
        return $this->hasMany(Subclass::class, 'class_id');
    }
}