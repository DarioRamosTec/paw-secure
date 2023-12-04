<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetSpace extends Model
{
    use HasFactory;
    protected $table = "pet_space";

    protected $fillable = ['pet', 'space'];
}
