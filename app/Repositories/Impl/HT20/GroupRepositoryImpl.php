<?php
namespace App\Repositories\Impl\HT20;

use App\Models\HT20\Group;
use App\Repositories\AbstractRepository;
use App\Repositories\HT20\GroupRepository;

class GroupRepositoryImpl extends AbstractRepository implements GroupRepository
{
   protected $model;
   public function __construct(Group $model)
        {
            parent::__construct($model);
        }
}
