<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class vGia extends Model
{
    protected $fillable = [
        'UnitCost', 'WarehouseId', 'ItemId','DocDate'
    ];
    protected $primaryKey = null;
    protected $table = "vGia";
    public $timestamps = false;

}
