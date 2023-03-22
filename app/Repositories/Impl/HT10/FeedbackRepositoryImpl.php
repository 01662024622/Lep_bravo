<?php
namespace App\Repositories\Impl\HT10;
use App\Models\HT10\Feedback;
use App\Repositories\AbstractRepository;
use App\Repositories\HT10\FeedbackRepository;

class FeedbackRepositoryImpl extends AbstractRepository implements FeedbackRepository
{
    public function __construct(Feedback $model)
    {
        parent::__construct($model);
    }
}
