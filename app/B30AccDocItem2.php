<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocItem2 extends Model
{
    protected $fillable = [
        'ItemId', 'BranchCode', 'Unit', 'Quantity', 'ConvertRate9', 'Quantity9', 'OriginalUnitCost', 'UnitCost', 'OriginalAmount9',
         'Amount9', 'CreatedBy', 'DebitAccount', 'CreditAccount', 'WarehouseId','Stt','DocDate','DocGroup','DocCode','TransCode'];
    protected $primaryKey ='Id';
    protected $table = "B30AccDocItem2";
    public $timestamps = false;
    public static function setData($index,$item, $itemInfo, $warehouse,$account): array
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

            'DebitAccount'=>$account, //=>$warehouse?$warehouse->ClassCode1:''
            'CreditAccount'=>$warehouse?$warehouse->Name2:'', //632

            'WarehouseId'=>$warehouse?$warehouse->Id:0,
            'TransCode '=>'2107',
            'Gia_Tb_Tt' => '1',
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocGroup' => '2',  //1
            'DocCode' => 'PX', //TL
        ];
    }
}
