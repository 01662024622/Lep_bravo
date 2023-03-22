<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\ResultRepository;
use App\Services\AbstractService;
use App\Services\HT30\ResultService;

class ResultServiceImpl extends AbstractService implements ResultService
{
   protected $repository;
   public function __construct(ResultRepository $repository)
        {
            parent::__construct($repository);
        }
}