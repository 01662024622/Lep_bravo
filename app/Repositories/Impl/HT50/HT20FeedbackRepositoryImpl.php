<?php
namespace App\Repositories\Impl\HT50;

use App\Models\HT50\HT20Feedback;
use App\Repositories\AbstractRepository;
use App\Repositories\HT50\HT20FeedbackRepository;

class HT20FeedbackRepositoryImpl extends AbstractRepository implements HT20FeedbackRepository
{
   protected $model;
   public function __construct(HT20Feedback $model)
        {
            parent::__construct($model);
        }
}
