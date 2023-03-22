<?php


namespace App\Repositories\Impl\HT10;

use App\Models\HT10\Review;
use App\Repositories\AbstractRepository;
use App\Repositories\HT10\ReviewRepository;

class ReviewRepositoryImpl extends  AbstractRepository implements ReviewRepository
{

    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

}
