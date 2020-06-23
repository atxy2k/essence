<?php namespace Atxy2k\Essence\Support\Datatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 23/2/2019
 * Time: 10:38
 */
class Order implements Arrayable
{
    protected $column;
    protected $dir;

    public function __construct(array $data)
    {
        $this->column = Arr::get($data,'column', 0);
        $this->dir = Arr::get($data,'dir', 'desc');
    }

    /**
     * @return mixed
     */
    public function getColumn() : ?int
    {
        return $this->column;
    }

    /**
     * @param mixed $column
     */
    public function setColumn($column): void
    {
        $this->column = $column;
    }

    /**
     * @return mixed
     */
    public function getDir() : ?string
    {
        return $this->dir;
    }

    /**
     * @param mixed $dir
     */
    public function setDir($dir): void
    {
        $this->dir = $dir;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'colum' => $this->column,
            'dir'   => $this->dir
        ];
    }
}
