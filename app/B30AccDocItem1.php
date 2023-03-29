<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocItem1 extends Model
{
    protected $fillable = [
        'ItemId', 'BranchCode',  'Unit', 'Quantity', 'ConvertRate9', 'Quantity9', 'OriginalUnitCost', 'UnitCost', 'OriginalAmount9',
         'Amount9', 'CreatedBy', 'DebitAccount', 'CreditAccount', 'WarehouseId','Stt','DocDate','DocGroup','DocCode','RowId' ];
    protected $primaryKey ='Id';
    protected $table = "B30AccDocItem1";
    public $timestamps = false;
    public static function setData($index,$item, $itemInfo, $warehouse): array
    {
        return  [
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'BuiltinOrder' => $index,
            'ItemId' => $itemInfo->Id,
            'Desciption' => $itemInfo->Name,
            'Unit' => $itemInfo->Unit,
            'Quantity' => $item->quantity,
            'ConvertRate9' => 1,
            'Quantity9' => $item->quantity,

            'OriginalUnitCost'=>$item->price,
            'UnitCost'=>$item->price,
            'OriginalAmount9'=>$item->money,
            'Amount9'=>$item->money,

            'DebitAccount'=>'15611', //=>$warehouse?$warehouse->ClassCode1:''
            'CreditAccount'=>'15611', //632

            'WarehouseId'=>$warehouse?$warehouse->Id:0,

            'Gia_Tb_Tt' => '1',
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocGroup' => '1',  //1
            'DocCode' => 'PN', //TL
        ];
    }
}
