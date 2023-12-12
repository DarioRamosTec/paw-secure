<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;
    protected $table = "sensors";

    public function sensor_type()
    {
        return $this->belongsTo(SensorType::class, 'sensor_type');
    }

    protected $with = ['sensor_type'];

}
