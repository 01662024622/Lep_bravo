<?php


namespace App\Repositories\Impl\HT00;


use App\Models\HT00\Category;
use App\Repositories\AbstractRepository;
use App\Repositories\HT00\CategoryRepository;

class CategoryRepositoryImpl extends AbstractRepository implements CategoryRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
