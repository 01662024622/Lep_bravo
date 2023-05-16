<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Item extends Model
{
    protected $fillable = [
        'Code','Name', 'Unit', 'ItemType','ItemGroupCode','ParentId'
    ];
    protected $primaryKey ='Id';
    protected $table = "B20Item";
    public $timestamps = false;

    public static function getItemByCode(string $code){
        $items = B20Item::where("Code", $code)->get();
        if (sizeof($items) > 0) {
            return $items[0];
        }
        return null;
    }
}
