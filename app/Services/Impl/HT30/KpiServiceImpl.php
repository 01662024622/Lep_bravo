<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\KpiRepository;
use App\Services\AbstractService;
use App\Services\HT30\KpiService;

class KpiServiceImpl extends AbstractService implements KpiService
{
   protected $repository;
   public function __construct(KpiRepository $repository)
        {
            parent::__construct($repository);
        }
}