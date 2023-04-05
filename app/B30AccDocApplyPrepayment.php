<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Summary of B30AccDocApplyPrepayment
 */
class B30AccDocApplyPrepayment extends Model
{
    protected $fillable = [
        'CustomerId',
        'Account',
        'RowId_SourceDoc',
        'PrepayOriginalAmount',
        'OriginalAmount',
        'PrepayAmount',
        'Amount',
        'Stt',
        'BuiltinOrder',
        'AtchDocType',
        'CreatedBy'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocApplyPrepayment";
    public $timestamps = false;
    public static function setData($order,$B30AccDocOther): array
    {

        return [
            'Account'=>$B30AccDocOther->RowId,
            'RowId_SourceDoc'=>$B30AccDocOther->CreditAccount,
            'PrepayOriginalAmount'=>$order->calcTotalMoney,
            'OriginalAmount'=>$order->calcTotalMoney,
            'PrepayAmount'=>$order->calcTotalMoney,
            'Amount'=>$order->calcTotalMoney,
            'CustomerId'=>$B30AccDocOther->CustomerId,

            'Stt'=>$B30AccDocOther->Stt,
            'BuiltinOrder'=>'1',
            'AtchDocType' => 'OtherDebit',
            'CreatedBy' => 4,
            // 'DocDate' => Carbon::today()->format('Y-m-d'),
            // 'AtchDocDate' => Carbon::today()->format('Y-m-d'),
            // 'BranchCode' => 'A01',
            // 'DocCode' => 'H2',
            // 'Description' => trim(str_replace('Kho hàng hóa','',$warehouses?$warehouses->HH->Name:''))."Đơn hàng lên từ nhanh.vn"
        ];
    }
}
