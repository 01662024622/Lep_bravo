<?php

namespace App\Models\HT20;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
	protected $fillable = [
        'name', 'code', 'description','status','user_id','create_by','modify_by'
    ];
    protected $fillable_store = [
        'name', 'code', 'description','status','user_id'
    ];
    protected $fillable_update = [
        'name', 'code', 'description','status','user_id'
    ];
    public function users()
    {
        return $this->hasMany('App\Models\HT20\User');
    }
    protected $table = "ht20_apartments";
}
