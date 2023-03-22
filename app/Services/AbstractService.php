<?php


namespace App\Services;


use App\Repositories\Impl\HT00\CategoryRepository;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

abstract class AbstractService implements ServiceInterface
{
    protected $repository;
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all() {
        return $this->repository->all();
    }

    public function create(Request $request,array $data){
        return $this->repository->create($request,$data);
    }

    public function update(array $data, $id){
        return null;
    }

    public function delete($id){
        return $this->repository->delete($id);
    }

    public function show($id){
        return $this->repository->show($id);
    }
}
