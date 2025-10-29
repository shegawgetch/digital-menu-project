<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory; // Make sure this trait is imported and used

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
