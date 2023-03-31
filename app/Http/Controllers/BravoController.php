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
        $a = 1;
        $b = 2;
        $c = $a + $b;
        return response("true", 200);
    }
    public function create(Request $request)
    {


        $speed = json_decode(json_encode($request->only(["event", "webhooksVerifyToken", "data"])), FALSE);
        $this->SpeedService = SpeedService::getInstance();
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
        return response("true", 200);
    }


    private function procedureProduct($speed)
    {
        $parentId = $speed->parentId;
        if ($parentId != null || $parentId > 0)
            return response('true', 200);
        // $product = B20Item::create(["Code"=>$speed["data"]["code"],"Name"=>$speed["data"]["name"],"Unit"=>"Chiếc","ItemType"=>2]);

        $res = $this->SpeedService->getProductDetail($speed->productId);
        foreach ($res->data as $value) {
            B20Item::create(["Code" => $value->code, "Name" => $value->name, "Unit" => "Chiếc", "ItemType" => 2, "ItemGroupCode" => "HH"]);
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
        if ($order->typeId == 1) return $this->procedureAddOrderReal($order, 1);
        if ($order->typeId == 14) return $this->procedureAddOrderRefund($order, 1);
    }







    private function procedureChange()
    {
        $data = $this->SpeedService->getWarehousing()->data->bill;
        foreach ($data as $order) {
            $check = B30AccDocItem::where("DocNo", $order->type == 1 ? 'NKN' . $order->id : 'XKN' . $order->id)->get();
            if (sizeof($check) > 0) continue;
            $description1 = "";
            $account = "";
            if ($order->mode == 2) {

                $order->moneyDiscount = $order->discount;
                $order->customerAddress = "";
                $order->customerWard = "";
                $order->customerDistrict = "";
                $order->customerCity = "";
                $order->statusCode = "Success";
                $order->customerShipFee = 0;
                $order->returnFromOrderId = property_exists($order, 'relatedBillId') ? $order->relatedBillId : 0;
                $order->usedPoints = (int)(property_exists($order, 'usedPoints') ? $order->usedPoints : 0);
                $order->discount = (int)(property_exists($order, 'discount') ? $order->discount : 0);
                $order->calcTotalMoney = $order->money - $order->usedPoints;
                $order->products = json_decode(json_encode($order->products), true);
                $order->type == 1 ?
                    $this->procedureAddOrderRefund($order, 2) :
                    $this->procedureAddOrderReal($order, 2);

                continue;
            } else
            if ($order->mode == 3) {
                $description1 = $order->description == "" ? "Chuyển kho" : "Chuyển kho" . "-" . $order->description;
                $account = "15699";
            } else
            if ($order->mode == 4) {
                $this->addGiftToAccDocSale($order);
                continue;
            } else
            if ($order->mode == 5) {
                $description1 = $order->description == "" ? "Nhà cung cấp" : "Nhà cung cấp" . "-" . $order->description;
                $account = "3311";
            } elseif ($order->mode == 8) {
                $description1 = $order->description == "" ? "Kiểm kho" : "Kiểm kho" . "-" . $order->description;
                $account = $order->type == 1 ? "1381" : "3381";
            } else continue;
            // detail from bravo
            $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
            $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
            $index = 1;
            $accDocItem1 = [];
            $accDocItem2 = [];
            if ($order->type == 1) {
                foreach ($order->products as $item) {
                    $itemInfo = B20Item::getItemByCode($item->code);
                    if ($itemInfo == null) {
                        $description1 .= "-";
                        $description1 .= $item->code;
                        continue;
                    }

                    $accDocItem1[] = B30AccDocItem1::setData($index, $item, $itemInfo, $warehouses, $account);
                    $index++;
                }
            }
            if ($order->type == 2) {
                foreach ($order->products as $item) {
                    $itemInfo = B20Item::getItemByCode($item->code);
                    if ($itemInfo == null) {
                        $description1 .= "-";
                        $description1 .= $item->code;
                        continue;
                    }

                    $accDocItem2[] = B30AccDocItem2::setData($index, $item, $itemInfo, $warehouses, $account);
                    $index++;
                }
            }
            $data = B30AccDocItem::setData($order, $employeeid, $description1);
            $acc = B30AccDocItem::create($data);
            $acc = B30AccDocItem::find($acc->Id);
            if ($order->type == 1) {
                foreach ($accDocItem1 as $sale) {
                    $sale["Stt"] = $acc->Stt;
                    B30AccDocItem1::create($sale);
                }
            }
            if ($order->type == 2) {
                foreach ($accDocItem2 as $sale) {
                    $sale["Stt"] = $acc->Stt;
                    B30AccDocItem2::create($sale);
                }
            }
            B30AccDocSales::runExec($acc);
        }
        return response("true", 200);
    }






    private function procedureAddOrderReal($order, $type)
    {

        $check = B30AccDocSales::where("DocNo", 'HDN' . $order->id)->get();
        if (sizeof($check) > 0) return response("true", 200);
        // detail from bravo
        $customer = B20Customer::getCustomer($order);
        $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $order->usedPoints = $order->usedPoints ? $order->usedPoints : 0;
        $order->moneyDiscount = $order->moneyDiscount ? $order->moneyDiscount : 0;
        $desciption = "";
        $data = B30AccDocSales::setData($order, $customer, $employeeid, $warehouses);
        $i = 1;
        $j = 1;
        $coin = new Coin($order);
        $listAccDocSale1 = [];


        foreach ($order->products as $item) {
            $item = json_decode(json_encode($item), FALSE);
            if ($type == 2) {
                $item->discount = $item->discount ? $item->discount / $item->quantity : 0;
            }
            if (!property_exists($item, 'productCode')) $item->productCode = $item->code;
            $itemInfo = B20Item::getItemByCode($item->productCode);
            if ($itemInfo == null) {
                $desciption = $item->productCode . '-' . $desciption;
                $j++;
                continue;
            }
            // set Coin
            $item->usedPoints = $coin->getCoin($j == sizeof($order->products), $item->price, $item->quantity);

            $listAccDocSale1[] = B30AccDocSales1::setData($i, $item, $customer, $itemInfo, $warehouses);
            $i++;
            $j++;
            // check sản phẩm có quà tặng kèm không nếu có thì add vào với giá 0đ
            if (property_exists($item, "giftProducts")) {
                if (sizeof($item->giftProducts) > 0) {
                    foreach ($item->giftProducts as $gifts) {
                        foreach ($gifts as $gift) {
                            $giftInfo = B20Item::getItemByCode($gift->productCode);
                            if ($giftInfo == null) {
                                $desciption = $item->productCode . '-' . $desciption;
                                continue;
                            }
                            $gift->price = 0;
                            $gift->usedPoints = 0;
                            $gift->discount = 0;
                            $gift->quantity = $gift->productQuantity;
                            $listAccDocSale1[] = B30AccDocSales1::setData($i, $gift, $customer, $giftInfo, $warehouses);
                            $i++;
                        }
                    }
                }
            }
        }
        $data["Description"] = $desciption != "" ? $desciption : "";
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        foreach ($listAccDocSale1 as $sale) {
            $sale["Stt"] = $acc->Stt;
            B30AccDocSales1::create($sale);
        }
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }





    private function procedureAddOrderRefund($order, $type)
    {
        $check = B30AccDocSales::where("DocNo", 'TLN' . $order->id)->get();
        if (sizeof($check) > 0) return response("true", 200);
        // detail from bravo
        $customer = B20Customer::getCustomer($order);
        $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $order->moneyDiscount = $order->moneyDiscount ? $order->moneyDiscount : 0;
        $data = B30AccDocSales::setDataRefund($order, $customer, $employeeid, $warehouses);

        $accSale = B30AccDocSales::where("DocNo", "HDN" . $order->returnFromOrderId)->get();
        sizeof($accSale) > 0 ? $accSale = $accSale[0]->Stt : null;
        $i = 1;
        $listAccDocSale2 = [];
        foreach ($order->products as $item) {
            $item = json_decode(json_encode($item), FALSE);
            if ($type == 2) {
                $item->discount = $item->discount ? $item->discount / $item->quantity : 0;
            }
            if (!property_exists($item, 'productCode')) $item->productCode = $item->code;
            $itemInfo = B20Item::getItemByCode($item->productCode);
            if ($itemInfo == null) {
                $order->desciption = $item->productCode . '-' . $order->desciption;
                continue;
            }

            $itemAccInfo = B30AccDocSales1::getItemByStt($accSale, $itemInfo->Id);
            if ($itemAccInfo == null) {
                $order->desciption = $item->productCode . '-' . $order->desciption;
                continue;
            }
            $listAccDocSale2[] = B30AccDocSales2::setData($i, $item, $customer, $itemInfo, $warehouses, $itemAccInfo);
            $i++;
        }
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        foreach ($listAccDocSale2 as $sale) {
            $sale["Stt"] = $acc->Stt;
            B30AccDocSales2::create($sale);
        }
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }



    private function addGiftToAccDocSale($order): void
    {
        $acc = B30AccDocSales::where('DocNo', $order->type == 1 ? 'TLN' . $order->relatedBillId : 'HDN' . $order->relatedBillId)->get();
        if (!(sizeof($acc) > 0)) return;
        $acc= $acc[0];
        $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $customer = B20Customer::getCustomer($order);
        if ($order->type == 1) {
            foreach ($order->products as $gift) {

                $giftInfo = B20Item::getItemByCode($gift->code);
                if ($giftInfo == null) continue;

                $itemAccInfo = B30AccDocSales2::getItemByStt($acc->Stt, $giftInfo->Id);
                if ($itemAccInfo != null) continue;

                $line = B30AccDocSales2::where("Stt", $acc->Stt)->get();
                $i = sizeof($line) + 1;
                if ($i > 1) {
                    $line = $line[0];
                }
                $accSale1 = B30AccDocSales1::where("RowId", $line->Stt_Hbtl)->get();
                if (!(sizeof($accSale1) > 0)) continue;
                $accSale1 = $accSale1[0];

                $accSale1 = B30AccDocSales1::where("Stt", $accSale1->Stt)->where("ItemId", $giftInfo->Id)->get();
                if (!(sizeof($accSale1) > 0)) continue;
                $accSale1 = $accSale1[0];
                $gift->price=0;
                $gift->usedPoints=0;
                $gift->discount=0;
                $sale = B30AccDocSales2::setData($i, $gift, $customer, $giftInfo, $warehouses, $accSale1);
                $sale["Stt"] = $acc->Stt;
                B30AccDocSales2::create($sale);
                B30AccDocSales::runExec($acc);
            }
        } else {
            foreach ($order->products as $gift) {

                $giftInfo = B20Item::getItemByCode($gift->code);
                if ($giftInfo == null) continue;

                $itemAccInfo = B30AccDocSales1::getItemByStt($acc->Stt, $giftInfo->Id);
                if ($itemAccInfo != null) continue;
                $i = B30AccDocSales1::where("Stt", $acc->Stt)->count();

                $gift->price=0;
                $gift->usedPoints=0;
                $gift->discount=0;
                $sale = B30AccDocSales1::setData($i + 1, $gift, $customer, $giftInfo, $warehouses);
                $sale["Stt"] = $acc->Stt;
                B30AccDocSales1::create($sale);
                B30AccDocSales::runExec($acc);
            }
        }
    }


    private function procedureUpdateOrder($speed)
    {

        B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->update(['DocStatus' => B30AccDocSales::convertStatus(($speed->status))]);
        $acc = B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->get();
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }
}

class Coin
{
    function __construct($order)
    {
        $this->usedPoints = $order->usedPoints;
        $this->moneyDiscountPercent = $order->usedPoints / ($order->calcTotalMoney + $order->moneyDiscount + $order->usedPoints);
        $this->allotted = 0;
    }
    private $usedPoints;
    private $moneyDiscountPercent;
    private $allotted;
    public function getCoin(bool $endOfList, $price, $quantity)
    {
        if ($endOfList)  return $this->usedPoints - $this->allotted;
        $coin = (int)($price * $quantity * $this->moneyDiscountPercent);
        $this->allotted = $this->allotted + $coin;
        return $coin;
    }
}
