<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;// Disable timestamps

    protected $fillable =[
        'category_name',
    ];

    public function items(){
        return $this->hasMany(Item::class);
    }
}
