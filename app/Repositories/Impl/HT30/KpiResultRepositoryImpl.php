<?php
namespace App\Repositories\Impl\HT30;

use App\Models\HT30\KpiResult;
use App\Repositories\AbstractRepository;
use App\Repositories\HT30\KpiResultRepository;

class KpiResultRepositoryImpl extends AbstractRepository implements KpiResultRepository
{
   protected $model;
   public function __construct(KpiResult $model)
        {
            parent::__construct($model);
        }
}
