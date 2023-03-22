<?php
namespace App\Repositories\Impl\HT50;

use App\Models\HT50\Gift;
use App\Repositories\AbstractRepository;
use App\Repositories\HT50\GiftRepository;

class GiftRepositoryImpl extends AbstractRepository implements GiftRepository
{
   protected $model;
   public function __construct(Gift $model)
        {
            parent::__construct($model);
        }
}
