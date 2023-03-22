<?php

namespace App\Models\HT20;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = "ht20_users";

    protected $fillable = [
        'name', 'tagname', 'avata','position', 'apartment_id', 'location', 'skype', 'email_htauto', 'email', 'phone', 'role',
         'birth_day', 'authentication', 'password','status','create_by','modify_by'
    ];
    protected $fillable_store = [
        'name','avata', 'position', 'apartment_id', 'location', 'skype', 'email_htauto', 'phone_htauto', 'phone'
    ];
    protected $fillable_update = [
        'name', 'avata','position', 'apartment_id', 'location', 'skype', 'email_htauto', 'phone'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function apartment()
    {
        return $this->belongsTo('App\Models\HT20\Apartment');
    }
}
