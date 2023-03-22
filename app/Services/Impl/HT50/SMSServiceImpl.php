<?php
namespace App\Services\Impl\HT50;

use App\Repositories\HT50\SMSRepository;
use App\Services\AbstractService;
use App\Services\HT50\SMSService;

class SMSServiceImpl extends AbstractService implements SMSService
{
   protected $repository;
   public function __construct(SMSRepository $repository)
        {
            parent::__construct($repository);
        }

}
