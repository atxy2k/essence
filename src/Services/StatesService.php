<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:23
 */

use Atxy2k\Essence\Eloquent\State;
use Atxy2k\Essence\Exceptions\States\NameNotAvailableException;
use Atxy2k\Essence\Exceptions\States\StateNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\StatesRepository;
use Atxy2k\Essence\Validators\StatesValidator;
use DB;
use Sentinel;
use Throwable;
use Illuminate\Support\Str;

class StatesService extends Service
{
    /** @var StatesRepository  */
    protected $statesRepository;

    /**
     * StatesService constructor.
     * @param StatesRepository $statesRepository
     * @param StatesValidator $statesValidator
     */
    public function __construct(StatesRepository $statesRepository, StatesValidator $statesValidator)
    {
        parent::__construct();
        $this->statesRepository = $statesRepository;
        $this->validator = $statesValidator;
    }

    /**
     * @param array $data
     * @return State|null
     * @throws Throwable
     */
    public function create(array $data = []) : ?State
    {
        $return = null;
        if ($this->validator->with($data)->passes() )
        {
            try
            {
                DB::beginTransaction();
                throw_unless($this->statesRepository->slugFromTextIsAvailable($data['country_id'], $data['name'] ),
                    NameNotAvailableException::class);
                $data['slug'] = Str::slug($data['name']);
                $data['user_id'] = Sentinel::check() ? Sentinel::getUser()->getUserId() : null;
                /** @var State|null $return */
                $return = $this->statesRepository->create($data);
            }
            catch (Throwable $e)
            {
                $this->pushError($e->getMessage());
            }
            if ( $return )
            {
                DB::commit();
            }
            else
            {
                DB::rollBack();
            }
        }
        return $return;
    }

    /**
     * @param int $id
     * @param array $data
     * @return State|null
     * @throws Throwable
     */
    public function update(int $id, array $data = []) : ?State
    {
        $return = null;
        if ($this->validator->with( $data )->passes('update'))
        {
            try
            {
                DB::beginTransaction();
                $current = $this->statesRepository->find($id);
                throw_if($current === null, StateNotFoundException::class);
                throw_unless($this->statesRepository->slugFromTextIsAvailable( $data['country_id'], $data['name'], $id ),
                    NameNotAvailableException::class);
                $data['slug'] = Str::slug($data['name']);
                if( $this->statesRepository->update($id, $data) )
                {
                    /** @var State|null $return */
                    $return = $this->statesRepository->find($id);
                    DB::commit();
                }
            }
            catch (Throwable $e)
            {
                $this->pushError(vsprintf('%s:::%s::::%s',[
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ]));
                DB::rollBack();
            }
        }
        return $return;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id) : bool
    {
        $return = false;
        $element = $this->statesRepository->find($id);
        try
        {
            throw_if($element === null, StateNotFoundException::class);
            $element->delete();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}
