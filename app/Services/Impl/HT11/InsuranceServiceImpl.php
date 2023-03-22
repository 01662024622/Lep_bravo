<?php
namespace App\Services\Impl\HT11;

use App\Services\AbstractService;
use App\Services\HT11\InsuranceService;
use App\Repositories\HT11\InsuranceRepository;

class InsuranceServiceImpl extends AbstractService implements InsuranceService
{
   protected $repository;
   public function __construct(InsuranceRepository $repository)
        {
            parent::__construct($repository);
        }
}