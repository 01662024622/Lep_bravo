<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocSales2 extends Model
{
    protected $fillable = [
        'BranchCode','Stt', 'BuiltinOrder', 'DocCode', 'DocGroup', 'DocDate', 'CustomerId', 'ItemId', 'Description',
        'Unit', 'Quantity', 'ConvertRate9', 'Quantity9', 'OriginalUnitPrice', 'UnitPrice', 'OriginalAmount9',
         'Amount9', 'OriginalAmount2', 'Amount2',
          'DebitAccount2', 'CreditAccount2', 'DebitAccount', 'CreditAccount', 'DebitAccount3',
           'Amount3', 'OriginalAmount3', 'Amount4', 'OriginalAmount4', 'DiscountRate', 'DebitAccount4',
            'OriginalAmount41', 'Amount41', 'DebitAccount41', 'CreatedBy', 'Gia_Tb_Tt', 'WarehouseId','Stt_Hbtl','TransCode '];
    protected $primaryKey ='Id';
    protected $table = "B30AccDocSales2";
    public $timestamps = false;
    public static function setData($index,$item, $customer, $itemInfo, $warehouse,$Stt,$itemAccInfo): array
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

            'CreditAccount2'=>$itemAccInfo->DebitAccount2,
            'DebitAccount2'=>'5212',
            'CreditAccount'=>'632',
            'DebitAccount'=>$warehouse->Name2?$warehouse->Name2:'1561',
            'DebitAccount3'=>'',
            'CreditAccount3'=>'',
            'Amount3'=>0,
            'OriginalAmount3'=>0,
            'DebitAccount4'=>'5211',
            'DebitAccount41'=>'5214',

            'Amount4'=>$item->discount*$item->quantity,
            'OriginalAmount4'=>$item->discount*$item->quantity,
            'DiscountRate'=>$item->price==0||$item->quantity==0?0:$item->discount/($item->price*$item->quantity),


            'WarehouseId'=>$warehouse?$warehouse->Id:0,

            'Stt_Hbtl'=>$itemAccInfo->RowId,
            'Gia_Tb_Tt' => '1',
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocGroup' => '1',
            'DocCode' => 'TL',
            'TransCode '=>'2107'
        ];
    }
}
