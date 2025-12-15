<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{     
    use  SoftDeletes; 
    protected $fillable = ['name', 'description', 'image', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
