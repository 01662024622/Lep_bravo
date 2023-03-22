<?php
namespace App\Repositories\Impl\HT30;

use App\Models\HT30\TargetKpi;
use App\Repositories\AbstractRepository;
use App\Repositories\HT30\TargetKpiRepository;

class TargetKpiRepositoryImpl extends AbstractRepository implements TargetKpiRepository
{
   protected $model;
   public function __construct(TargetKpi $model)
        {
            parent::__construct($model);
        }
}
