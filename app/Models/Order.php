<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_date',
        'total_amount',
        'order_status',
        'cart_id',
        'user_id',
        'discount',
        'final_amount',
        'is_deleted',
    ];
}
