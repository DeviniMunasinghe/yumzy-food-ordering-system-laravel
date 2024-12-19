<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['category_name'=>'Pizza']);
        Category::create(['category_name'=>'Cake']);
        Category::create(['category_name'=>'Beverage']);
    }   
}
