<?php
namespace App\Repositories\Impl\HT30;

use App\Models\HT30\Result;
use App\Repositories\AbstractRepository;
use App\Repositories\HT30\ResultRepository;

class ResultRepositoryImpl extends AbstractRepository implements ResultRepository
{
   protected $model;
   public function __construct(Result $model)
        {
            parent::__construct($model);
        }
}
