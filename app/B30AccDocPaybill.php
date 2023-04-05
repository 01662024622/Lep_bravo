<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Summary of B30AccDocPaybill
 */
class B30AccDocPaybill extends Model
{
    protected $fillable = [
        'BranchCode', 'DocCode', 'DocDate',
        'RowId_SourceDoc',
        'Account',
        'CustomerId',
        'OriginalPaymentAmount',
        'PaymentAmount',
        'OriginalAmount',
        'Amount',
        'TransType ',
        'CreatedBy',
         'Stt', 'BuiltinOrder'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocPaybill";
    public $timestamps = false;
    public static function setData($order,$B30AccDocOther): array
    {

        return [

            'Account'=>$B30AccDocOther->RowId,
            'RowId_SourceDoc'=>$B30AccDocOther->CreditAccount,

            'OriginalPaymentAmount'=>$order->calcTotalMoney,
            'PaymentAmount'=>$order->calcTotalMoney,
            'OriginalAmount'=>$order->calcTotalMoney,
            'CustomerId'=>$B30AccDocOther->CustomerId,

            'Stt'=>$B30AccDocOther->Stt,
            'BuiltinOrder'=>1,
            'CreatedBy' => 4,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'BranchCode' => 'A01',
            'DocCode' => 'BT'
        ];
    }
}
