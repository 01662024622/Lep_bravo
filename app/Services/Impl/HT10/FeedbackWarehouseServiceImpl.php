<?php


namespace App\Services\Impl\HT10;


use App\Repositories\HT10\FeedbackWarehouseRepository;
use App\Services\AbstractService;
use App\Services\HT10\FeedbackWarehouseService;

class FeedbackWarehouseServiceImpl extends AbstractService implements FeedbackWarehouseService
{
    protected $repository;
    public function __construct(FeedbackWarehouseRepository $repository)
    {
        parent::__construct($repository);
    }

}
