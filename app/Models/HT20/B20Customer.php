<?php

namespace App\Models\HT20;

use Illuminate\Database\Eloquent\Model;

class B20Customer extends Model
{
    protected $fillable = [
        'Code','Name'
    ];
    protected $table = "B20Customer";
}
