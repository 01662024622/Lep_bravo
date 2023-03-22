<?php
namespace App\Repositories\Impl\HT50;

use App\Repositories\AbstractRepository;
use App\Repositories\HT50\AccumulateRepository;
use App\Models\{{ NamespaceEntity }};

class AccumulateRepositoryImpl extends AbstractRepository implements AccumulateRepository
{
   protected $model;
   public function __construct(Accumulate $model)
        {
            parent::__construct($model);
        }
}