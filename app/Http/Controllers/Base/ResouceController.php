<?php
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Services\ServiceInterface;
use Illuminate\Http\Request;

class ResouceController extends Controller
{
	protected $service;
	protected $table;
	protected $blade;
	function __construct(ServiceInterface $service,array $arr,$blade='.index') {
		$this->service = $service;
		$this->blade = $blade;
		$this->table=$arr['active'];
	}
    protected function index(){
		return view($this->table.$this->blade);
	}

    protected function show($id){
		$data=$this->service->show($id);
		return response()->json($data);
	}
    protected function destroy($id){
		$data=$this->service->delete($id);
		return response()->json($data);
	}
	protected function storeRequest(Request $request,array $arr=[]){
        return $this->service->create($request,$arr);
    }

}
