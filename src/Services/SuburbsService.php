<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 19:20
 */

use Atxy2k\Essence\Eloquent\Suburb;
use Atxy2k\Essence\Exceptions\Suburbs\SuburbNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\SuburbsRepository;
use Atxy2k\Essence\Validators\SuburbsValidator;
use Sentinel;
use DB;
use Throwable;
use Illuminate\Support\Str;

class SuburbsService extends Service
{
    /** @var SuburbsRepository  */
    protected $suburbsRepository;

    /**
     * SuburbsService constructor.
     * @param SuburbsRepository $suburbsRepository
     * @param SuburbsValidator $suburbsValidator
     */
    public function __construct(SuburbsRepository $suburbsRepository, SuburbsValidator $suburbsValidator)
    {
        parent::__construct();
        $this->validator = $suburbsValidator;
        $this->suburbsRepository = $suburbsRepository;
    }

    /**
     * @param array $data
     * @return Suburb|null
     * @throws Throwable
     */
    public function create(array $data = []) : ?Suburb
    {
        $return = null;
        if ($this->validator->with($data)->passes() )
        {
            try
            {
                DB::beginTransaction();
                throw_unless($this->suburbsRepository->slugFromTextIsAvailable( $data['municipality_id'], $data['name'] ),
                    SuburbNotFoundException::class);
                $data['slug'] = Str::slug($data['name']);
                $data['user_id'] = Sentinel::check() ? Sentinel::getUser()->getUserId() : null;
                /** @var Suburb|null $return */
                $return = $this->suburbsRepository->create($data);
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
     * @param bool $id
     * @param array $data
     * @return Suburb|null
     * @throws Throwable
     */
    public function update($id = false, array $data = []) : ?Suburb
    {
        $return = null;
        if ($this->validator->with( $data )->passes('update'))
        {
            try
            {
                DB::beginTransaction();
                throw_unless($this->suburbsRepository->slugFromTextIsAvailable( $data['municipality_id'], $data['name'], $id ) ,
                    SuburbNotFoundException::class);
                $data['slug'] = Str::slug($data['name']);
                if( $this->suburbsRepository->update($id, $data) )
                {
                    /** @var Suburb|null $return */
                    $return = $this->suburbsRepository->find($id);
                }
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
        else
        {
            $this->pushErrors( $this->validator->errors() );
        }
        return $return;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id) : bool
    {
        $return = false;
        try
        {
            $element = $this->suburbsRepository->find($id);
            throw_if($element === null, SuburbNotFoundException::class);
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
