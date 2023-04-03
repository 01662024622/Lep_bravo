<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class B30AccDocPrepay extends Model
{
    protected $fillable = [
        'BranchCode','Stt', 'BuiltinOrder', 'DocCode',  'DocDate','DocNo', 'CustomerId','Account','Description',
         'Amount', 'OriginalAmount', 'CreatedBy', 'CurrencyCode','ExchangeRate','PaidOriginalAmount','PaidAmount'];
    protected $primaryKey ='Id';
    protected $table = "B30AccDocPrepay";
    public $timestamps = false;
    public static function getItemByStt($accSale,$id){
        $itemAccInfo = B30AccDocPrepay::where("Stt", $accSale)->where("ItemId", $id)->get();
            if (sizeof($itemAccInfo) > 0) {
                return  $itemAccInfo[0];
            }
        return null;
    }

    public static function setData($order,$customer, $warehouses, $Stt): array
    {
        return  [
            'CustomerId' => $customer->Id,
            'DocNo' => "TLN".$order->Id,
            'Description' => "Đơn lên từ nhanh",

            'Account'=>$warehouses?$warehouses->HH->ClassCode2:'',

            'OriginalAmount'=>$order->calcTotalMoney,
            'Amount'=>$order->calcTotalMoney,
            'PaidOriginalAmount'=>$order->calcTotalMoney,
            'PaidAmount'=>$order->calcTotalMoney,


            'Stt'=>$Stt,
            'CurrencyCode' => 'VND',
            'ExchangeRate' => '1',
            'BuiltinOrder' => 1,
            'CreatedBy' => 4,
            'BranchCode' => 'A01',
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'DocCode' => 'TL', //TL
        ];
    }
}
