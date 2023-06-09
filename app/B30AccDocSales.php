<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Summary of B30AccDocSales
 */
class B30AccDocSales extends Model
{
    protected $fillable = [
        'BranchCode', 'DocCode', 'DocNo', 'DocGroup', 'DocDate', 'CustomerId', 'Person', 'Address', 'Description',
        'EmployeeId', 'TransCode', 'DiscountRate', 'CurrencyCode', 'ExchangeRate', 'DocStatus', 'CreatedBy',
        'PlateNumber', 'DueDate',
        'DebitAccountMk','DebitAccountFl', 'DebitAccountDl', 'CreditAccountMk', 'CreditAccountFL', 'CreditAccountDl',
        'TotalOriginalAmount', 'TotalAmount0', 'TotalAmount', 'TotalAmount4', 'TotalAmount41','DeptId','ExpenseCatgId',
        'TotalOriginalAmount0', 'TotalOriginalAmount', 'TotalOriginalAmount4', 'TotalOriginalAmount41','Stt','TotalOriginalAmountDl','TotalAmountDl'
    ];
    protected $primaryKey = 'Id';
    protected $table = "B30AccDocSales";
    public $timestamps = false;
    public static function setData($order, $customer, $employeeid, $warehouses): array
    {

        $address = $order->customerAddress . "-" . $order->customerWard . "-" . $order->customerDistrict . "-" . $order->customerCity;
        return [
            'DocNo' => 'HDN' . $order->id,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'CustomerId' => $customer->Id,
            'Person' => $order->customerName,
            'Address' => $address,
            'EmployeeId' => $employeeid?$employeeid:null,
            'DocStatus' => $order->statusCode?B30AccDocSales::convertStatus($order->statusCode):11,
            'DebitAccountMk' => '',
            'DebitAccountFl' =>'',
            'DebitAccountDl' => '',
            'CreditAccountMk' => $warehouses?$warehouses->HH->ClassCode2:'',
            'CreditAccountFL' => $warehouses?$warehouses->HH->ClassCode2:'',
            'CreditAccountDl' => $warehouses?$warehouses->HH->ClassCode2:'',
            'PlateNumber' => $order->couponCode?$order->couponCode:'',
            'DeptId' => $warehouses ? $warehouses->HH->ClassCode3 : '20354472',
            'ExpenseCatgId'=>'26931992',
            'TotalAmount0'=>$order->usedPoints+$order->moneyDiscount+$order->calcTotalMoney,
            'TotalAmount4'=>$order->moneyDiscount,
            'TotalAmountDl'=> $order->customerShipFee,
            'TotalAmount41'=> $order->usedPoints,
            'TotalAmount'=>$order->calcTotalMoney,
            'TotalOriginalAmount0'=>$order->usedPoints+$order->moneyDiscount+$order->calcTotalMoney,
            'TotalOriginalAmount4'=>$order->moneyDiscount,
            'TotalOriginalAmount41'=> $order->usedPoints,
            'TotalOriginalAmount'=>$order->calcTotalMoney,
            'Description' => trim(str_replace('Kho hàng hóa','',$warehouses?$warehouses->HH->Name:''))."-".$order->description,

            'TransCode' => '2301',
            'CurrencyCode' => 'VND',
            'DiscountRate' => 0,
            'ExchangeRate' => '1',
            'CreatedBy' => 4,
            'DueDate' => '2',
            'DocGroup' => '2',
            'BranchCode' => 'A01',
            'DocCode' => 'H2'
        ];
    }
    public static function setDataRefund($order, $customer, $employeeid, $warehouses): array
    {
        $order->description=strlen($order->description) > 100 ? substr($order->description,0,50)."..." : $order->description;
        $address = $order->customerAddress . "-" . $order->customerWard . "-" . $order->customerDistrict . "-" . $order->customerCity;
        return [
            'DocNo' => 'TLN' . $order->id,
            'DocDate' => Carbon::today()->format('Y-m-d'),
            'CustomerId' => $customer->Id,
            'Person' => $order->customerName,
            'Address' => $address,
            'EmployeeId' => $employeeid?$employeeid:null,
            'DocStatus' => 4,
            'DebitAccountMk' => '',
            'DebitAccountFl' =>'',
            'DebitAccountDl' => '',
            'CreditAccountMk' => $warehouses?$warehouses->HH->ClassCode2:'',
            'CreditAccountFL' => $warehouses?$warehouses->HH->ClassCode2:'',
            'CreditAccountDl' => $warehouses?$warehouses->HH->ClassCode2:'',
            'PlateNumber' => $order->couponCode?$order->couponCode:'',
            'DeptId' => $warehouses ? $warehouses->HH->ClassCode3 : '20354472',

            'TotalAmount0'=>$order->moneyDiscount+$order->calcTotalMoney,
            'TotalAmount4'=>$order->moneyDiscount,
            'TotalAmountDl'=> $order->customerShipFee,
            'TotalAmount'=>$order->calcTotalMoney,
            'TotalOriginalAmount0'=>$order->moneyDiscount+$order->calcTotalMoney,
            'TotalOriginalAmount4'=>$order->moneyDiscount,
            'TotalOriginalAmountDl'=>$order->customerShipFee,
            'TotalOriginalAmount'=>$order->calcTotalMoney,

            'TransCode' => '2107',
            'Description' => trim(str_replace('Kho hàng hóa','',$warehouses?$warehouses->HH->Name:''))."-".$order->description,
            'CurrencyCode' => 'VND',
            'DiscountRate' => 0,
            'ExchangeRate' => '1',
            'CreatedBy' => 4,
            'DocGroup' => '1',
            'BranchCode' => 'A01',
            'DocCode' => 'TL'
        ];
    }
    public static function convertStatus($status): int
    {
        switch ($status) {
            case "New":
            case "Confirming":
            case "CustomerConfirming":
            case "Confirmed":
            case "Packing":
            case "Packed":
            case "ChangeDepot":
                return 11;
            case "Pickup":
                return 12;
            case "Shipping":
                return 13;
            case "Success":
                return 14;
            case "Failed":
            case "Canceled":
            case "Aborted":
            case "CarrierCanceled":
            case "SoldOut":
            case "Returning":
            case "Returned":
                return 15;
            default:
                return 14;
        }
    }

    public static function runExec($acc,$code=''){
        if(!property_exists($acc, 'DocStatus')) {$acc->DocStatus=4;}
        $sql ="use B8R3_AuCouture_TT_QT;EXECUTE dbo.usp_B30AccDoc_Post
        @_BranchCode = 'A01',
        @_Stt = '".$acc->Stt."',
        @_DocDate = '".Carbon::today()->format('Y-m-d')."',
        @_DocCode ='". ($code!=''?$code:'H2')."',
        @_DocStatus = ". $acc->DocStatus .",
        @_UpdateType = NULL,
        @_RelationSttListNotUpdate = 1,
        @_FiscalYear = ".Carbon::today()->format('Y')."";
        DB::statement($sql);
    }
}
