<?php
namespace App\Repositories\Impl\HT20;

use App\Models\HT20\Apartment;
use App\Repositories\AbstractRepository;
use App\Repositories\HT20\ApartmentRepository;

class ApartmentRepositoryImpl extends  AbstractRepository implements ApartmentRepository
{

  public function __construct(Apartment $model)
  {
    parent::__construct($model);
  }

}

