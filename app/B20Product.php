<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Product extends Model
{
    protected $fillable = [
        'Code','Name', 'ParentId', 'Unit','ProductType','ConvertRate1','ConvertRate2','IsGroup'
    ];
    protected $primaryKey ='Id';
    protected $table = "B20Product";
    public $timestamps = false;

    public static function getItemByCode(string $code){
        $items = B20Product::where("Code", $code)->get();
        if (sizeof($items) > 0) {
            return $items[0];
        }
        return null;
    }
}
