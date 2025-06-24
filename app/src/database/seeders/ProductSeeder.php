<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Selfbot',
            'description' => 'A premium selfbot for LINE with advanced features.',
            'price' => 50000,
        ]);

        Product::create([
            'name' => 'Official Bot',
            'description' => 'Subscription for the official LINE bot service.',
            'price' => 100000,
        ]);
    }
}
