<?php namespace Atxy2k\Essence\Support\Datatable;
use Atxy2k\Essence\Support\Datatable\Exceptions\UnsupportedConstructorException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
/**
 * Class DatatableRequest
 * @package Atxy2k\Essence\Support\Datatable
 */
class DatatableRequest implements Arrayable, Jsonable
{
    /**
     * @var int
     */
    protected $draw = 1;
    /** @var int  */
    protected $start = 0;
    /** @var int  */
    protected $length = 10;
    /**
     * @var string|null
     */
    protected $search = null;
    /** @var Order|null  */
    protected $order = ['column' => 1, 'dir' => 'desc'];
    /** @var array|mixed  */
    protected $columns = [];
    /** @var array|mixed  */
    protected $additional_filters = [];

    public function __construct( $data )
    {
        if( is_array($data) )
        {
            $current_order = Arr::get($data, 'order', []);
            $this->draw = Arr::get($data, 'draw', 1);
            $this->start= Arr::get($data,'start', 0);
            $this->length = Arr::get($data, 'length', 10);
            $this->search = Arr::get( Arr::get($data,'search', []), 'value', false );
            /** @var array $current_order */
            $this->order  =  new Order( count($current_order) > 0 ? $current_order[0] : $this->order ) ; //column & dir
            $this->columns = Arr::get($data, 'columns', []);
            $this->additional_filters = Arr::get($data,'additional_filters', []);
            $this->response = [
                'draw' => $this->draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ];
        }
        else
        {
            throw  new UnsupportedConstructorException();
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'draw'      => $this->draw,
            'start'     => $this->start,
            'length'    => $this->length,
            'search'    => $this->search,
            'order'     => $this->order->toArray(),
            'columns'   => $this->columns,
            'additional_filters' => $this->additional_filters
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0) : string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return int
     */
    public function getStart() : ?int
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return $this->length;
    }

    /**
     * @return string|null
     */
    public function getSearch() : ?string
    {
        return $this->search;
    }

    /**
     * @return Order
     */
    public function getOrder() : Order
    {
        return $this->order;
    }

    /**
     * @return array|mixed
     */
    public function getColumns() : ?array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getAdditionalFilters() : array
    {
        return $this->additional_filters;
    }

    /**
     * @return int
     */
    public function getDraw(): ?int
    {
        return $this->draw;
    }
}
