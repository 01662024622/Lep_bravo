<?php

namespace App\Models\HT20;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $fillable = [
        'group_id', 'user_id', 'status', 'create_by', 'modify_by'
    ];
    protected $table = "ht20_group_user";
}
