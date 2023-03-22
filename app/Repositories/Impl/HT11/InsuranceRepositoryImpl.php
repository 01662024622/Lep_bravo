<?php
namespace App\Repositories\Impl\HT11;

use App\Repositories\AbstractRepository;
use App\Repositories\HT11\InsuranceRepository;
use App\Models\HT11\Insurance;

class InsuranceRepositoryImpl extends AbstractRepository implements InsuranceRepository
{
   protected $model;
   public function __construct(Insurance $model)
        {
            parent::__construct($model);
        }
}
