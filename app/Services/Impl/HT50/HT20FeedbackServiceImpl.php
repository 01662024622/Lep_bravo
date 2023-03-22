<?php
namespace App\Services\Impl\HT50;

use App\Repositories\HT50\HT20FeedbackRepository;
use App\Services\AbstractService;
use App\Services\HT50\HT20FeedbackService;

class HT20FeedbackServiceImpl extends AbstractService implements HT20FeedbackService
{
   protected $repository;
   public function __construct(HT20FeedbackRepository $repository)
        {
            parent::__construct($repository);
        }
}