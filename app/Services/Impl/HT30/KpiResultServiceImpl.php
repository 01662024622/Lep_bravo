<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\KpiResultRepository;
use App\Services\AbstractService;
use App\Services\HT30\KpiResultService;

class KpiResultServiceImpl extends AbstractService implements KpiResultService
{
   protected $repository;
   public function __construct(KpiResultRepository $repository)
        {
            parent::__construct($repository);
        }
}