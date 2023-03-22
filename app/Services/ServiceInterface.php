<?php
namespace App\Services;


use Illuminate\Http\Request;

interface ServiceInterface{

  public function all();

  public function create(Request $request,array $data);

  public function update(array $data, $id);

  public function delete($id);

  public function show($id);
}

