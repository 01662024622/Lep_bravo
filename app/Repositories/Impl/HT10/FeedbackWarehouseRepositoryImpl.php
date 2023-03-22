<?php


namespace App\Repositories\Impl\HT10;


use App\Models\HT10\FeedbackWarehouse;
use App\Repositories\AbstractRepository;
use App\Repositories\HT10\FeedbackWarehouseRepository;

class FeedbackWarehouseRepositoryImpl extends AbstractRepository implements FeedbackWarehouseRepository
{
    public function __construct(FeedbackWarehouse $model)
    {
        parent::__construct($model);
    }
}
