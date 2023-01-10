<?php

namespace App\Infrastructures\Core\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function all(): Collection;

    public function findById($id): ?Model;

    public function create(array $dataRequest): Model;

    public function updateById($id, array $dataRequest): ?Model;

    public function deleteById($id): Model;

    public function paginator($size = 50, $page = 1): LengthAwarePaginator;
}
