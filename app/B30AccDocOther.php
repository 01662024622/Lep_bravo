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
        'BranchCode', 'DocCode', 'DocDate',
        'CreatedBy',
        'TransCode',
        'DebitAccount','DebitDueDate','DebitOriginalAmount','DebitOriginalAmount9','CreditAccount',
        'CreditDueDate','CreditOriginalAmount','CreditOriginalAmount9','Amount',
        'Amount9','DebitCustomerId',
        'CreditCustomerId', 'Stt', 'BuiltinOrder','RowId'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocOther";
    public $timestamps = false;
    public static function setData($order,$orderRefund,$debitAcount,$creditAcount,$Stt): array
    {

        return [
            'DebitAccount'=>$debitAcount,
            'DebitDueDate'=>2,
            'DebitOriginalAmount'=>$orderRefund->TotalOriginalAmount,
            'DebitOriginalAmount9'=>$orderRefund->TotalOriginalAmount,
            'CreditAccount'=>$creditAcount,
            'CreditDueDate'=>0,
            'CreditOriginalAmount'=>$orderRefund->TotalOriginalAmount,
            'CreditOriginalAmount9'=>$orderRefund->TotalOriginalAmount,
            'Amount'=>$orderRefund->TotalOriginalAmount,
            'Amount9'=>$orderRefund->TotalOriginalAmount,
            'DebitCustomerId'=>$order->CustomerId,
            'CreditCustomerId'=>$order->CustomerId,

            'Stt'=>$Stt,
            'BuiltinOrder'=>1,
            'TransCode' => '1303',
            'CreatedBy' => 4,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'BranchCode' => 'A01',
            'DocCode' => 'BT'
        ];
    }

}
