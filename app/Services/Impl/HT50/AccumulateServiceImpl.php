<?php
namespace App\Services\Impl\HT50;

use App\Services\AbstractService;
use App\Services\HT50\AccumulateService;
use App\Repositories\HT50\AccumulateRepository;

class AccumulateServiceImpl extends AbstractService implements AccumulateService
{
   protected $repository;
   public function __construct(AccumulateRepository $repository)
        {
            parent::__construct($repository);
        }
}