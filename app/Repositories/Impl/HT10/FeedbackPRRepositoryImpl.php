<?php
namespace App\Repositories\Impl\HT10;


use App\Models\HT10\FeedbackPR;
use App\Repositories\AbstractRepository;
use App\Repositories\Impl\HT00\FeedbackPRRepository;

class FeedbackPRRepositoryImpl extends AbstractRepository implements FeedbackPRRepository
{
    public function __construct(FeedbackPR $model)
    {
        parent::__construct($model);
    }
}
