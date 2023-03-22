<?php
namespace App\Services\Impl\HT40;

use App\Repositories\HT40\TopicUserRepository;
use App\Services\AbstractService;
use App\Services\HT40\TopicUserService;

class TopicUserServiceImpl extends AbstractService implements TopicUserService
{
   protected $repository;
   public function __construct(TopicUserRepository $repository)
        {
            parent::__construct($repository);
        }
}