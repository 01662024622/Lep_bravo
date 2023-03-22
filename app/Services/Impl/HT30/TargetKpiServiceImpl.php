<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\TargetKpiRepository;
use App\Services\AbstractService;
use App\Services\HT30\TargetKpiService;

class TargetKpiServiceImpl extends AbstractService implements TargetKpiService
{
   protected $repository;
   public function __construct(TargetKpiRepository $repository)
        {
            parent::__construct($repository);
        }
}