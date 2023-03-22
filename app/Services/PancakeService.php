<?php

namespace App\Services;

use App\PancakeItemMap;
use Carbon\Carbon;
use Unirest\Request as Api;

class PancakeService
{
    private const TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiI1MDM1MGMwMy00NGQ5LTQxMWItOTk4OS1jODgyMjA5MjRhOWIiLCJpYXQiOjE2NjcxODQyOTEsImZiX25hbWUiOiJWxakgVGjhuq9uZyIsImZiX2lkIjoiMTQyMTU4NzA2NTI2NTk1IiwiZXhwIjoxNjc0OTYwMjkxfQ.JFxpc_Jxz3SSH7KKIirACH8JYItTkJJ2g9gQIVQVBYA";
    private static $service;

    private function __construct()
    {
    }

    public static function getInstance(): PancakeService
    {
        if (static::$service == null) {
            static::$service = new PancakeService();
        }
        return static::$service;
    }

    public function updateInventory($id, $available)
    {
        $item = PancakeItemMap::where('n_id', $id)->first();
        if ($item && $item->inventory == $available) {
            return;
        }
        $item = $this->getProduct($id);
        $res = $this->createInventory($item->p_id, $available, $item->inventory);
        if (property_exists($res, 'data')) {
            $this->changeStatusInventory($res->data->id);
            $item->inventory = $available;
            $item->save();
        }
    }

    public function createInventory(string $id, int $available, int $current)
    {
        $url = "https://pos.pages.fm/api/v1/shops/268808/stocktakings?access_token=" . self::TOKEN;
        $data = "{
                  \"stocktaking\": {
                    \"warehouse_id\": \"64c185f4-a7c3-417d-a514-38b624f4a0f2\",
                    \"items\": [
                      {
                        \"variation_id\": \"" . $id . "\",
                        \"changed_quantity\": " . ($available - $current) . ",
                        \"current_quantity\": " . $current . ",
                        \"quantity\": " . $available . "
                      }
                    ],
                    \"note\": \"\",
                    \"shop_id\": \"268808\",
                    \"status\": 0,
                    \"is_batch_shelf\": false,
                    \"stocktaking_at\": \"" . Carbon::now() . "\"
                  }
                }";
        $response = Api::post($url, array('Content-Type' => 'application/json'), $data);
        return $response->body;
    }

    public function changeStatusInventory($id)
    {
        $url = "https://pos.pages.fm/api/v1/shops/268808/stocktakings/" . $id . "?access_token=" . self::TOKEN;
        $response = Api::get($url)->body;
        if (property_exists($response, 'data')) {
            $response->data->status = 1;
            $headers = array('Content-Type' => 'application/json');
            $stocktaking = json_encode($response->data);
            Api::put($url, $headers, "{\"stocktaking\": " . $stocktaking . "}");
        }
    }

    private function getProduct($id): PancakeItemMap
    {
        $item = PancakeItemMap::where('n_id', $id)->first();
        if ($item && $item->p_id) {
            return $item;
        }
        if ($item) {
            $url = "https://pos.pages.fm/api/v1/shops/268808/products?access_token=" . self::TOKEN .
                "&page=1&page_size=100&nearly_out=false&limit_quantity_to_warn=false&last_imported_price=false&is_promotion=false&search=" . $item->code;
            $response = Api::get($url);
            $response = $response->body;
            if (property_exists($response, 'data') && property_exists($response, 'total_entries') && $response->total_entries > 0) {
                $item->p_id = $response->data[0]->variations[0]->id;
                $item->save();
                return $item;
            }
        }
        return $this->createProduct($id);
    }

    private function createProduct($id) : PancakeItemMap
    {
        $service = SpeedService::getInstance();
        $res = $service->getProductDetail($id);
        if (property_exists($res, 'data')) {
            foreach ($res->data as $key => $value) {

                $size = substr($value->code, -1);
                if ($size != "S" && $size != "M" && $size != "L") {
                    $size = "";
                } else {
                    $size = "size:" . $size;
                }
                $quantity = 0;
                if (property_exists($value->inventory, 'depots')) {
                    if (property_exists($value->inventory->depots, '133563')) {
                        $quantity = $value->inventory->depots["133563"]->available;
                    }
                }
                if ((int)$key == $id) {
                    $data = array(
                        "params[data][product][new_product_id]" => $value->code,
                        "params[data][product][product_name]" => $value->name,
                        "params[data][variations][0][warehouse_id]" => "64c185f4-a7c3-417d-a514-38b624f4a0f2",
                        "params[data][variations][0][fields]" => $size,
                        "params[data][variations][0][weight]" => "300",
                        "params[data][variations][0][retail_price]" => $value->price,
                        "params[data][variations][0][remain_quantity]" => $quantity,
                        "params[is_kiotviet]" => "false",
                        "params[is_auto_gen]" => "true",
                        "params[is_custom_gen]" => "false"
                    );

                    $this->createProductApi($data);

                    PancakeItemMap::create(['n_id' => $value->idNhanh, 'n_parent_code' => "", 'n_parent_name' => "", 'code' => $value->code, 'name' => $value->name, 'price' => $value->price, 'inventory' => $quantity, 'p_id', 'push' => 1]);
                }
            }
        }

        return $this->getProduct($id);
    }


    public function procedureCreateProduct($speed,$image)
    {

        $size = substr($speed->code, -1);
        if ($size != "S" && $size != "M" && $size != "L") {
            $size = "";
        } else {
            $size = "size:" . $size;
        }
        $data = array(
            "params[data][product][new_product_id]" => $speed->code,
            "params[data][product][product_name]" => $speed->name,
            "params[data][variations][0][warehouse_id]" => "64c185f4-a7c3-417d-a514-38b624f4a0f2",
            "params[data][variations][0][fields]" => $size,
            "params[data][variations][0][weight]" => "300",
            "params[data][variations][0][retail_price]" => $speed->price,
            "params[data][variations][0][remain_quantity]" => 0,
            "params[is_kiotviet]" => "false",
            "params[is_auto_gen]" => "true",
            "params[is_custom_gen]" => "false",

        );
        if($image!="")
            $data["params[data][variations][0][images][0]"]=$image;
        $this->createProductApi($data);

        PancakeItemMap::create(['n_id' => $speed->idNhanh, 'n_parent_code' => "", 'n_parent_name' => "", 'code' => $speed->code, 'name' => $speed->name, 'price' => $speed->price, 'inventory' => 0, 'p_id', 'push' => 1]);

    }

    public function createProductApi($data)
    {
        $headers = array('Accept' => '*/*');
        $body = Api\Body::multipart($data);
        $url = 'https://pos.pages.fm/api/v1/shops/268808/products/import?access_token=' . self::TOKEN;
        $res = Api::post($url, $headers, $body);
        return $res;
    }
}
