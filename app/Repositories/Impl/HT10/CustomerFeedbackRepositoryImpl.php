<?php


namespace App\Repositories\Impl\HT10;


use App\Models\HT10\CustomerFeedback;
use App\Repositories\AbstractRepository;
use App\Repositories\HT10\CustomerFeedbackRepository;

class CustomerFeedbackRepositoryImpl extends AbstractRepository implements CustomerFeedbackRepository
{
    public function __construct(CustomerFeedback $model)
    {
        parent::__construct($model);
    }
}
