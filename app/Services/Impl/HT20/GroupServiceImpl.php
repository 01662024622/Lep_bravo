<?php
namespace App\Services\Impl\HT20;

use App\Repositories\HT20\GroupRepository;
use App\Services\AbstractService;
use App\Services\HT20\GroupService;

class GroupServiceImpl extends AbstractService implements GroupService
{
   protected $repository;
   public function __construct(GroupRepository $repository)
        {
            parent::__construct($repository);
        }
}