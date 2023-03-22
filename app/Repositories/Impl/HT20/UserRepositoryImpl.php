<?php
namespace App\Repositories\Impl\HT20;

use App\Repositories\AbstractRepository;
use App\Models\HT20\User;
use App\Repositories\HT20\UserRepository;

class UserRepositoryImpl extends AbstractRepository implements UserRepository
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

}

