<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;   // ✅ You missed this line

// ✅ Needed for the relationship

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'phone',
        'items',
        'total_price',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
