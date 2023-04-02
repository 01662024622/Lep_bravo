<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Summary of B30AccDocOther
 */
class B30AccDocOther extends Model
{
    protected $fillable = [
        'BranchCode', 'DocCode', 'DocNo', 'DocGroup', 'DocDate', 'CustomerId', 'Person', 'CurrencyCode', 'Description',
        'ExchangeRateType', 'ExchangeRate', 'Posted', 'PostGeneralLedger', 'PostStockLedger', 'DocStatus', 'CreatedBy',
        'Ma_Nvu', 'FiscalYear',
        'TransCode',
        'TotalOriginalAmount', 'TotalAmount0', 'TotalAmount',
        'TotalOriginalAmount0', 'Stt', BuiltinOrder
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocOther";
    public $timestamps = false;
    public static function setData($order): array
    {

        return [
            'DocNo' => 'BTN' . $order->id,
            'TotalAmount0'=>$order->usedPoints+$order->moneyDiscount+$order->calcTotalMoney,
            'TotalOriginalAmount'=>$order->calcTotalMoney,
            'TotalAmount'=>$order->calcTotalMoney,
            'TotalOriginalAmount0'=>$order->usedPoints+$order->moneyDiscount+$order->calcTotalMoney,

            'PostGeneralLedger' => 1,
            'PostStockLedger' => 1,
            'Posted' => 4,
            'DocStatus' => 4,
            'Ma_Nvu' => 'K',

            'TransCode' => '1303',
            'CurrencyCode' => 'VND',
            'DiscountRate' => 0,
            'ExchangeRate' => '1',
            'ExchangeRateType' => 2,
            'CreatedBy' => 4,
            'FiscalYear' => Carbon::today()->format('Y'),
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'DocGroup' => '3',
            'BranchCode' => 'A01',
            'DocCode' => 'BT',
            'Description' => 'Bù trừ công nợ trả lại hàng'
        ];
    }
}
