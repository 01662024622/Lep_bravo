<?php
namespace App\Repositories\Impl\HT50;

use App\Models\HT50\InforCustomerSurvey;
use App\Repositories\AbstractRepository;
use App\Repositories\HT50\InforCustomerSurveyRepository;

class InforCustomerSurveyRepositoryImpl extends AbstractRepository implements InforCustomerSurveyRepository
{
   protected $model;
   public function __construct(InforCustomerSurvey $model)
        {
            parent::__construct($model);
        }
}
