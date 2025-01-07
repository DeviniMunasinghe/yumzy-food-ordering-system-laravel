<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'promotion_description',
        'promotion_image',
        'start_date',
        'end_date',
        'categories',
    ];

    public function rules()
    {
        return $this->hasMany((PromotionRule::class));
    }
}
