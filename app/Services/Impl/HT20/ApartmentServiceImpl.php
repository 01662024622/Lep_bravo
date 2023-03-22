<?php
namespace App\Services\Impl\HT20;


use App\Repositories\HT20\ApartmentRepository;
use App\Repositories\Impl\HT20\ApartmentRepositoryImpl;
use App\Services\AbstractService;
use App\Services\HT20\ApartmentService;

class ApartmentServiceImpl extends AbstractService implements ApartmentService {
    protected $repository;
    public function __construct(ApartmentRepository $repository)
    {
        parent::__construct($repository);
    }

}

