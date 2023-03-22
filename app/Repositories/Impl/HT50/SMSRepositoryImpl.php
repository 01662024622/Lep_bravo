<?php
namespace App\Repositories\Impl\HT50;

use App\Models\HT50\SMS;
use App\Repositories\AbstractRepository;
use App\Repositories\HT50\SMSRepository;

class SMSRepositoryImpl extends AbstractRepository implements SMSRepository
{
   protected $model;
   public function __construct(SMS $model)
        {
            parent::__construct($model);
        }
}
