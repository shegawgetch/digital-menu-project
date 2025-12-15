<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MenuItem extends Model
{
    use HasFactory, SoftDeletes; // Enable soft deletes
    protected $fillable = [
        'item_name',
        'category_id',
        'price',
        'tax_percentage',
        'discount',
        'photo',
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
