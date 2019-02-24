<?php namespace Atxy2k\Essence\Support\Datatable;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 23/2/2019
 * Time: 10:44
 */
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class DatatableResponse implements Arrayable, Jsonable
{
    /** @var array */
    protected $data;
    /** @var int */
    protected $recordsTotal;
    /** @var int */
    protected $recordsFiltered;
    /** @var DatatableRequest */
    protected $datatableRequest;

    /**
     * DatatableResponse constructor.
     * @param DatatableRequest $request
     * @param int $recordsTotal
     * @param int $recordsFiltered
     */
    public function __construct(DatatableRequest $request, int $recordsTotal = 0, int $recordsFiltered = 0)
    {
        $this->datatableRequest = $request;
        $this->recordsTotal = $recordsTotal;
        $this->recordsFiltered = $recordsFiltered;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'draw' => $this->datatableRequest->getDraw(),
            'recordsTotal' => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
            'data' => $this->data
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
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return DatatableResponse
     */
    public function setData(array $data): DatatableResponse
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return DatatableResponse
     */
    public function withData(array $data): DatatableResponse
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordsTotal(): int
    {
        return $this->recordsTotal;
    }

    /**
     * @param int $recordsTotal
     * @return DatatableResponse
     */
    public function setRecordsTotal(int $recordsTotal): DatatableResponse
    {
        $this->recordsTotal = $recordsTotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordsFiltered(): int
    {
        return $this->recordsFiltered;
    }

    /**
     * @param int $recordsFiltered
     * @return DatatableResponse
     */
    public function setRecordsFiltered(int $recordsFiltered): DatatableResponse
    {
        $this->recordsFiltered = $recordsFiltered;
        return $this;
    }

}
