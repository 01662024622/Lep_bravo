<?php

namespace App\Http\Controllers;

use App\B20Customer;
use App\B20Employee;
use App\B20Item;
use App\B20ItemInfo;
use App\B20Nhanh_tontucthoi;
use App\B20Product;
use App\B20Warehouse;
use App\B30AccDoc;
use App\B30AccDocApplyPrepayment;
use App\B30AccDocAtchDoc;
use App\B30AccDocItem;
use App\B30AccDocItem1;
use App\B30AccDocItem2;
use App\B30AccDocOther;
use App\B30AccDocPaybill;
use App\B30AccDocSales;
use App\B30AccDocSales1;
use App\B30AccDocSales2;
use App\Services\SpeedService;
use App\vGia;
use Illuminate\Http\Request;
use stdClass;

class BravoController extends Controller
{

    private $SpeedService;
    const STOCK = [12435, 16186, 16187, 18414, 19536, 22885, 25405, 30719, 44298, 58454, 58601, 63530, 70310, 132462, 132718, 132719, 132720, 132761, 133563];
    const BRANCH = ["", "LEP", "KEIRA-TONG", "CHUNCHILL", "BIGBAE"];

    public function get()
    {
        $data = vGia::where("WarehouseId", 18960752)->where("ItemId", 25477912)->orderBy('DocDate', 'DESC')->select("UnitCost")->first();
        return $data->UnitCost;
    }
    public function create(Request $request)
    {
        $speed = json_decode(json_encode($request->only(["event", "webhooksVerifyToken", "data"])), FALSE);
        $this->SpeedService = SpeedService::getInstance();
        if ($speed->webhooksVerifyToken != "Thangui0011@@1996")
            return response('error', 404);


        if ($speed->event == "productAdd") {
            $speed = $speed->data;
            return $this->procedureProducts($speed);
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
            $this->updateToBravo($speed->data);
            return $this->procedureChange();
        }
        if ($speed->event == "orderDelete") {
            $speed = $speed->data;
            return $this->procedureDeleteOrder($speed);
        }
        return response("true", 200);
    }


    private function procedureProducts($speed)
    {
        $parentId = $speed->parentId;
        if ($parentId != null && $parentId > 0)
            return response('true', 200);
        return $this->importProducts($speed->productId);
    }
    private function importProducts($id)
    {
        $res = $this->SpeedService->getProductDetail($id);
        $product = null;
        foreach ($res->data as $value) {
            if ($value->idNhanh == $id) {
                $product = B20Product::getItemByCode($value->code);
                if ($product == null) {
                    B20Product::create(["Code" => $value->code, "Name" => $value->name, "Unit" => "Chiếc", "ParentId" => 26757072, "ProductType" => 2, "ConvertRate1" => 1, "ConvertRate2" => 1, "IsGroup" => 0]);
                    $product = B20Product::getItemByCode($value->code);
                }
                break;
            }
        }
        foreach ($res->data as $value) {
            $check = B20Item::getItemByCode($value->code);
            if ($check == null) {
                $branch = "";
                if (property_exists($value, 'branchId') && $value->branchId > 0 && $value->branchId < 5)
                    $branch = self::BRANCH[$value->branchId];
                B20Item::create(["Code" => $value->code, "Name" => $value->name, "Linhvuc" => $branch, "Unit" => "Chiếc", "ItemType" => 1, "ItemGroupCode" => "15511", "ParentId" => 21297832]);
                $check = B20Item::getItemByCode($value->code);
                B20ItemInfo::create(["ItemId" => $check->Id, "ProductId" => $product->Id, "Weight" => 1]);
            }
        }
        return response("true", 200);
    }
    private function procedureProductFromSub($id)
    {
        $resDetail = $this->SpeedService->getProductDetail($id)->data;
        if (count((array) $resDetail) > 0) {
            foreach ($resDetail as $valueDetail) {
                if ($valueDetail->parentId > 0) {
                    $this->importProducts($valueDetail->parentId);
                    break;
                } else {
                    $this->importProducts($valueDetail->idNhanh);
                    break;
                }
            }
        }
    }



