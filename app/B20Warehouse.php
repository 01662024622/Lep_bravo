<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Warehouse extends Model
{
    protected $fillable = [
        'Name2','Address','ClassCode1','ClassCode2','ClassCode3'];
    protected $primaryKey ='Id';
    protected $table = "B20Warehouse";
    public $timestamps = false;

    public static function getWarehouse(int $id)
    {

        $warehouse = B20Warehouse::where('ClassCode1', (string) $id)->get();
        if (sizeof($warehouse) > 0) {
            $warehouse = $warehouse[0];
            return $warehouse;
        }
        return null;
    }
}
