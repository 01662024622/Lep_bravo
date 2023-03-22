<?php
namespace App\Repositories;

use Illuminate\Http\Request;

interface RepositoryInterface
{
    public function all();

    public function create(Request $data,array $arr);

    public function update(Request $data,int $id);

    public function delete(int $id);

    public function show(int $id);
}
