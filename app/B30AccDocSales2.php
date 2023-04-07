<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocSales2 extends Model
{
    protected $fillable = [
        'BranchCode', 'Stt', 'BuiltinOrder', 'DocCode', 'DocGroup', 'DocDate', 'CustomerId', 'ItemId', 'Description',
        'Unit', 'Quantity', 'ConvertRate9', 'Quantity9', 'OriginalUnitPrice', 'UnitPrice', 'OriginalAmount9',
        'Amount9', 'OriginalAmount2', 'Amount2',
        'DebitAccount2', 'CreditAccount2', 'DebitAccount', 'CreditAccount', 'DebitAccount3',
        'Amount3', 'OriginalAmount3', 'Amount4', 'OriginalAmount4', 'DiscountRate', 'DebitAccount4',
        'OriginalAmount41', 'Amount41', 'DebitAccount41', 'CreatedBy', 'Gia_Tb_Tt', 'WarehouseId', 'Stt_Hbtl', 'TransCode ',
        'CreditAccount41','CreditAccount4','DeptId'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocSales2";
    public $timestamps = false;
    public static function getItemByStt($accSale, $id)
    {
        $itemAccInfo = B30AccDocSales2::where("Stt", $accSale)->where("ItemId", $id)->get();
        if (sizeof($itemAccInfo) > 0) {
            return  $itemAccInfo[0];
        }
        return null;
    }
    public static function setData($index, $item, $customer, $itemInfo, $warehouses, $itemAccInfo): array
    {
        $discount = $itemAccInfo->Amount4 / $itemAccInfo->Quantity9;
        $amount41 = $itemAccInfo->Amount41 / $itemAccInfo->Quantity9;
        return  [
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'BuiltinOrder' => $index,
            'CustomerId' => $customer->Id,
            'ItemId' => $itemInfo->Id,
            'Description' => $itemInfo->Name,
            'Unit' => $itemInfo->Unit,
            'Quantity' => $item->quantity,
            'ConvertRate9' => 1,
            'Quantity9' => $item->quantity,

            'OriginalUnitPrice' => $itemAccInfo->OriginalUnitPrice,
            'UnitPrice' => $itemAccInfo->OriginalUnitPrice,
            'OriginalAmount9' => $itemAccInfo->OriginalUnitPrice * $item->quantity,
            'Amount9' => $itemAccInfo->OriginalUnitPrice * $item->quantity,
            'OriginalAmount2' => $itemAccInfo->OriginalUnitPrice * $item->quantity,
            'Amount2' => $itemAccInfo->OriginalUnitPrice * $item->quantity,

            'CreditAccount2' => $itemAccInfo->DebitAccount2,
            'DeptId' => $warehouses ? $warehouses->HH->ClassCode3 : '20354472',
            'DebitAccount2' => '5212',
            'CreditAccount' => '632',
            'DebitAccount' => $itemInfo->ItemType == "1" ? ($warehouses ? $warehouses->TP->Name2 : '1561') : ($warehouses ? $warehouses->HH->Name2 : '1561'),
            'DebitAccount3' => '',
            'CreditAccount3' => '',
            'Amount3' => 0,
            'OriginalAmount3' => 0,
            'DebitAccount4' => $warehouses ? $warehouses->HH->ClassCode2 : '1311',
            'DebitAccount41' => $warehouses ? $warehouses->HH->ClassCode2 : '1311',

            'CreditAccount41' => '5214',
            'CreditAccount4' => '5211',
            'Amount41' => $amount41 * $item->quantity,
            'OriginalAmount41' => $amount41 * $item->quantity,

            'Amount4' => $discount * $item->quantity,
            'OriginalAmount4' => $discount * $item->quantity,
            'DiscountRate' => $itemAccInfo->OriginalUnitPrice == 0 || $item->quantity == 0 ? 0 : $discount / $itemAccInfo->OriginalUnitPrice,


            'WarehouseId' => $itemInfo->ItemType == "1" ? ($warehouses ? $warehouses->TP->Id : 0) : ($warehouses ? $warehouses->HH->Id : 0),

            'Stt_Hbtl' => $itemAccInfo->RowId,
            'Gia_Tb_Tt' => '1',
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocGroup' => '1',
            'DocCode' => 'TL',
            'TransCode ' => '2107'
        ];
    }
}
