<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'address',
        'city',
        'postal_code',
        'phone_number',
        'first_name',
        'last_name',
    ];
}
