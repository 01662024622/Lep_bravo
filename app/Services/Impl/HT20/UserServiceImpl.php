<?php
namespace App\Services\Impl\HT20;

use App\Repositories\HT20\UserRepository;
use App\Services\AbstractService;
use App\Services\HT20\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserServiceImpl extends AbstractService implements UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }
    public function show($id)
    {
        $data = $this->repository->show($id);
        $data['birth_day'] = Carbon::parse($data['birth_day'])->format('d/m/Y');
        return $data;
    }
}
