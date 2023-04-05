<?php

namespace App\Services;

use App\ErpToken;
use App\Log;
use Doctrine\DBAL\Driver\Exception;
use Unirest\Request as Api;
use Unirest\Request\Body;

class SpeedService
{
    private static $service;
    private const TOKEN = "7anjS4b4hxRKM6KlBVeNCWWRYtcPZ8T74EWHYeVqIMLl4OmAtNTAV6mbYZ9R7QC4kh1lROEgUT52qEeeW9PO3GYmMFSve5UNYc3ZMyYoNQKDTkl1vJaGZSt1R4ziD7ElUelow8QJUZSlS2PXMPAjuYtEAMjp77Ufb";


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
    public function getOrderDetail($id)
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"id\":".$id.",\"dataOptions\":[\"giftProducts\"]}");
        $response = Api::post('https://open.nhanh.vn/api/order/index', $headers, $data);
        return $response->body;

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
        $data = $this->getBody("{\"type\":1,\"modes\":[2,3,4,5,8],\"dataOptions\":[\"giftProducts\"]}"); // 2 lẻ- 3 là chuyển kho -4 là quà tặng kèm -5 nhà cung cấp- 8 kiểm kho
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
        return "appId=73363&version=2.0&businessId=16294&accessToken=".self::TOKEN
            ."&data=".$data;
    }
}
