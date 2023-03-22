<?php

namespace App\Services;

use App\ErpToken;
use App\Log;
use Doctrine\DBAL\Driver\Exception;
use Unirest\Request as Api;
use Unirest\Request\Body;

class ERPService
{
    public static $service;
    private const USER = "admin";
    private const PW = "123456a@";
    private $token = "";


    private function __construct()
    {
        $oldToken = ErpToken::latest()->first();
        $this->token = str_replace("\"", "",$oldToken->token);
    }


    public static function getInstance(): ERPService
    {
        if (static::$service == null) {
            static::$service = new ERPService();
        }

        return static::$service;
    }


    public function getProduct(int $id, bool $check = true)
    {
        $headers = array('Authorization' => 'Bearer ' . $this->token);

        $response = Api::get('https://api.lep.vn/v1/products/' . $id, $headers);
        $res = $response->body;
        if (property_exists($res, 'data')) {
            return $response->body->data->products;
        }
        Log::create(['content' => 'error', 'description' => 'get product ' . $id]);
        if ($check) {
            if ($this->token == '') return null;
            return $this->getProduct($id, false);
        }
        return null;
    }
    private function getCustomer($phone)
    {
        $headers = array('Authorization' => 'Bearer ' .$this->token, 'Content-Type' => 'application/json');
        $response = Api::get('https://api.lep.vn/v1/users?limit=20&skip=0&keyword='.$phone, $headers);
        return $response->body;

    }
    public function updatePoint($customer)
    {
        $headers = array('Authorization' => 'Bearer ' .$this->token,'Content-Type' => 'application/json');
        $data = "{\n" .
            "  \"type\": \"individual\",\n" .
            "  \"name\": \"".$customer->name."\",\n" .
            "  \"phone\": \"".$customer->phone."\",\n" .
            "  \"group\": {\n" .
            "    \"id\": 6,\n" .
            "    \"name\": \"MEMBER\"\n" .
            "  },\n" .
            "  \"stores\": [\n" .
            "\n" .
            "  ],\n" .
            "  \"gender\": \"female\",\n" .
            "  \"country\": \"vn\",\n" .
            "  \"permissions\": [\n" .
            "    \"user\"\n" .
            "  ],\n" .
            "  \"total_point\": ".$customer->coin.",\n" .
            "  \"status\": \"active\"\n" .
            "}";
        if(empty($customer->eId)){
            $response = Api::post('https://api.lep.vn/v1/users', $headers, $data);
        }else{
            $response = Api::put('https://api.lep.vn/v1/users/'.$customer->eId, $headers, $data);
        }
        return $response->body;
    }
    public function getListSubOfProduct($id)
    {
        $headers = array('Authorization' => 'Bearer ' .$this->token, 'Content-Type' => 'application/json');
        $response = Api::get('https://api.lep.vn/v1/products/'.$id, $headers);
        return $response->body;

    }
    public function createProduct($data)
    {
        $headers = array('Authorization' => 'Bearer ' .$this->token, 'Content-Type' => 'application/json');
        $body = Body::json($data);
        $response = Api::post('https://api.lep.vn/v1/products', $headers, $body);
        return $response->body;

    }


    private function login()
    {
        try {
            $headers = array('Content-Type' => 'application/json');
            $data = array('username' => self::USER, 'password' => self::PW, 'service' => 'staff');

            $body = Body::json($data);
            $response = Api::post('https://api.lep.vn/v1/auth/login-password?group=web', $headers, $body);
            $res = $response->body;
            if (property_exists($res, 'data')) {
                $this->token = json_encode(str_replace("\"", "", $response->body->data->token->access_token));
                ErpToken::create(['token' => $this->token]);
            }
            Log::create(['content' => 'error', 'description' => 'login false']);

        } catch (Exception $e) {}
    }
    public function checkToken():bool
    {
        if($this->token=="") return false;
        return true;
    }
    public function upDateInventory($stock, $product)
    {
        $headers = array('Authorization' => 'Bearer ' .$this->token, 'Content-Type' => 'application/json');
        $products = array($product);
        $data = array('note' => 'Phiếu kiểm hàng tự động', 'store' => $stock, 'status' => 'completed', 'products' => $products);

        $body = Body::json($data);
        $response = Api::post('https://api.lep.vn/v1/stock-takes', $headers, $body);
        $res = $response->body;

    }
    public function getToken():string
    {
        return $this->token;
    }

}
