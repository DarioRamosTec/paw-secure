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
            'name' => 'presence',
            'details' => 'It measure the presence.',
            'unity' => '',
            'feed' => 'movimiento'
        ],
        [
            'name' => 'humidity',
            'details' => 'It measure the humidity.',
            'unity' => '%',
            'feed' => 'humedad'
        ],
        [
            'name' => 'sound',
            'details' => 'It measure the sound.',
            'unity' => '',
            'feed' => 'sonido'
        ],
        [
            'name' => 'temperature',
            'details' => 'It measure the temperature.',
            'unity' => '°C',
            'feed' => 'temperatura'
        ],
        [
            'name' => 'gas',
            'details' => 'It measure the gas.',
            'unity' => 'ppm',
            'feed' => 'mq2'
        ],
        [
            'name' => 'motion',
            'details' => 'It measure the motion.',
            'unity' => '',
            'feed' => 'acelerometro'
        ],
        [
            'name' => 'position',
            'details' => 'It measure the position.',
            'unity' => '',
            'feed' => 'gps'
        ],]);
    }
}
