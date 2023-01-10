<?php

namespace App\Infrastructures\Repositories;

use App\Infrastructures\Core\Repository;
use App\Models\User;

class UserRepository extends Repository
{
    protected function model(): string
    {
        return User::class;
    }
}
