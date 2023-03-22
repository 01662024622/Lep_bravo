<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpToken extends Model
{
    protected $fillable = [
        'token'
    ];
    protected $table = "erp_token";
}
