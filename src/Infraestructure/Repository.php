<?php namespace Atxy2k\Essence\Infraestructure;
use Atxy2k\Essence\Interfaces\RepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 13:39
 */
class Repository implements RepositoryInterface
{
    /** @var string */
    protected $name = null;
    /** @var Model */
    protected $model = null;
    /** @var Container */
    protected $app = null;

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * @param array $data
     * @return Model|null
     */
    public function create(array $data): ?Model
    {
        return $this->model->create($data);
    }

    /**
     * @param $id
     * @param array $attributes
     * @return bool
     */
    public function update($id, array $attributes = []): bool
    {
        return $this->model->findOrFail($id)->update($attributes);
    }

    /**
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->get($columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return Model|null
     */
    public function find($id, $columns = ['*']): ?Model
    {
        return $this->model->find($id,$columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return Model|null
     */
    public function findWithTrashed($id, $columns = ['*']): ?Model
    {
        return $this->model->withTrashed()->find($id,$columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*']) : Model
    {
        return $this->model->findOrFail($id,$columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findOrFailWithTrashed($id, $columns = ['*']) : Model
    {
        return $this->model->withTrashed()->findOrFail($id,$columns);
    }

    /**
     * @param $ids
     * @return int
     */
    public function destroy($ids): int
    {
        return $this->model->destroy($ids);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
    }

    /**
     * @param string $column
     * @return int
     */
    public function max(string $column): int
    {
        return $this->model->max($column);
    }

    /**
     * @param string $column
     * @return int
     */
    public function min(string $column): int
    {
        return $this->model->min($column);
    }

    /**
     * @param string $col
     * @param string|null $key
     * @return array
     */
    public function lists(string $col, string $key = null): array
    {
        return $this->model->pluck($col,$key)->all();
    }

    /**
     * @param string $col
     * @param string|null $key
     * @return array
     */
    public function pluck(string $col, string $key = null): array
    {
        return $this->lists($col, $key);
    }

    /**
     * @param int $per_page
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function paginate(int $per_page, int $page = 1): LengthAwarePaginator
    {
        return $this->model->paginate( $per_page, ['*'], null, $page );
    }

    /**
     * @return Model|null
     */
    public function first(): ?Model
    {
        return $this->model->first();
    }

    /**
     * @return Model|null
     */
    public function last(): ?Model
    {
        return $this->model->last();
    }
}
