<?php
namespace App\Services\Impl\HT50;

use App\Repositories\HT50\InforCustomerSurveyRepository;
use App\Services\AbstractService;
use App\Services\HT50\InforCustomerSurveyService;

class InforCustomerSurveyServiceImpl extends AbstractService implements InforCustomerSurveyService
{
   protected $repository;
   public function __construct(InforCustomerSurveyRepository $repository)
        {
            parent::__construct($repository);
        }
}