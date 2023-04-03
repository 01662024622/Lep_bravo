<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Summary of B30AccDocAtchDoc
 */
class B30AccDocAtchDoc extends Model
{
    protected $fillable = [
        'BranchCode', 'DocCode', 'DocGroup', 'DocDate', 'CustomerId', 'CurrencyCode', 'Description',
        'ExchangeRate', 'CreatedBy','Account',
        'TransCode',
        'OriginalDueAmount', 'DueAmount',
        'Stt',
        'AtchDocDate','AtchDocNo','DueDate','AtchDocType'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocAtchDoc";
    public $timestamps = false;
    public static function setData($order, $customer,$warehouses,$Stt): array
    {

        return [
            'OriginalDueAmount'=>$order->calcTotalMoney,
            'DueAmount'=>$order->calcTotalMoney,
            'AtchDocNo'=>"HDN".$order->id,
            'Account'=>$warehouses?$warehouses->HH->ClassCode2:'',
            'CustomerId'=>$customer->Id,
            'Stt'=>$Stt,


            'DueDate' => 2,
            'AtchDocType' => 'DOCDUE',
            'CurrencyCode' => 'VND',
            'ExchangeRate' => '1',
            'CreatedBy' => 4,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'AtchDocDate' => Carbon::today()->format('Y-m-d'),
            'BranchCode' => 'A01',
            'DocCode' => 'H2',
            'Description' => "Đơn hàng lên từ nhanh.vn"
        ];
    }
}
