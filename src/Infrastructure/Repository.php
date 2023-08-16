<?php namespace Atxy2k\Essence\Infrastructure;

use Atxy2k\Essence\Contracts\RepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use BadMethodCallException;
use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 13:39
 */
class Repository implements RepositoryInterface
{
    protected string|null $model = null;
    protected Model|Builder $query;
    protected Container|null $app = null;
    protected array $criteria = [];

    public function __construct(Container $app)
    {
        $this->query = $app->make($this->model);
        $this->app = $app;
    }

    public function count(): int
    {
        return $this->query->count();
    }

    public function create(array $data): ?Model
    {
        return $this->query->create($data);
    }

    public function update($id, array $attributes = []): bool
    {
        return $this->query->findOrFail($id)->update($attributes);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query->get($columns);
    }

    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->query->find($id,$columns);
    }

    public function findWithTrashed(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->query->withTrashed()->find($id,$columns);
    }

    public function findOrFail(int|string $id, $columns = ['*']) : Model
    {
        return $this->query->findOrFail($id,$columns);
    }

    public function findOrFailWithTrashed(int|string $id, array $columns = ['*']) : Model
    {
        return $this->query->withTrashed()->findOrFail($id,$columns);
    }

    public function destroy(int|array|string $ids): int
    {
        if(is_array($ids))
            $this->query->withTrashed()->whereIn('id', $ids)->destroy();
        return $this->query->destroy($ids);
    }

    public function delete(int|array|string $ids): bool
    {
        if(is_array($ids))
            $this->query->whereIn('id', $ids)->delete();
        return $this->query->findOrFail($ids)->delete();
    }

    public function query(): Model
    {
        return $this->query;
    }

    public function max(string $column): float|int
    {
        return $this->query->max($column);
    }

    public function min(string $column): float|int
    {
        return $this->query->min($column);
    }

    public function pluck(string $col, string $key = null): array
    {
        return $this->query->pluck($col,$key)->all();
    }

    public function paginate(int $per_page, int $page = 1): LengthAwarePaginator
    {
        return $this->query->paginate( $per_page, ['*'], 'page', $page );
    }

    public function first(): ?Model
    {
        return $this->query->first();
    }

    public function last(): ?Model
    {
        return $this->query->last();
    }

    public function pushCriteria(Criteria $criteria): Repository
    {
        if(!in_array($criteria, $this->criteria))
            $this->criteria[] = $criteria;
        return $this;
    }

    public function addCriteria(Criteria $criteria): Repository
    {
        return $this->pushCriteria($criteria);
    }

    public function __call($name, $arguments)
    {
        if(Str::endsWith($name, 'WithCriteria'))
        {
            $functionToCall = str_replace('WithCriteria', '', $name);
            throw_unless(method_exists($this,$functionToCall), BadMethodCallException::class);
            $otherModel = $this->app->make($this->model);
            /** @var Criteria $criteria */
            foreach ( $this->getCriteria() as $criteria )
            {
                $this->query = $criteria->apply($this->query, $this);
            }
            $response = call_user_func_array([$this,$functionToCall], $arguments);
            $this->query = $otherModel;
            return $response;
        }
        throw new BadMethodCallException;
    }
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function cleanCriteria(): Repository
    {
        $this->criteria = [];
        return $this;
    }
}
