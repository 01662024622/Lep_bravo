<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\TargetControllerRepository;
use App\Services\AbstractService;
use App\Services\HT30\TargetControllerService;

class TargetControllerServiceImpl extends AbstractService implements TargetControllerService
{
   protected $repository;
   public function __construct(TargetControllerRepository $repository)
        {
            parent::__construct($repository);
        }
}