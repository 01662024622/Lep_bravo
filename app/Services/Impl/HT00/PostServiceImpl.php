<?php
namespace App\Services\Impl\HT00;

use App\Repositories\HT00\PostRepository;
use App\Services\AbstractService;
use App\Services\HT00\PostService;

class PostServiceImpl extends AbstractService implements PostService
{
   protected $repository;
   public function __construct(PostRepository $repository)
        {
            parent::__construct($repository);
        }
}