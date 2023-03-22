<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\ObjectUserRepository;
use App\Services\AbstractService;
use App\Services\HT30\ObjectUserService;

class ObjectUserServiceImpl extends AbstractService implements ObjectUserService
{
   protected $repository;
   public function __construct(ObjectUserRepository $repository)
        {
            parent::__construct($repository);
        }
}