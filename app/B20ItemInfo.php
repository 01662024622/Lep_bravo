<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class B20ItemInfo extends Model
{
    protected $fillable = [
        'ItemId','ProductId', 'Weight '
    ];
    protected $primaryKey ='Id';
    protected $table = "B20ItemInfo";
    public $timestamps = false;
}
