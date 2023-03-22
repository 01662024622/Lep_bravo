<?php
namespace App\Services\Impl\HT30;

use App\Repositories\HT30\KeyRepository;
use App\Services\AbstractService;
use App\Services\HT30\KeyService;

class KeyServiceImpl extends AbstractService implements KeyService
{
   protected $repository;
   public function __construct(KeyRepository $repository)
        {
            parent::__construct($repository);
        }
}