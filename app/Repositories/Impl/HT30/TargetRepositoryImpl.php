<?php
namespace App\Repositories\Impl\HT30;

use App\Models\Ht30\Target;
use App\Repositories\AbstractRepository;
use App\Repositories\HT30\TargetRepository;

class TargetRepositoryImpl extends AbstractRepository implements TargetRepository
{
   protected $model;
   public function __construct(Target $model)
        {
            parent::__construct($model);
        }
}
