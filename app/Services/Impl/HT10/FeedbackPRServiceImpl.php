<?php


namespace App\Services\Impl\HT10;


use App\Repositories\Impl\HT00\FeedbackPRRepository;
use App\Services\AbstractService;
use App\Services\HT10\FeedbackPRService;

class FeedbackPRServiceImpl extends AbstractService implements FeedbackPRService
{
    protected $repository;
    public function __construct(FeedbackPRRepository $repository)
    {
        parent::__construct($repository);
    }

}
