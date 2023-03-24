<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Customer extends Model
{
    protected $fillable = [
        'Code','ParentId','Name', 'CustomerType', 'Address', 'BillingAddress','Tel', 'Email'];
    protected $primaryKey ='Id';
    protected $table = "B20Customer";
    public $timestamps = false;
}
