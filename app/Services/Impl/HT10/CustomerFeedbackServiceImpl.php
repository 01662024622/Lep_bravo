<?php


namespace App\Services\Impl\HT10;


use App\Repositories\HT10\CustomerFeedbackRepository;
use App\Services\AbstractService;
use App\Services\HT10\CustomerFeedbackService;

class CustomerFeedbackServiceImpl extends AbstractService implements CustomerFeedbackService
{
    protected $repository;
    public function __construct(CustomerFeedbackRepository $repository)
    {
        parent::__construct($repository);
    }

}
