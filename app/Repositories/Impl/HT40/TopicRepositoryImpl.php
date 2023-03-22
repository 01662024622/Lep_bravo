<?php
namespace App\Repositories\Impl\HT40;

use App\Models\HT40\Topic;
use App\Repositories\AbstractRepository;
use App\Repositories\HT40\TopicRepository;

class TopicRepositoryImpl extends AbstractRepository implements TopicRepository
{
   protected $model;
   public function __construct(Topic $model)
        {
            parent::__construct($model);
        }
}
