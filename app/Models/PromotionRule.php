<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'min_price',
        'discount_percentage',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
