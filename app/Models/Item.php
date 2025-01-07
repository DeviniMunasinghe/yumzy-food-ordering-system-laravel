<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'item_description',
        'item_price',
        'item_image',
        'category_id',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
