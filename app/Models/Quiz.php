<?php

namespace App\Models;

use App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;


    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
