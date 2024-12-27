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

    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_items', 'order_id', 'item_id');
    }
}
