<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;
    protected $table = "spaces";
    
    public function pets() {
        return $this->belongsToMany(Pet::class, 'pet_space', 'space', 'pet');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }

    protected $with = ['pets'];
}
