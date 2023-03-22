<?php


namespace App\Models;


trait ModelTrait
{

    public function getStore() :array
    {
        return $this->fillable_store;
    }
    public function getUpdate() :array
    {
        return $this->fillable_update;
    }
}