    private function procedureAddOrder($speed)
    {

        //detail order
        $orderDeatail = $this->SpeedService->getOrderDetail($speed->orderId);
        if ($orderDeatail != null) {
            if (property_exists($orderDeatail, 'shopOrderId') && $orderDeatail->shopOrderId != null && $orderDeatail->shopOrderId > 0)
                $orderDeatail->id = $orderDeatail->shopOrderId;
            if ($orderDeatail->typeId == 1)
                return $this->procedureAddOrderReal($orderDeatail, 1, 'Đơn hàng');
            if ($orderDeatail->typeId == 14)
                return $this->procedureAddOrderRefund($orderDeatail, 1, 'Đơn hàng');
        }
        $this->addListOrder($this->SpeedService);
    }
    public function addListOrder($service)
    {
        $this->SpeedService = $service;
        $orders = $this->SpeedService->getOrderList()->data->orders;
        if ($orders == null)
            return response("true", 200);
        foreach ($orders as $order) {
            if (property_exists($order, 'shopOrderId') && $order->shopOrderId != null && $order->shopOrderId > 0)
                $order->id = $order->shopOrderId;
            if ($order->typeId == 1)
                $this->procedureAddOrderReal($order, 1, 'Đơn hàng');
            if ($order->typeId == 14)
                $this->procedureAddOrderRefund($order, 1, 'Đơn hàng');
        }
        return response('true', 200);
    }






