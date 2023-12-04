<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SensorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\SensorType::factory()->createMany([[
            'name' => 'Presence',
            'details' => 'It measure the presence.',
            'unity' => ''
        ],
        [
            'name' => 'Humidity',
            'details' => 'It measure the humidity.',
            'unity' => '%'
        ],
        [
            'name' => 'Sound',
            'details' => 'It measure the sound.',
            'unity' => 'dB'
        ],
        [
            'name' => 'Temperature',
            'details' => 'It measure the temperature.',
            'unity' => 'Celsius'
        ],
        [
            'name' => 'Gas',
            'details' => 'It measure the gas.',
            'unity' => '%'
        ],
        [
            'name' => 'Motion',
            'details' => 'It measure the motion.',
            'unity' => '%'
        ],
        [
            'name' => 'Position',
            'details' => 'It measure the position.',
            'unity' => ''
        ],]);
    }
}
