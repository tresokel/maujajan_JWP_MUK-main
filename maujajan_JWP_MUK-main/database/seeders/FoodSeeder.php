<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('food')->insert([
            [
                'name' => 'Nasi Goreng',
                'category' => 'Makanan',
                'price' => 20000,
                'description' => 'Nasi Goreng spesial dengan bumbu racik tersendiri (Homemade)',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Mie Goreng Taliwang',
                'category' => 'Makanan',
                'price' => 25000,
                'description' => 'Mie goreng dengan khas rasa Taliwang, pedas dan gurih',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Es Teh Manis',
                'category' => 'Minuman',
                'price' => 10000,
                'description' => 'Es Teh Manis segar',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ice Matcha Tea Latte',
                'category' => 'Minuman',
                'price' => 12500,
                'description' => 'Minuman Matcha asli import dari jepang dengan rasa khas',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Potato Wedges',
                'category' => 'Cemilan',
                'price' => 15000,
                'description' => 'Cemilan kentang goreng dengan bumbu spesial',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
