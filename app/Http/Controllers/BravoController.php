<?php

namespace App\Http\Controllers;

use App\B20Customer;
use App\B20Employee;
use App\B20Item;
use App\B20Warehouse;
use App\B30AccDocItem;
use App\B30AccDocItem1;
use App\B30AccDocItem2;
use App\B30AccDocSales;
use App\B30AccDocSales1;
use App\B30AccDocSales2;
use App\Models\HT20\B20Customer as HT20B20Customer;
use App\Services\SpeedService;
use App\Webhook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Log\NullLogger;

class BravoController extends Controller
{

    private $SpeedService;

    public function get()
    {
        $a= 1;
        $b=2;
        $c=$a+$b;
        return response("true", 200);
    }
    public function create(Request $request)
    {


        $speed = json_decode(json_encode($request->only(["event", "webhooksVerifyToken", "data"])), FALSE);

        if ($speed->webhooksVerifyToken != "Thangui0011@@1996") return response('error', 404);


        if ($speed->event == "productAdd") {
            $speed = $speed->data;
            return $this->procedureProduct($speed);
        }

        if ($speed->event == "orderAdd") {
            $speed = $speed->data;
            return $this->procedureAddOrder($speed);
        }
        if ($speed->event == "orderUpdate") {
            $speed = $speed->data;
            return $this->procedureUpdateOrder($speed);
        }
        if ($speed->event == "inventoryChange") {
            $speed = $speed->data;
            return $this->procedureChange();
        }
        $speed = $speed->data;
        return $this->procedureInventory($speed);
    }


    private function procedureProduct($speed)
    {
        $parentId = $speed->parentId;
        if ($parentId != null || $parentId > 0)
            return response('true', 200);
        // $product = B20Item::create(["Code"=>$speed["data"]["code"],"Name"=>$speed["data"]["name"],"Unit"=>"Chiếc","ItemType"=>2]);

        $res = $this->SpeedService->getProductDetail($speed->productId);
        foreach ($res->data as $value) {
            B20Item::create(["Code" => $value->code, "Name" => $value->name, "Unit" => "Chiếc", "ItemType" => 2]);
        }
        return response("true", 200);
    }




    private function procedureAddOrder($speed)
    {
        
        //detail order
        $orders = $this->SpeedService->getOrderDetail($speed->orderId);
        if (!property_exists($orders, "data")) return response("true", 200);
        $orders = $orders->data->orders;
        $order = null;
        foreach ($orders as $i) {
            $order = $i;
            break;
        }
        if ($order == null) return response("true", 200);
        if ($order->typeId == 1) return $this->procedureAddOrderReal($order);
        if ($order->typeId == 14) return $this->procedureAddOrderRefund($order);
    }







    private function procedureChange()
    {
        $data = $this->SpeedService->getWarehousing()->data->bill;
        foreach ($data as $order) {
            $check = B30AccDocItem::where("DocNo", $order->type==1?'NKN' . $order->id:'XKN' . $order->id)->get();
            if(sizeof($check>0)) continue;
            $description = "";
            if ($order->mode == 3) {
                $description = $order->description==""?"Chuyển kho":"Chuyển kho"."-".$order->description;
            }else
            if ($order->mode == 5) {
                $description =$order->description==""?"Nhà cung cấp":"Nhà cung cấp"."-".$order->description;
            }elseif ($order->mode == 8) {
                $description = $order->description==""?"Kiểm kho":"Kiểm kho"."-".$order->description;
            }else continue;
            // detail from bravo
            $warehouse = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
            $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
            $i = 1;
            $accDocItem1=[];
            $accDocItem2=[];
            if ($order->type == 1) {
                foreach ($order->products as $item) {
                    $itemInfo = B20Item::getItemByCode($item->code);
                    if ($itemInfo==null) {
                        $description = $item->productCode .'-'. $description;
                        continue;
                    }
                    
                    $accDocItem1[]= B30AccDocItem1::setData($i, $item, $itemInfo, $warehouse);
                    B30AccDocItem1::create($line);
                    $i++;
                }
            }
            if ($order->type == 2) {
                foreach ($order->products as $item) {
                    $itemInfo = B20Item::getItemByCode($item->code);
                    if ($itemInfo==null) {
                        $description = $item->productCode .'-'. $description;
                        continue;
                    }
                    
                    $accDocItem2[]= B30AccDocItem2::setData($i, $item, $itemInfo, $warehouse);
                    B30AccDocItem2::create($line);
                    $i++;
                }
            }
            $acc= B30AccDocItem::setData($order, $employeeid, $description);
            $acc = B30AccDocItem::create($data);
            $acc = B30AccDocItem::find($acc->Id);
            if($order->type==1){
                foreach($accDocItem1 as $sale){
                    $sale["Stt"] = $acc->Stt;
                    B30AccDocItem1::create($sale);
                }
            }
            if($order->type==2){
                foreach($accDocItem2 as $sale){
                    $sale["Stt"] = $acc->Stt;
                    B30AccDocItem2::create($sale);
                }
            }
            B30AccDocSales::runExec($acc);
        
        }
        return response("true", 200);
    }






