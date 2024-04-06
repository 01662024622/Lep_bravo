<?php

namespace App\Services;

use App\ErpToken;
use App\Log;
use Carbon\Carbon;
use Doctrine\DBAL\Driver\Exception;
use Unirest\Request as Api;
use Unirest\Request\Body;

class SpeedService
{
    private static $service;
    private const TOKEN = "ZhuJP2EhyY6gW8lKAc4SNuV1oXwojPAga5B9wuPgnmzhMSPHwyYbpOMxmx3CuWuiZ11NHmB1jUELMvco327QGSMAtesICRymkTyCOpZSVokSfKtkzdmTdxn9MwvOZ8pw0lH4AVaXQ75AXkpBDotws";


    private function __construct()
    {
    }

    public static function getInstance(): SpeedService
    {
        if (static::$service == null) {
            static::$service = new SpeedService();
        }
        return static::$service;
    }

    public function getProductDetail($id)
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody($id);
        $response = Api::post('https://open.nhanh.vn//api/product/detail', $headers, $data);
        return $response->body;

    }
    public function getSubProducts($id)
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"page\":1,\"parentId\":".$id."}");
        $response = Api::post('https://open.nhanh.vn/api/product/search', $headers, $data);
        return $response->body;

    }
    public function getOrderList()
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"fromDate\":\"".Carbon::today()->format('Y-m-d')."\",\"toDate\":\"".Carbon::today()->format('Y-m-d')."\",\"dataOptions\":[\"giftProducts\"]}");
        $response = Api::post('https://open.nhanh.vn/api/order/index', $headers, $data);
        $orders= $response->body;
        return $orders;

    }
    public function getOrderDetail($id)
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"id\":".$id.",\"dataOptions\":[\"giftProducts\"]}");
        $response = Api::post('https://open.nhanh.vn/api/order/index', $headers, $data);
        $orders= $response->body;
        if (!property_exists($orders, "data")) return null;
        $orders = $orders->data->orders;
        $order = null;
        foreach ($orders as $i) {
            $order = $i;
            break;
        }

        if(!property_exists($order, 'shopOrderId')&&$order->shopOrderId!=null)$order->id=$order->shopOrderId;
        return $order;

    }
    public function getCustomerDetail($id)
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"id\":".$id."}");
        $response = Api::post('https://open.nhanh.vn/api/customer/search', $headers, $data);
        return $response->body;

    }
    public function getWarehousing()
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"modes\":[2,3,5,8],\"dataOptions\":[\"giftProducts\"],\"icpp\":20}"); // 2 lẻ- 3 là chuyển kho -4 là quà tặng kèm -5 nhà cung cấp- 8 kiểm kho
        $response = Api::post('https://open.nhanh.vn/api/bill/search', $headers, $data);
        return $response->body;
    }

    public function getCustomersPoint()
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"page\":1,\"fromLastBoughtDate\":\"" . date("Y-m-d") . "\",\"toLastBoughtDate\":\"" . date("Y-m-d") . "\"}");
        $response = Api::post('https://open.nhanh.vn/api/customer/search', $headers, $data);
        return $response->body;
    }
    private function getBody($data):string {
        return "appId=74190&version=2.0&businessId=16294&accessToken=".self::TOKEN
            ."&data=".$data;
    }
}
