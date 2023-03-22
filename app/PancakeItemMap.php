<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PancakeItemMap extends Model
{
    protected $fillable = [
        'n_id','n_parent_code','n_parent_name','code','name','price','inventory','p_id','push'
    ];
    protected $table = "pancake_item_map";
}
