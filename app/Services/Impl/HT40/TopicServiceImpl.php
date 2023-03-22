<?php
namespace App\Services\Impl\HT40;

use App\Repositories\HT40\TopicRepository;
use App\Services\AbstractService;
use App\Services\HT40\TopicService;

class TopicServiceImpl extends AbstractService implements TopicService
{
   protected $repository;
   public function __construct(TopicRepository $repository)
        {
            parent::__construct($repository);
        }
}