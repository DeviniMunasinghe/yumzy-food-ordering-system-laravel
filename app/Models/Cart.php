<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['added_at', 'user_id'];

    public function items()
    {
        return
            $this->hasMany(CartItem::class);
    }
}