    private function procedureAddOrderReal($order)
    {
        // detail from bravo
        $customer = B20Customer::getCustomer($order);
        $warehouse = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $order->usedPoints = $order->usedPoints ? $order->usedPoints : 0;
        $order->moneyDiscount = $order->moneyDiscount ? $order->moneyDiscount : 0;
        $data = B30AccDocSales::setData($order, $customer, $employeeid, $warehouse);
        $i = 1;
        $j = 1;
        $coin = new Coin($order);
        $listAccDocSale1 = [];
        foreach ($order->products as $item) {
            $itemInfo = B20Item::getItemByCode($item->productCode);
            if ($itemInfo==null) {
                $order->desciption = $item->productCode .'-'. $order->desciption;
                continue;
            }
            // set Coin
            $item->usedPoints = $coin->getCoin($j == sizeof($order->products),$item->price);

            $listAccDocSale1[]= B30AccDocSales1::setData($i, $item, $customer, $itemInfo, $warehouse);
            $i++;
            $j++;
            // check sản phẩm có quà tặng kèm không nếu có thì add vào với giá 0đ
            if (property_exists($item, "giftProducts")) {
                if (sizeof($item->giftProducts) > 0) {
                    foreach ($item->giftProducts as $gift) {

                        $giftInfo = B20Item::getItemByCode($gift->productCode);
                        if ($giftInfo==null) {
                            $order->desciption = $item->productCode .'-'. $order->desciption;
                            continue;
                        }
                        $gift->Price = 0;
                        $gift->usedPoints = 0;
                        $gift->discount = 0;
                        $gift->quantity = $gift->productQuantity;
                        $listAccDocSale1[]= B30AccDocSales1::setData($i, $gift, $customer, $giftInfo, $warehouse);
                        $i++;
                    }
                }
            }
        }
        $data["Description"] = $order->desciption!=""?$data["Description"]."-".$order->desciption:$data["Description"];
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        foreach($listAccDocSale1 as $sale){
            $sale["Stt"] = $acc->Stt;
            B30AccDocSales1::create($sale);
        }
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }





    private function procedureAddOrderRefund($order)
    {
        // detail from bravo
        $this->SpeedService = null;
        $customer = B20Customer::getCustomer($order);
        $warehouse = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $order->moneyDiscount = $order->moneyDiscount ? $order->moneyDiscount : 0;
        $data = B30AccDocSales::setDataRefund($order, $customer, $employeeid, $warehouse);
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        $accSale = B30AccDocSales::where("DocNo", "HDN" . $order->returnFromOrderId)->get();
        sizeof($accSale) > 0 ? $accSale = $accSale[0]->Stt : null;
        $i = 1;
        $listAccDocSale2 = [];
        foreach ($order->products as $item) {
            $itemInfo = B20Item::getItemByCode($item->productCode);
            if ($itemInfo==null) {
                $order->desciption = $item->productCode .'-'. $order->desciption;
                continue;
            }
            $itemAccInfo = B30AccDocSales1::getItemByStt($accSale,$itemInfo->Id);
            if ($itemAccInfo==null) {
                $order->desciption = $item->productCode .'-'. $order->desciption;
                continue;
            }
            $listAccDocSale2[]=B30AccDocSales2::setData($i, $item, $customer, $itemInfo, $warehouse, $itemAccInfo);
            $i++;
        }
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        foreach($listAccDocSale2 as $sale){
            $sale["Stt"] = $acc->Stt;
            B30AccDocSales2::create($sale);
        }
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }






    private function procedureUpdateOrder($speed)
    {
        B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->update(['DocStatus' => B30AccDocSales::convertStatus(($speed->status))]);
        $acc = B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->get();
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }
}

class Coin {
    function __construct($order) {
        $this->usedPoints = $order->usedPoints;
        $this->moneyDiscountPercent = $order->usedPoints / ($order->calcTotalMoney + $order->moneyDiscount + $order->usedPoints);
        $this->allotted = 0;
    }
    private $usedPoints;
    private $moneyDiscountPercent;
    private $allotted ;
    public function getCoin(bool $endOfList,$price){
        if ($endOfList)  return $this->usedPoints- $this->allotted;
            $coin = (int)($price * $this->moneyDiscountPercent);
            $this->allotted = $this->allotted + $coin;
            return $coin;
    }
}