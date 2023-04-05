<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class B20Warehouse extends Model
{
    protected $fillable = [
        'Name','Name2','Address','ClassCode1','ClassCode2','ClassCode3'];
    protected $primaryKey ='Id';
    protected $table = "B20Warehouse";
    public $timestamps = false;

    public static function getWarehouse(int $id)
    {
        $warehouses=new stdClass();
        $warehouse = B20Warehouse::where('ClassCode1', "HH-".$id)->get();
        if (sizeof($warehouse) > 0) {
            $warehouses->HH = $warehouse[0];
        }else{
            return null;
        }
        $warehouse = B20Warehouse::where('ClassCode1', "TP-".$id)->get();
        if (sizeof($warehouse) > 0) {
            $warehouses->TP = $warehouse[0];
        }else{
            return null;
        }

        return $warehouses;
    }
}
