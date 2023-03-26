<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Summary of B30AccDocItem
 */
class B30AccDocItem extends Model
{
    protected $fillable = [
        'BranchCode', 'DocCode', 'DocNo', 'DocGroup', 'DocDate', 'CustomerId', 'Person', 'Address', 'Description',
        'EmployeeId', 'TransCode', 'CurrencyCode', 'ExchangeRate', 'DocStatus', 'CreatedBy',
        'TotalOriginalAmount', 'TotalAmount0', 'TotalAmount',
        'TotalOriginalAmount0', 'TotalOriginalAmount','Stt',
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocItem";
    public $timestamps = false;
    public static function setData($order, $employeeid,$description): array
    {
       return [
            'DocNo' => $order->type==1?'NKN' . $order->id:'XKN' . $order->id,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'CustomerId' => 14124712,
            'Person' => 'Admin(Cấm xóa)',
            'Address' => 'Admin(Cấm xóa)',
            'EmployeeId' => $employeeid?$employeeid:null,
            'DocStatus' => 4,

            'TotalAmount0'=>$order->money,
            'TotalAmount'=>$order->money,
            'TotalOriginalAmount0'=>$order->money,
            'TotalOriginalAmount'=>$order->money,

            'TransCode' => $order->type==1?'2100':'2211',
            'Description' => "Đơn lên từ nhanh.vn-".$description,
            'CurrencyCode' => 'VND',
            'DiscountRate' => 0,
            'ExchangeRate' => '1',
            'CreatedBy' => 4,
            'DocGroup' => $order->type==1?'1':'2',
            'BranchCode' => 'A01',
            'DocCode' => $order->type==1?'PN':'PX'
        ];
    }
}
