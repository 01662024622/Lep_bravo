<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\ObjectRepository;
use App\Services\AbstractService;
use App\Services\HT30\ObjectService;

class ObjectServiceImpl extends AbstractService implements ObjectService
{
   protected $repository;
   public function __construct(ObjectRepository $repository)
        {
            parent::__construct($repository);
        }
}