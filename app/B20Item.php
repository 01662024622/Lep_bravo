<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Item extends Model
{
    protected $fillable = [
        'Code','Name', 'Unit', 'ItemType'
    ];
    protected $primaryKey ='Id';
    protected $table = "B20Item";
    public $timestamps = false;
}
