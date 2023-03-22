<?php
namespace App\Http\Controllers;

use App\Center;
use App\Customer;
use App\Log;
use App\Product;
use App\Services\ERPService;
use App\Services\PancakeService;
use App\Services\SpeedService;
use App\Webhook;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use Unirest\Request as Api;
use Unirest\Request\Body;

class WebhookController extends Controller
{

    private $ErpService;
    private $SpeedService;
    private $PancakeService;
    public function get()
    {
        return response("true", 200);

    }


    public function create(Request $request)
    {
        $this->ErpService=ERPService::getInstance();
        $this->SpeedService=SpeedService::getInstance();
        $speed = $request->only(["event", "webhooksVerifyToken", "data"]);
        if ($speed["webhooksVerifyToken"] != "Thangui0011@@1996") return response('error', 404);

        Webhook::create(['data' => json_encode($speed)]);
        if ($speed["event"] == "productAdd") {
            return $this->procedureProductERP($speed);
        }

        if ($speed["event"] == "orderAdd" ||$speed["event"] == "orderUpdate") {
            return $this->procedurePoint();
        }
        if ($speed["event"] != "inventoryChange") {
            return response("true", 200);
        }
        return $this->procedureInventory($speed);
    }
    private function procedurePoint(){
        $res = $this->SpeedService->getCustomersPoint();

            foreach ($res->data->customers as $customer) {
                $CheckCustomer = Customer::where("phone",preg_replace('/\s+/S', " ", $customer->mobile))->first();
                if($CheckCustomer){
                    if($CheckCustomer->coin == (int)$customer->points) continue;
                    $this->ErpService->updatePoint($CheckCustomer);
                    $CheckCustomer->update(["coin"=>(int)$customer->points]);
                }else{
                    // add customer
                    $newC = new Customer();
                    $newC->phone = preg_replace('/\s+/S', " ", str_replace("\"","",$customer->mobile));
                    $newC->name = preg_replace('/\s+/S', " ", str_replace("\"","",$customer->name));
                    $newC->coin = (int)$customer->points;

                    $getCustomer = $this->ErpService->getCustomer($newC->phone);
                    if (property_exists($getCustomer, 'data')){
                        if(count($getCustomer->data)>0){
                            $newC->eId=$getCustomer->data[0]->id;
                            $newC->save();
                            continue;
                        }
                    }
                    $res = $this->ErpService->updatePoint($newC);
                    if (!property_exists($res, 'data')) {
                        Log::create(["content"=> "create customer false","description"=>$newC->phone."-".$res]);
                        continue;
                    }
                    $newC->eId=$res->data->id;
                    $newC->save();
                }

            }
        return response('true', 200);
    }


    private function procedureProductERP($speed){
        if($speed["data"]["parentId"]!= null)
            return response('true', 200);
        $product = Product::create(["code"=>$speed["data"]["code"],"name"=>$speed["data"]["name"]]);
        $product->slug=$product->code;
        $product->sku=$product->code;
        $product->type="item";
        $product->variations=[];
        $product->categories=[];
        $product->units=[];
        $product->parts=[];
        $product->unit=null;
        $product->price=$speed["data"]["price"];
        $product->normal_price=$speed["data"]["price"];
        $product->original_price=0;
        try{
        if(array_key_exists("image",$speed["data"])){
            $speed["data"]["image"]=str_replace("https://bucket.nhanh.vn/store/16294/","https://traffic-edge27.cdn.vncdn.io/cdn-pos/bb1d54-16294/",$speed["data"]["image"]);
            $info = pathinfo($speed["data"]["image"]);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.lep.vn/v1/images/upload-single?group=products',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('file'=> curl_file_create($speed["data"]["image"],'image/jpeg',$info['basename'])),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$this->ErpService->getToken()
                ),
            ));

