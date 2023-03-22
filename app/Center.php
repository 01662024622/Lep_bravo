<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $fillable = [
        'name','phone','address','nhanhId','active'
    ];
    protected $table = "centers";
}
