<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewAndEvent extends Model
{
    use HasFactory;

    public function newsimage(){
        return $this->hasMany(NewImage::class, 'news_id');
    }
}