            $response = json_decode(curl_exec($curl));
            if (!property_exists($response, 'url')) return response("true", 200);
            $product->thumbnail_url =$response->url;
            curl_close($curl);
        }}catch (Exception $e){}


        $res = $this->SpeedService->getSubProducts($speed["data"]["productId"]);
        $this->procedureProductPanCake($speed,$res);
        if (!property_exists($res, 'data')) {
            $product->products=[];
            $resProduct = $this->ErpService->createProduct($product);
            if (!property_exists($resProduct, 'data')) return response("true", 200);
            $update = Product::find($product->id);
            $update->pId=$resProduct->data->id;
            $update->save();
            return response("true", 200);
        }
        $subProducts=[];
        $index=0;
        foreach ($res->data->products as $value) {
            $sub =array(
                "indexes"=>[$index],
                "options"=> ["\"".trim(str_replace("-","",str_replace($product->code,"",$value->code)))."\""],
                "option_name"=> trim(str_replace("-","",str_replace($product->code,"",$value->code))),
                "slug"=>$value->code,
                "sku"=>$value->code,
                "name"=>$value->name,
                "type"=>"item",
                "variations"=>[],
                "categories"=>[],
                "units"=>[],
                "parts"=>[],
                "unit"=>null,
                "price"=>$value->price==null?0:$value->price,
                "normal_price"=>$value->price==null?0:$value->price,
                "original_price"=>0,

            );
            $subProducts[] = $sub;
            $index++;
        }
        $product->products = $subProducts;
        $resProduct = $this->ErpService->createProduct($product);

        if (!property_exists($resProduct, 'data')) {
             $this->ErpService->login();
            if($this->ErpService->t) return response("true", 200);

            $resProduct = $this->ErpService->createProduct($product);
            if (!property_exists($resProduct, 'data')) return response("true", 200);
        }
        $update = Product::find($product->id);
        $update->pId=$resProduct->data->id;
        $update->save();
        $resProduct = $this->ErpService->getListSubOfProduct($update->pId);
        if (!property_exists($resProduct, 'data')) {
            $tok = $this->ErpService->login();
            if($tok=="") return response("true", 200);
            $resProduct = $this->ErpService->getListSubOfProduct($update->pId);
            if (!property_exists($resProduct, 'data')) return response("true", 200);
        }
        foreach ($resProduct->data->products as $value) {
            foreach ($subProducts as $subP){
                if (strtolower($subP["sku"]) == strtolower($value->sku)){
                    Product::create(["code"=>$value->sku,"name"=>$subP["name"],"pId"=>$value->id,"parentId"=>$update->pId]);
                    break;
                }
            }
        }
        return response("true", 200);
    }
    private function procedureProductPanCake($speed, $subProduct): void
    {
        $PancakeService = PancakeService::getInstance();
        if($speed["data"]["parentId"]!= null) {
            response('true', 200);
            return;
        }
        $image="";
        if(array_key_exists("image",$speed["data"])){
            if($speed["data"]["image"]!=null)
                $image = $speed["data"]["image"];
        }
        foreach ($subProduct->data->products as $value) {
            $PancakeService->procedureCreateProduct($value,$image);
        }

    }




    private function procedureInventory($speed){
        $stocks = Center::where('nhanhId', '>', 0)->where('active',1)->get();
        $stockNhanhs = Center::where('nhanhId', '>', 0)->where('active',1)->get()->pluck('nhanhId')->toArray();
        foreach ($speed["data"] as $item) {
            $this->procedureInventoryErp($item,$stockNhanhs,$stocks);
            foreach ($item["depots"] as $key => $value) {
                if ((int)$key==133563) {
                    $PancakeService = PancakeService::getInstance();
                    $PancakeService->updateInventory($item["id"],$value["available"]);
                }
            }
        }
        return response('true', 200);
    }
    private function procedureInventoryErp($item,$stockNhanhs,$stocks){
        $product = Product::where('code', $item['code'])->where('parentId', '>', 0)->first();
        if ($product == null) {
            $product = Product::where('code', str_replace("-", "", $item['code']))->where('parentId', '>', 0)->first();
        }
        if ($product == null) {
            Log::create(['content' => $item['code'], 'description' => 'product not have']);
            return;
        }
        $erpProduct = $this->ErpService->getProduct($product->parentId, false);
        foreach ($erpProduct as $i) {
            if (str_replace("-", "", $i->sku )==str_replace("-", "", $product->code) ) {
                foreach ($item["depots"] as $key => $value) {
                    if (in_array((int)$key, $stockNhanhs)) {
                        $stockMap = $this->stockFilterNhanh($stocks, (int)$key);
                        $this->checkQuantity($i,$value,$stockMap,$product->parentId);
                    }
                }
            }
        }
    }

    private function checkQuantity($inventoryErp, $inventoryNhanh, $stockMap,$id)
    {

        $check = false;
        foreach ($inventoryErp->stocks as $stockErp) {
            if ($stockErp->id != $stockMap->id) continue;
            if ($stockErp->total_quantity == $inventoryNhanh['available']) {
                $check =true;
                break;
            }

            $product = array('id' => $stockErp->product_id,
                'option_id' => $stockErp->product_option_id,
                'total_quantity' => $stockErp->total_quantity,
                'total_actual' => $inventoryNhanh['available'],
                'total_adjustment' => $inventoryNhanh['available']-$stockErp->total_quantity);
            $this->ErpService->upDateInventory($stockMap,$product);
            $check=true;
            break;
        }
        if($check) return;
        if ($inventoryNhanh['available']==0) return;
        $product = array('id' => $id,
            'option_id' => $inventoryErp->id,
            'total_quantity' => 0,
            'total_actual' => $inventoryNhanh['available'],
            'total_adjustment' => $inventoryNhanh['available']);
        $this->ErpService->upDateInventory($stockMap,$product);
    }

    private function checkPancakeStore(){

    }



    private function stockFilterNhanh($lts, int $id)
    {
        foreach ($lts as $value) {
            if ($value->nhanhId == $id) {
                return $value;
            }
        }
        return null;
    }

}
