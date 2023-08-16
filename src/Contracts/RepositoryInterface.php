<?php namespace Atxy2k\Essence\Contracts;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 13:00
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RepositoryInterface extends CriteriaInterface
{
    public function count() : int;
    public function create(array $data) : ?Model;
    public function update(int|string $id, array $attributes = []) : bool;
    public function all(array $columns = ['*']) : Collection;
    public function find(int|string $id, array $columns = ['*']) : ?Model;
    public function findWithTrashed(int|string $id,array $columns = ['*']) : ?Model;
    public function findOrFail(int|string $id, array $columns = ['*']) : Model;
    public function findOrFailWithTrashed(int|string $id,array $columns = ['*']) : Model;
    public function destroy(int|array|string $ids) : int;
    public function delete(int|array|string $ids) : bool ;
    public function query() : Model;
    public function max(string $column) : float|int;
    public function min(string $column) : float|int;
    public function pluck(string $col, string|null $key = null) : array;
    public function paginate(int $per_page, int $page = 1) : LengthAwarePaginator;
    public function first() : ?Model;
    public function last() : ?Model;

}
