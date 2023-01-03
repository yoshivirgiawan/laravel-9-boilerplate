<?php

namespace App\Infrastructures\Services\Core;

use App\Infrastructures\Services\Core\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Repository implements RepositoryInterface
{
    private Builder $query;

    public function __construct()
    {
        $this->begin();
    }

    abstract protected function model(): string;

    protected function begin(): self
    {
        $class = $this->model();
        $model = new $class();
        $this->query = $model->newQuery();
        return $this;
    }

    public function all(): Collection
    {
        return $this->query->get();
    }

    public function findById($id): Model
    {
        return $this->query->findOrFail($id);
    }

    public function paginator($size = 50, $page = 1): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
