<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Role::factory()->createMany([[
            'name' => 'admin',
            'description' => 'The administrator can create, read, update and delete.',
        ],
        [
            'name' => 'guest',
            'description' => 'The guest only read.',
        ],
        [
            'name' => 'user',
            'description' => 'The user can create, read and update.',
        ],]);
    }
}
