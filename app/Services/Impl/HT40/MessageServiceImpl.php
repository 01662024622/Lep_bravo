<?php
namespace App\Services\Impl\HT40;

use App\Repositories\HT40\MessageRepository;
use App\Services\AbstractService;
use App\Services\HT40\MessageService;

class MessageServiceImpl extends AbstractService implements MessageService
{
   protected $repository;
   public function __construct(MessageRepository $repository)
        {
            parent::__construct($repository);
        }
}