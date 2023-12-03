<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;
    protected $table = "pets";

    public function spaces() {
        return $this->belongsToMany(Space::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function animal() {
        return $this->belongsTo(Animal::class, 'animal');
    }

}
