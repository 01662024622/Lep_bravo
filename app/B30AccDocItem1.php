<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocItem1 extends Model
{
    protected $fillable = [
        'ItemId', 'BranchCode',  'Unit', 'Quantity', 'ConvertRate9', 'Quantity9', 'OriginalUnitCost', 'UnitCost', 'OriginalAmount9','Amount','OriginalAmount',
         'Amount9', 'CreatedBy', 'DebitAccount', 'CreditAccount', 'WarehouseId','Stt','DocDate','DocGroup','DocCode','RowId','DeptId','BuiltinOrder' ];
    protected $primaryKey ='Id';
    protected $table = "B30AccDocItem1";
    public $timestamps = false;
    public static function setData($index,$item, $itemInfo, $warehouses,$account): array
    {
        return  [
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'BuiltinOrder' => $index,
            'ItemId' => $itemInfo->Id,
            'Description' => $itemInfo->Name,
            'Unit' => $itemInfo->Unit,
            'Quantity' => $item->quantity,
            'ConvertRate9' => 1,
            'Quantity9' => $item->quantity,

            'DeptId' => $warehouses ? $warehouses->HH->ClassCode3 : '20354472',
            'OriginalUnitCost'=>$item->price,
            'UnitCost'=>$item->price,
            'OriginalAmount9'=>$item->money,
            'Amount9'=>$item->money,
            'Amount'=>$item->money,
            'OriginalAmount'=>$item->money,

            'DebitAccount'=>$itemInfo->ItemType == "1" ? ($warehouses ? $warehouses->TP->Name2 : '1561') : ($warehouses ? $warehouses->HH->Name2 : '1561'), //=>$warehouse?$warehouse->ClassCode1:''
            'CreditAccount'=>$account, //632

            'WarehouseId'=>$itemInfo->ItemType == "1" ? ($warehouses ? $warehouses->TP->Id : 0) : ($warehouses ? $warehouses->HH->Id : 0),

            'Gia_Tb_Tt' => '1',
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocGroup' => '1',  //1
            'DocCode' => 'PN', //TL
        ];
    }
}
