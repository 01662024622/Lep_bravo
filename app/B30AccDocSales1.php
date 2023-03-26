<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocSales1 extends Model
{
    protected $fillable = [
        'BranchCode','Stt', 'BuiltinOrder', 'DocCode', 'DocGroup', 'DocDate', 'CustomerId', 'ItemId', 'Description',
        'Unit', 'Quantity', 'ConvertRate9', 'Quantity9', 'OriginalUnitPrice', 'UnitPrice', 'OriginalAmount9',
         'Amount9', 'OriginalAmount2', 'Amount2',
          'DebitAccount2', 'CreditAccount2', 'DebitAccount', 'CreditAccount', 'DebitAccount3',
           'Amount3', 'OriginalAmount3', 'Amount4', 'OriginalAmount4', 'DiscountRate', 'DebitAccount4',
            'OriginalAmount41', 'Amount41', 'DebitAccount41', 'CreatedBy', 'Gia_Tb_Tt', 'WarehouseId'];
    protected $primaryKey ='Id';
    protected $table = "B30AccDocSales1";
    public $timestamps = false;
    public static function setData($index,$item, $customer, $itemInfo, $warehouse,$Stt): array
    {
        return  [
            'Stt'=>$Stt,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'BuiltinOrder' => $index,
            'CustomerId' => $customer->Id,
            'ItemId' => $itemInfo->Id,
            'Description' => $itemInfo->Name,
            'Unit' => $itemInfo->Unit,
            'Quantity' => $item->quantity,
            'ConvertRate9' => 1,
            'Quantity9' => $item->quantity,

            'OriginalUnitPrice'=>$item->price,
            'UnitPrice'=>$item->price,
            'OriginalAmount9'=>$item->price*$item->quantity,
            'Amount9'=>$item->price*$item->quantity,
            'OriginalAmount2'=>$item->price*$item->quantity,
            'Amount2'=>$item->price*$item->quantity,

            'DebitAccount2'=>$warehouse?$warehouse->ClassCode2:'131',  //5212
            'CreditAccount2'=>'5111', // $warehouse?$warehouse->ClassCode2:'131'
            'DebitAccount'=>'632', //=>$warehouse?$warehouse->ClassCode1:''
            'CreditAccount'=>$warehouse?$warehouse->ClassCode1:'', //632
            'DebitAccount3'=>'',
            'CreditAccount3'=>'',
            'Amount3'=>0,
            'OriginalAmount3'=>0,
            'DebitAccount4'=>'5211',
            'DebitAccount41'=>'5214',

            'Amount4'=>$item->discount*$item->quantity,
            'OriginalAmount4'=>$item->discount*$item->quantity,
            'DiscountRate'=>$item->price==0||$item->quantity==0?0:$item->discount/($item->price*$item->quantity)*100,
            'Amount41'=>$item->usedPoints,
            'OriginalAmount41'=>$item->usedPoints,

            'WarehouseId'=>$warehouse?$warehouse->Id:0,

            'Gia_Tb_Tt' => '1',
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocGroup' => '2',  //1
            'DocCode' => 'H2', //TL
        ];
    }
}
