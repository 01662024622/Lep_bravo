<?php
namespace App\Repositories\Impl\HT40;

use App\Models\HT40\Message;
use App\Repositories\AbstractRepository;
use App\Repositories\HT40\MessageRepository;

class MessageRepositoryImpl extends AbstractRepository implements MessageRepository
{
   protected $model;
   public function __construct(Message $model)
        {
            parent::__construct($model);
        }
}
