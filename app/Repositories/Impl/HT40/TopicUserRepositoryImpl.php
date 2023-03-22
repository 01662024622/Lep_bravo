<?php
namespace App\Repositories\Impl\HT40;

use App\Models\HT40\TopicUser;
use App\Repositories\AbstractRepository;
use App\Repositories\HT40\TopicUserRepository;

class TopicUserRepositoryImpl extends AbstractRepository implements TopicUserRepository
{
   protected $model;
   public function __construct(TopicUser $model)
        {
            parent::__construct($model);
        }
}
