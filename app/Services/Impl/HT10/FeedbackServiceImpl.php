<?php


namespace App\Services\Impl\HT10;


use App\Repositories\HT10\FeedbackRepository;
use App\Services\AbstractService;
use App\Services\HT10\FeedbackService;

class FeedbackServiceImpl extends AbstractService implements FeedbackService
{
    protected $repository;
    public function __construct(FeedbackRepository $repository)
    {
        parent::__construct($repository);
    }

}
