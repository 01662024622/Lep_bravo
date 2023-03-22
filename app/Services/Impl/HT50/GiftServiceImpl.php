<?php
namespace App\Services\Impl\HT50;

use App\Repositories\HT50\GiftRepository;
use App\Services\AbstractService;
use App\Services\HT50\GiftService;

class GiftServiceImpl extends AbstractService implements GiftService
{
   protected $repository;
   public function __construct(GiftRepository $repository)
        {
            parent::__construct($repository);
        }
}