<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\TargetRepository;
use App\Services\AbstractService;
use App\Services\HT30\TargetService;

class TargetServiceImpl extends AbstractService implements TargetService
{
   protected $repository;
   public function __construct(TargetRepository $repository)
        {
            parent::__construct($repository);
        }
}