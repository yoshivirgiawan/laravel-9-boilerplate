<?php

namespace App\Infrastructures\Core;

use App\Infrastructures\Core\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function findById($id): ?Model
    {
        return $this->query->findOrFail($id);
    }

    public function create(array $dataRequest): Model
    {
        return $this->query->create($dataRequest);
    }

    public function updateById($id, array $dataRequest): ?Model
    {
        $data = $this->findById($id);
        $data->update($dataRequest);

        return $data;
    }

    public function deleteById($id): Model
    {
        $data = $this->findById($id);
        $data->delete();

        return $data;
    }

    public function paginator($size = 50, $page = 1): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
