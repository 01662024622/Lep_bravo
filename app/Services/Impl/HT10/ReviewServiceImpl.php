<?php


namespace App\Services\Impl\HT10;


use App\Repositories\Impl\HT10\ReviewRepositoryImpl;
use App\Services\AbstractService;
use App\Services\HT10\ReviewService;

class ReviewServiceImpl extends AbstractService implements ReviewService
{
    protected $repository;

    public function __construct(ReviewRepositoryImpl $repository)
    {
        parent::__construct($repository);
    }



}
