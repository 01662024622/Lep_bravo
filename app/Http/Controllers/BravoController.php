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
        B20Customer::create(['Code' => 'HH00192319', 'ParentId' => 18104192, 'Name' => 'thangvm', 'CustomerType' => 0, 'Tel' => '0362024622']);
        return response("true", 200);
    }
    public function create(Request $request)
    {

        $this->SpeedService = SpeedService::getInstance();
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
            return $this->procedureChange($speed);
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
    private function procedureChange($order)
    {
        foreach ($order->bill as $i) {
            $order= $i;
            break;
        }
        // detail from bravo
        $warehouse = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $description = "";
        if ($order->mode == 3) {
            $description = "Chuyển kho";
        }
        if ($order->mode == 5) {
            $description = "Nhà cung cấp";
        }
        $data = B30AccDocItem::setData($order, $employeeid, $description);
        $acc = B30AccDocItem::create($data);
        $acc = B30AccDocItem::find($acc->Id);
        $i = 1;
        if ($order->type == 1) {
            foreach ($order->products as $item) {
                $itemInfo = B20Item::where("Code", $item->code)->get();
                if (sizeof($itemInfo) > 0) {
                    $itemInfo = $itemInfo[0];
                }
                $line = B30AccDocItem1::setData($i, $item, $itemInfo, $warehouse, $acc->Stt);
                B30AccDocItem1::create($line);
                $i++;
            }
        }
        if ($order->type == 2) {
            foreach ($order->products as $item) {
                $itemInfo = B20Item::where("Code", $item->code)->get();
                if (sizeof($itemInfo) > 0) {
                    $itemInfo = $itemInfo[0];
                }
                $line = B30AccDocItem2::setData($i, $item, $itemInfo, $warehouse, $acc->Stt);
                B30AccDocItem2::create($line);
                $i++;
            }
        }
        B30AccDocSales::runExec($acc);
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
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        $i = 1;
        $usedPoints = $order->usedPoints;
        $calcTotalMoney = $order->calcTotalMoney + $order->moneyDiscount + $usedPoints;
        $moneyDiscountPercent = $usedPoints / $calcTotalMoney;
        $allotted = 0;
        foreach ($order->products as $item) {
            $itemInfo = B20Item::where("Code", $item->productCode)->get();
            if (sizeof($itemInfo) > 0) {
                $itemInfo = $itemInfo[0];
            }
            if ($i == sizeof($order->products)) {
                $item->usedPoints = $usedPoints - $allotted;
            } else {

                $item->usedPoints = $item->price * $moneyDiscountPercent;
                $allotted = $allotted + $item->usedPoints;
            }
            $line = B30AccDocSales1::setData($i, $item, $customer, $itemInfo, $warehouse, $acc->Stt);
            B30AccDocSales1::create($line);
            $i++;
        }
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }
    private function procedureAddOrderRefund($order)
    {
        // detail from bravo
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
        foreach ($order->products as $item) {
            $itemInfo = B20Item::where("Code", $item->productCode)->get();
            if (sizeof($itemInfo) > 0) {
                $itemInfo = $itemInfo[0];
            }
            $itemAccInfo = B30AccDocSales1::where("Stt", $accSale)->where("ItemId",$itemInfo->Id)->get();
            if (sizeof($itemAccInfo) > 0) {
                $itemAccInfo = $itemAccInfo[0];
            }
            $line = B30AccDocSales2::setData($i, $item, $customer, $itemInfo, $warehouse, $acc->Stt,$itemAccInfo);
            B30AccDocSales2::create($line);
            $i++;
        }
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }
    private function procedureUpdateOrder($speed)
    {
        B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->update(['DocStatus' => B30AccDocSales::convertStatus(($speed->status))]);
        $acc = B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->get();
        foreach ($acc as $item) {
            B30AccDocSales::runExec($item);
        }
        return response("true", 200);
    }
}
