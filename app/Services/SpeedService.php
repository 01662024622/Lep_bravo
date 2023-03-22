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
    private const TOKEN = "MbkK0iZX0IwRewzFLAdJ0w1X9RCEwqmFUJsIpl2kZQ1ytElyOjTmHrjBqHwNKo2ysghCOhbBhzPQ65AVT5GBzFqgBADC2WekYD1jsgQoEtmq9IyrvbVRm8TME0uaDWsV5h1eYq6Jx5Zp87lrHgcZrJtRTsXjAo8uteUbkMgdWPWAt2IMTadyet8H2JTi";


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

    public function getCustomersPoint()
    {
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $data = $this->getBody("{\"page\":1,\"fromLastBoughtDate\":\"" . date("Y-m-d") . "\",\"toLastBoughtDate\":\"" . date("Y-m-d") . "\"}");
        $response = Api::post('https://open.nhanh.vn/api/customer/search', $headers, $data);
        return $response->body;

    }
    private function getBody($data):string {
        return "appId=73008&version=2.0&businessId=16294&accessToken=".self::TOKEN
            ."&data=".$data;
    }
}
