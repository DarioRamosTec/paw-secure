<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Animal::factory()->createMany([[
            'name' => 'dog',
            'description' => 'The woof woof!',
        ],
        [
            'name' => 'cat',
            'description' => 'The meow meow!',
        ],
        [
            'name' => 'rabbit',
            'description' => 'The chi chi!',
        ],]);
    }
}
