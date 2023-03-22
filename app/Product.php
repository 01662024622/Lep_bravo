<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code','pId','name','parentId'
    ];
    protected $table = "products1";
}
