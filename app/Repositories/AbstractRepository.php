<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class AbstractRepository implements RepositoryInterface
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->where('status', 0)->get();
    }

    public function create(Request $data,array $arr)
    {
        $auth=0;
        if (Auth::check()){
            $auth = Auth::id();
        }
//        try {
            if ($data->has("id")) {
                $update=$data->only($this->model->getUpdate());
                $update['modify_by']=$auth;
                $entity= $this->model->find($data->id);
                $entity->update(array_merge($update,$arr));
                return $entity;
            }
            $create=$data->only($this->model->getStore());
            $create['create_by']=$auth;
            return $this->model->create(array_merge($create,$arr));
//        } catch (Exception $e) {
//            return response()
//                ->json([
//                    'code' => 502,
//                    'message' => 'Dữ liệu không hợp lệ!',
//                    'detail' => $e
//                ], 502);
//        }
    }

    public function update(Request $data,int $id)
    {
        return null;
    }

    public function delete(int $id)
    {
        return $this->model->find($id)->update(["status" => 1]);
    }

    public function show(int $id)
    {
        return $this->model->find($id);
    }

}

