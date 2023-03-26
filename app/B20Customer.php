<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B20Customer extends Model
{
    protected $fillable = [
        'Code','ParentId','Name', 'CustomerType', 'Address', 'BillingAddress','Tel', 'Email'];
    protected $primaryKey ='Id';
    protected $table = "B20Customer";
    public $timestamps = false;
    public static function getCustomer($order) : B20Customer
    {

        $customer = B20Customer::where('Code', $order->customerMobile)->get();
        if (sizeof($customer) > 0) {
            $customer = $customer[0];
        } else {
            $address =$order->customerAddress."-".$order->customerWard."-".$order->customerDistrict."-".$order->customerCity;
            $customer = B20Customer::create([
                'Code' => $order->customerMobile,
                'Name' => $order->customerName,
                'CustomerType' => 0,
                'ParentId' => '18104192',
                'Address' => $address,
                'BillingAddress' => $address,
                'Tel' => $order->customerMobile
            ]);
        }
        return $customer;
    }
}