    public function procedureChangeOver($service)
    {
        $this->SpeedService = $service;
        $this->procedureChange();
    }
    private function procedureChange()
    {
        $data = $this->SpeedService->getWarehousing()->data->bill;
        foreach ($data as $order) {
            $order->products = json_decode(json_encode($order->products), true);
            $check = B30AccDocItem::where("DocNo", $order->type == 1 ? 'NKN' . $order->id : 'XKN' . $order->id)->get();
            $details = null;
            if (sizeof($check) > 0) {
                foreach ($check as $iy) {
                    $check = $iy;
                    break;
                }
                if ($order->money != $check->TotalAmount) {
                    B30AccDocItem::where("DocNo", $order->type == 1 ? 'NKN' . $order->id : 'XKN' . $order->id)->delete();
                    B30AccDocItem1::where("Stt", $check->Stt)->delete();
                    B30AccDocItem2::where("Stt", $check->Stt)->delete();
                } else {
                    if ($order->type == 1) {
                        $details = B30AccDocItem1::where("Stt", $check->Stt)->get();
                    }
                    if ($order->type == 2) {
                        $details = B30AccDocItem2::where("Stt", $check->Stt)->get();
                    }
                    if (sizeof($order->products) == sizeof($details)) {
                        continue;
                    } else {
                        B30AccDocItem::where("DocNo", $order->type == 1 ? 'NKN' . $order->id : 'XKN' . $order->id)->delete();
                        B30AccDocItem1::where("Stt", $check->Stt)->delete();
                        B30AccDocItem2::where("Stt", $check->Stt)->delete();
                    }
                }
            }
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
                $order->returnFromOrderId = property_exists($order, 'orderId') && ($order->returnFromOrderId == '' || $order->returnFromOrderId == 0) ? $order->orderId : 0;
                $order->usedPoints = (int) (property_exists($order, 'usedPoints') ? $order->usedPoints : 0);
                $order->discount = (int) (property_exists($order, 'discount') ? $order->discount : 0);
                $order->moneyTransfer = (int) (property_exists($order, 'moneyTransfer') ? $order->moneyTransfer : 0);
                $order->moneyDeposit = (int) (property_exists($order, 'moneyDeposit') ? $order->moneyDeposit : 0);
                $order->calcTotalMoney = $order->money - $order->usedPoints - $order->moneyTransfer - $order->moneyDeposit;
                $order->type == 1 ?
                    $this->procedureAddOrderRefund($order, 2, "Bán lẻ") :
                    $this->procedureAddOrderReal($order, 2, "Bán lẻ");
                continue;
            } else
                if ($order->mode == 3) {
                    $order->description = $order->description == "" ? "Chuyển kho" : "Chuyển kho" . "-" . $order->description;
                    $account = "15699";
                } else
                    if ($order->mode == 4) {
                        $this->addGiftToAccDocSale($order);
                        continue;
                    } else
                        if ($order->mode == 5) {
                            $order->description = $order->description == "" ? "Nhà cung cấp" : "Nhà cung cấp" . "-" . $order->description;
                            $account = "3311";
                        } elseif ($order->mode == 8) {
                            $order->description = $order->description == "" ? "Kiểm kho" : "Kiểm kho" . "-" . $order->description;
                            $account = $order->type == 1 ? "1381" : "3381";
                        } else
                            continue;
            // detail from bravo
            $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
            $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
            $index = 1;
            $accDocItem = [];
            if ($order->type == 1) {
                foreach ($order->products as $item) {
                    $item = (object) $item;
                    $itemInfo = B20Item::getItemByCode($item->code);
                    if ($itemInfo == null) {
                        $this->procedureProductFromSub($item->id);
                        $itemInfo = B20Item::getItemByCode($item->code);
                        if ($itemInfo == null) {
                            $order->description .= "-";
                            $order->description .= $item->code;
                            continue;
                        }
                    }

                    if ($order->mode == 8) {
                        $itemPrice = vGia::where("WarehouseId", $warehouses ? $warehouses->HH->Id : '20354472')->where("ItemId", $itemInfo->Id)->orderBy('DocDate', 'DESC')->select("UnitCost")->first();
                        $item->price = $itemPrice != null && property_exists($itemPrice, 'UnitCost') ? $itemPrice->UnitCost : 0;
                    }
                    $accDocItem[] = B30AccDocItem1::setData($index, $item, $itemInfo, $warehouses, $account);
                    $index++;
                }
            }
            if ($order->type == 2) {
                foreach ($order->products as $item) {
                    $item = (object) $item;
                    if ($order->mode == 8) {
                        $itemPrice = vGia::where("WarehouseId", $warehouses ? $warehouses->HH->Id : '20354472')->where("ItemId", $itemInfo->Id)->orderBy('DocDate', 'DESC')->select("UnitCost")->first();
                        $item->price = $itemPrice != null && property_exists($itemPrice, 'UnitCost') ? $itemPrice->UnitCost : 0;
                    }
                    $itemInfo = B20Item::getItemByCode($item->code);
                    if ($itemInfo == null) {
                        if ($itemInfo == null) {
                            $this->procedureProductFromSub($item->id);
                            $itemInfo = B20Item::getItemByCode($item->code);
                            if ($itemInfo == null) {
                                $order->description .= "-";
                                $order->description .= $item->code;
                                continue;
                            }
                        }
                    }
                    $accDocItem[] = B30AccDocItem2::setData($index, $item, $itemInfo, $warehouses, $account);
                    $index++;
                }
            }
            if ($order->mode == 3 && $order->type == 1) {
                B30AccDocItem::where("Loai_Ps", $order->requirementBillId)->update(['WorkProcessCode' => 'CD1']);
            }
            $data = B30AccDocItem::setData($order, $employeeid, $warehouses);
            $acc = B30AccDocItem::create($data);
            $acc = B30AccDocItem::find($acc->Id);
            if ($order->type == 1) {
                foreach ($accDocItem as $sale) {
                    $sale["Stt"] = $acc->Stt;
                    B30AccDocItem1::create($sale);
                }

                B30AccDocSales::runExec($acc, 'PN');
            }
            if ($order->type == 2) {
                foreach ($accDocItem as $sale) {
                    $sale["Stt"] = $acc->Stt;
                    B30AccDocItem2::create($sale);
                }
                B30AccDocSales::runExec($acc, 'PX');
            }
        }
        return response("true", 200);
    }






    private function procedureAddOrderReal($order, $type, $typeDoc)
    {

        if (!property_exists($order, 'customerShipFee'))
            $order->customerShipFee = 0;

        $check = B30AccDocSales::where("DocNo", 'HDN' . $order->id)->get();
        if (sizeof($check) > 0)
            return response("true", 200);
        // detail from bravo
        $customer = B20Customer::getCustomer($order);
        $this->getCustomerLevelId($order->customerId, $customer);
        if (!property_exists($order, 'saleChannel'))
            $order->saleChannel = 1;
        $check = B30AccDocSales::where("DocNo", 'HDN' . $order->id)->get();
        $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        if ($warehouses != null && ($order->saleChannel == 41 || $order->saleChannel == 42 || $order->saleChannel == 43))
            $warehouses->HH->ClassCode2 = "1319";
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $order->usedPoints = $order->usedPoints ? $order->usedPoints : 0;
        $order->moneyDiscount = $order->moneyDiscount ? $order->moneyDiscount : 0;
        $order->moneyTransfer = property_exists($order, 'moneyTransfer') && $order->moneyTransfer !== null ? $order->moneyTransfer : 0;
        $order->moneyDeposit = property_exists($order, 'moneyDeposit') && $order->moneyDeposit !== null ? $order->moneyDeposit : 0;
        $order->calcTotalMoney = $order->calcTotalMoney == null ? abs($order->calcTotalMoney) + $order->moneyTransfer + $order->moneyDeposit : $order->moneyDeposit + $order->moneyTransfer;
        if ($order->saleChannel == 41) {
            if ($warehouses != null) {
                $warehouses->HH->ClassCode2 = '131H';
                $warehouses->HH->ClassCode3 = '20356992';
            }
            $order->description = "Lazada-" . $order->description;
        } elseif ($order->saleChannel == 42) {

            if ($warehouses != null) {
                $warehouses->HH->ClassCode2 = '131S';
                $warehouses->HH->ClassCode3 = '20356872';
            }
            $order->description = "Shopee-" . $order->description;
        } elseif ($order->saleChannel == 43) {

            $order->description = "Sendo-" . $order->description;
        } elseif ($order->saleChannel == 44) {

            $order->description = "Tiki-" . $order->description;
        } elseif ($order->saleChannel == 48) {

            $order->description = "Tiktok -" . $order->description;
        } else {
            $order->description = $typeDoc . "-" . $order->description;
        }

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
            if (!property_exists($item, 'productCode'))
                $item->productCode = $item->code;
            $itemInfo = B20Item::getItemByCode($item->productCode);

            if ($itemInfo == null) {
                $this->procedureProductFromSub($item->id);
                $itemInfo = B20Item::getItemByCode($item->code);
                if ($itemInfo == null) {
                    $order->description = $item->productCode . '-' . $order->description;
                    $j++;
                    continue;
                }
            }
            // set Coin
            $item->usedPoints = $coin->getCoin($j == sizeof($order->products), $item->price, $item->quantity);

            $listAccDocSale1[] = B30AccDocSales1::setData($i, $item, $customer, $itemInfo, $warehouses);
            $i++;
            $j++;
            if (property_exists($order, 'mode') && $order->mode == 2) {
                // check sản phẩm có quà tặng kèm không nếu có thì add vào với giá 0đ
                if (property_exists($item, "giftProducts")) {
                    if (sizeof($item->giftProducts) > 0) {
                        foreach ($item->giftProducts as $gift) {
                            $giftInfo = B20Item::getItemByCode($gift->productCode);
                            if ($giftInfo == null) {
                                $this->procedureProductFromSub($gift->productId);
                                $giftInfo = B20Item::getItemByCode($gift->productCode);
                                if ($giftInfo == null) {
                                    $order->description = $gift->productCode . '-' . $order->description;
                                    continue;
                                }
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
            } else {
                // check sản phẩm có quà tặng kèm không nếu có thì add vào với giá 0đ
                if (property_exists($item, "giftProducts")) {
                    if (sizeof($item->giftProducts) > 0) {
                        foreach ($item->giftProducts as $gifts) {
                            foreach ($gifts as $gift) {
                                $giftInfo = B20Item::getItemByCode($gift->productCode);
                                if ($giftInfo == null) {
                                    $this->procedureProductFromSub($gift->productId);
                                    $giftInfo = B20Item::getItemByCode($gift->productCode);
                                    if ($giftInfo == null) {
                                        $order->description = $gift->productCode . '-' . $order->description;
                                        continue;
                                    }
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
        }

        $Amount9 = 0;
        foreach ($listAccDocSale1 as $obj) {
            if (isset($obj->Amount9)) {
                $Amount9 += $obj->Amount9;
            }
        }
        $data['TotalOriginalAmount0'] = $Amount9;
        $data['TotalAmount0'] = $Amount9;
        $data['TotalOriginalAmount'] = $Amount9 - $data['TotalOriginalAmount4'];
        $data['TotalAmount'] = $Amount9 - $data['TotalAmount4'];

        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        foreach ($listAccDocSale1 as $sale) {
            $sale["Stt"] = $acc->Stt;
            B30AccDocSales1::create($sale);
        }
        if ($order->customerShipFee != null && $order->customerShipFee > 0) {
            $shipInfor = B20Item::getItemByCode("VANCHUYEN");
            $ship = new stdClass();
            $ship->price = $order->customerShipFee;
            $ship->usedPoints = 0;
            $ship->discount = 0;
            $ship->quantity = 1;
            $shipSave = B30AccDocSales1::setData($i, $ship, $customer, $shipInfor, $warehouses);
            $shipSave["Stt"] = $acc->Stt;
            B30AccDocSales1::create($shipSave);
        }
        B30AccDocAtchDoc::create(B30AccDocAtchDoc::setData($order, $customer, $warehouses, $acc->Stt));
        B30AccDocSales::runExec($acc);
        return response("true", 200);
    }





    private function procedureAddOrderRefund($order, $type, $typeDoc)
    {

        if (!property_exists($order, 'customerShipFee'))
            $order->customerShipFee = 0;
        $check = B30AccDocSales::where("DocNo", 'TLN' . $order->id)->get();
        if (sizeof($check) > 0)
            return response("true", 200);
        // detail from bravo
        $customer = B20Customer::getCustomer($order);
        $this->getCustomerLevelId($order->customerId, $customer);
        $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $employeeid = $order->saleId ? B20Employee::getEmployee($order->saleId) : null;
        $order->moneyDiscount = $order->moneyDiscount ? $order->moneyDiscount : 0;
        $order->moneyTransfer = property_exists($order, 'moneyTransfer') && $order->moneyTransfer ? $order->moneyTransfer : 0;
        $order->calcTotalMoney = $order->calcTotalMoney == null ? abs($order->calcTotalMoney) + $order->moneyTransfer : $order->moneyTransfer;
        $order->description = $typeDoc . "-" . $order->description;
        $data = B30AccDocSales::setDataRefund($order, $customer, $employeeid, $warehouses);
        if (!property_exists($order, 'returnFromOrderId') || $order->returnFromOrderId == 0)
            $order->returnFromOrderId = $order->relatedBillId;
        $accSales = B30AccDocSales::where("DocNo", "HDN" . $order->returnFromOrderId)->get();
        $accSale = 0;
        if (sizeof($accSales) > 0) {
            $accSale = $accSales[0]->Stt;
            $accSales = $accSales[0];
        } else {
            return response("true", 200);
        }
        $i = 1;
        $listAccDocSale2 = [];
        $debitAcount = "";
        $creditAcount = $warehouses ? $warehouses->HH->ClassCode2 : '1311';
        foreach ($order->products as $item) {
            $item = json_decode(json_encode($item), FALSE);
            if ($type == 2) {
                $item->discount = $item->discount ? $item->discount / $item->quantity : 0;
            }
            if (!property_exists($item, 'productCode'))
                $item->productCode = $item->code;
            $itemInfo = B20Item::getItemByCode($item->productCode);
            if ($itemInfo == null) {
                $this->procedureProductFromSub($item->productId);
                $itemInfo = B20Item::getItemByCode($item->productCode);
                if ($itemInfo == null) {
                    $order->description = $item->productCode . '-' . $order->description;
                    continue;
                }
            }

            $itemAccInfo = B30AccDocSales1::getItemByStt($accSale, $itemInfo->Id);
            if ($itemAccInfo == null) {
                $order->description = $item->productCode . '-' . $order->description;
                continue;
            }
            if ($debitAcount == "")
                $debitAcount = $itemAccInfo->DebitAccount2;
            $listAccDocSale2[] = B30AccDocSales2::setData($i, $item, $customer, $itemInfo, $warehouses, $itemAccInfo);
            $i++;

            if (property_exists($order, 'mode') && $order->mode == 2) {
                if (property_exists($item, "giftProducts")) {
                    if (sizeof($item->giftProducts) > 0) {
                        foreach ($item->giftProducts as $gift) {
                            $giftInfo = B20Item::getItemByCode($gift->productCode);
                            if ($giftInfo == null) {
                                $this->procedureProductFromSub($gift->productId);
                                $giftInfo = B20Item::getItemByCode($gift->productCode);
                                if ($giftInfo == null) {
                                    $order->description = $gift->productCode . '-' . $order->description;
                                    continue;
                                }
                            }
                            $gift->price = 0;
                            $gift->usedPoints = 0;
                            $gift->discount = 0;
                            $gift->quantity = $gift->productQuantity;

                            $itemAccInfog = B30AccDocSales1::getItemByStt($accSale, $giftInfo->Id);
                            $listAccDocSale2[] = B30AccDocSales2::setData($i, $gift, $customer, $giftInfo, $warehouses, $itemAccInfog);
                            $i++;
                        }
                    }
                }
            } else {
                if (property_exists($item, "giftProducts")) {
                    if (sizeof($item->giftProducts) > 0) {
                        foreach ($item->giftProducts as $gifts) {
                            foreach ($gifts as $gift) {
                                $giftInfo = B20Item::getItemByCode($gift->productCode);
                                if ($giftInfo == null) {
                                    $this->procedureProductFromSub($gift->productId);
                                    $giftInfo = B20Item::getItemByCode($gift->productCode);
                                    if ($giftInfo == null) {
                                        $order->description = $gift->productCode . '-' . $order->description;
                                        continue;
                                    }
                                }
                                $gift->price = 0;
                                $gift->usedPoints = 0;
                                $gift->discount = 0;
                                $gift->quantity = $gift->productQuantity;

                                $itemAccInfog = B30AccDocSales1::getItemByStt($accSale, $giftInfo->Id);
                                $listAccDocSale2[] = B30AccDocSales2::setData($i, $gift, $customer, $giftInfo, $warehouses, $itemAccInfog);
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        $acc = B30AccDocSales::create($data);
        $acc = B30AccDocSales::find($acc->Id);
        $Stt = $acc->Stt;
        foreach ($listAccDocSale2 as $sale) {
            $sale["Stt"] = $Stt;
            B30AccDocSales2::create($sale);
        }

        B30AccDocSales::runExec($acc, "TL");

        // bù trừ công nợ
        if ($debitAcount != $creditAcount) {
            $btn = B30AccDoc::setData($order);
            $btnm = B30AccDoc::create($btn);
            $btnm = B30AccDoc::find($btnm->Id);
            B30AccDocOther::create(B30AccDocOther::setData($accSales, $acc, $debitAcount, $creditAcount, $btnm->Stt));

            $AtchDoc = B30AccDocAtchDoc::create(B30AccDocAtchDoc::setData($order, $customer, $warehouses, $btnm->Stt));
            $AtchDoc = B30AccDocAtchDoc::find($AtchDoc->Id);
            B30AccDocApplyPrepayment::create(B30AccDocApplyPrepayment::setData($order, $AtchDoc));
            B30AccDocPaybill::create(B30AccDocPaybill::setData($order, $AtchDoc));
            B30AccDocSales::runExec($btnm, "BT");
        }
        return response("true", 200);
    }



    private function addGiftToAccDocSale($order): void
    {
        $acc = B30AccDocSales::where('DocNo', $order->type == 1 ? 'TLN' . $order->relatedBillId : 'HDN' . $order->relatedBillId)->get();
        if (!(sizeof($acc) > 0))
            return;
        $acc = $acc[0];
        $warehouses = $order->depotId ? B20Warehouse::getWarehouse($order->depotId) : null;
        $customer = B20Customer::getCustomer($order);
        if ($order->type == 1) {
            foreach ($order->products as $gift) {

                $giftInfo = B20Item::getItemByCode($gift->code);
                if ($giftInfo == null) {
                    $this->procedureProductFromSub($gift->id);
                    $giftInfo = B20Item::getItemByCode($gift->code);
                    if ($giftInfo == null) {
                        continue;
                    }
                }

                $itemAccInfo = B30AccDocSales2::getItemByStt($acc->Stt, $giftInfo->Id);
                if ($itemAccInfo != null)
                    continue;

                $line = B30AccDocSales2::where("Stt", $acc->Stt)->get();
                $i = sizeof($line) + 1;
                if ($i > 1) {
                    $line = $line[0];
                }
                $accSale1 = B30AccDocSales1::where("RowId", $line->Stt_Hbtl)->get();
                if (!(sizeof($accSale1) > 0))
                    continue;
                $accSale1 = $accSale1[0];

                $accSale1 = B30AccDocSales1::where("Stt", $accSale1->Stt)->where("ItemId", $giftInfo->Id)->get();
                if (!(sizeof($accSale1) > 0))
                    continue;
                $accSale1 = $accSale1[0];
                $gift->price = 0;
                $gift->usedPoints = 0;
                $gift->discount = 0;
                $sale = B30AccDocSales2::setData($i, $gift, $customer, $giftInfo, $warehouses, $accSale1);
                $sale["Stt"] = $acc->Stt;
                B30AccDocSales2::create($sale);
                B30AccDocSales::runExec($acc);
            }
        } else {
            foreach ($order->products as $gift) {

                $giftInfo = B20Item::getItemByCode($gift->code);
                if ($giftInfo == null) {
                    $this->procedureProductFromSub($gift->id);
                    $giftInfo = B20Item::getItemByCode($gift->code);
                    if ($giftInfo == null) {
                        continue;
                    }
                }

                $itemAccInfo = B30AccDocSales1::getItemByStt($acc->Stt, $giftInfo->Id);
                if ($itemAccInfo != null)
                    continue;
                $i = B30AccDocSales1::where("Stt", $acc->Stt)->count();

                $gift->price = 0;
                $gift->usedPoints = 0;
                $gift->discount = 0;
                $sale = B30AccDocSales1::setData($i + 1, $gift, $customer, $giftInfo, $warehouses);
                $sale["Stt"] = $acc->Stt;
                B30AccDocSales1::create($sale);
                B30AccDocSales::runExec($acc);
            }
        }
    }


    private function procedureUpdateOrder($speed)
    {

        $order = $this->SpeedService->getOrderDetail($speed->orderId);
        if (property_exists($order, 'shopOrderId') && $order->shopOrderId != null)
            $order->id = $order->shopOrderId;
        if ($order == null)
            return response("true", 200);
        if ($order->typeId == 1) {
            $olderOrder = B30AccDocSales::where("DocNo", 'HDN' . $speed->orderId)->get();
            foreach ($olderOrder as $i) {
                $olderOrder = $i;
                break;
            }
            if ($olderOrder->TotalAmount != $order->calcTotalMoney) {
                B30AccDocAtchDoc::where('Stt', $olderOrder->Stt)->delete();
                B30AccDocSales::where("DocNo", 'HDN' . $speed->orderId)->delete();
                B30AccDocSales::runExec($olderOrder);
                return $this->procedureAddOrderReal($order, 1, "Đơn hàng");
            }

            $accDocSales = B30AccDocSales1::where("Stt", $olderOrder->Stt)->get();
            foreach ($order->products as $p) {
                $olderOrderSale = B20Item::where("Code", $p->productCode)->get();
                foreach ($olderOrderSale as $j) {
                    foreach ($accDocSales as $accDocSale) {
                        if ($accDocSale->ItemId == $j->Id && $accDocSale->Quantity != $p->quantity) {
                            B30AccDocAtchDoc::where('Stt', $olderOrder->Stt)->delete();
                            B30AccDocSales::where("DocNo", 'HDN' . $speed->orderId)->delete();
                            B30AccDocSales::runExec($olderOrder);
                            return $this->procedureAddOrderReal($order, 1, "Đơn hàng");
                        }
                    }
                    break;
                }
            }
            B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->update(['DocStatus' => B30AccDocSales::convertStatus(($speed->status))]);
            $acc = B30AccDocSales::where('DocNo', 'HDN' . $speed->orderId)->get();
            foreach ($acc as $i) {
                $acc = $i;
                break;
            }
            B30AccDocSales::runExec($acc);
            return response("true", 200);
        } else if ($order->typeId == 14) {
            $olderOrder = B30AccDocSales::where("DocNo", 'TLN' . $speed->orderId)->get();
            foreach ($olderOrder as $i) {
                $olderOrder = $i;
                break;
            }
            if ($olderOrder->TotalAmount != $order->calcTotalMoney) {
                B30AccDocSales::where("DocNo", 'TLN' . $speed->orderId)->delete();
                B30AccDocSales::runExec($olderOrder);
                return $this->procedureAddOrderRefund($order, 1, "Đơn hàng");
            }

            $accDocSales = B30AccDocSales2::where("Stt", $olderOrder->Stt)->get();
            foreach ($order->products as $p) {
                $olderOrderSale = B20Item::where("Code", $p->productCode)->get();
                foreach ($olderOrderSale as $j) {
                    foreach ($accDocSales as $accDocSale) {

                        if ($accDocSale->ItemId == $j->Id && $accDocSale->Quantity != $p->quantity) {
                            B30AccDocSales::where("DocNo", 'TLN' . $speed->orderId)->delete();
                            B30AccDocSales::runExec($olderOrder);
                            return $this->procedureAddOrderRefund($order, 1, "Đơn hàng");
                        }
                    }
                    break;
                }
            }
            return response("true", 200);
        }
    }
    private function procedureDeleteOrder($speed)
    {
        foreach ($speed as $orderId) {

            $accs = B30AccDocSales::where("DocNo", 'TLN' . $orderId)->orWhere("DocNo", 'HDN' . $orderId)->get();
            B30AccDocSales::where("DocNo", 'TLN' . $orderId)->orWhere("DocNo", 'HDN' . $orderId)->update(['DocStatus' => 15]);
            foreach ($accs as $acc)
                B30AccDocSales::runExec($acc);
        }
        return response("true", 200);
    }

    private function updateToBravo($data)
    {
        $data = (object) $data;
        foreach ($data as $val) {
            $array = [];
            foreach ($val->depots as $key => $depot) {
                if (!in_array($key, self::STOCK))
                    continue;
                $array['K' . $key] = $depot->available;
            }
            $check = B20Nhanh_tontucthoi::Where("ItemId", $val->id)->first();
            if ($check == null) {
                $array["ItemId"] = $val->id;
                $array["Code"] = $val->code;
                B20Nhanh_tontucthoi::create($array);
            } else {
                B20Nhanh_tontucthoi::where("ItemId", $val->id)->update($array);
            }

        }
    }
    private function getCustomerLevelId($id, $customer)
    {
        $customerSpeeds = $this->SpeedService->getCustomerDetail($id);

        if (!property_exists($customerSpeeds, "data"))
            return;
        $customerSpeeds = $customerSpeeds->data->customers;
        foreach ($customerSpeeds as $i) {
            $customer->update(['ParentId' => $this->convertParentId(12)]);
        }
    }
    private function convertParentId($id): int
    {
        switch ($id) {
            case 11:
                return 12;
            case 12:
                return 13;
            default:
                return 11;
        }
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
        if ($this->usedPoints == $this->allotted)
            return 0;
        if ($endOfList)
            return $this->usedPoints - $this->allotted;
        $coin = (int) ($price * $quantity * $this->moneyDiscountPercent);
        $this->allotted = $this->allotted + $coin;
        if ($this->usedPoints - $this->allotted < 10)
            $coin = $coin + $this->usedPoints - $this->allotted;
        return $coin;
    }
}
