<?php
namespace App\Repositories\Impl\HT30;

use App\Models\HT30\Kpi;
use App\Repositories\AbstractRepository;
use App\Repositories\HT30\KpiRepository;

class KpiRepositoryImpl extends AbstractRepository implements KpiRepository
{
   protected $model;
   public function __construct(Kpi $model)
        {
            parent::__construct($model);
        }
}
