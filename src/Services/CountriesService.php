<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:04
 */

use Atxy2k\Essence\Eloquent\Country;
use Atxy2k\Essence\Exceptions\Countries\CountryNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\CountriesRepository;
use Atxy2k\Essence\Validators\CountriesValidator;
use Sentinel;
use Throwable;
use DB;
use Atxy2k\Essence\Exceptions\Countries\NameNotAvailableException;

class CountriesService extends Service
{
    /** @var CountriesRepository */
    protected $countriesRepository;

    /**
     * CountriesService constructor.
     * @param CountriesRepository $countriesRepository
     * @param CountriesValidator $countriesValidator
     */
    public function __construct(CountriesRepository $countriesRepository, CountriesValidator $countriesValidator)
    {
        parent::__construct();
        $this->countriesRepository = $countriesRepository;
        $this->validator = $countriesValidator;
    }

    /**
     * @param array $data
     * @return Country|null
     * @throws Throwable
     */
    public function create(array $data = []) : ?Country
    {
        $return = null;
        if ($this->validator->with($data)->passes() )
        {
            try
            {
                DB::beginTransaction();
                throw_unless($this->countriesRepository->slugFromTextIsAvailable( $data['name'] ),
                    NameNotAvailableException::class);
                $data['user_id'] = Sentinel::check() ? Sentinel::getUser()->getUserId() : null;
                /** @var Country|null $return */
                $return = $this->countriesRepository->create($data);
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
     * @param int|null $id
     * @param array $data
     * @return Country|null
     * @throws Throwable
     */
    public function update(int $id = null, array $data = []) : ?Country
    {
        $return = null;
        if ($this->validator->with( $data )->passes('update'))
        {
            try
            {
                DB::beginTransaction();
                throw_unless($this->countriesRepository->slugFromTextIsAvailable( $data['name'], $id ),
                    NameNotAvailableException::class);
                if( $this->countriesRepository->update($id, $data) )
                {
                    /** @var Country|null $return */
                    $return = $this->countriesRepository->find($id);
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
     * @throws Throwable
     */
    public function delete(int $id) : bool
    {
        $return = false;
        try
        {
            $element = $this->countriesRepository->find($id);
            throw_if($element === null, CountryNotFoundException::class);
            DB::beginTransaction();
            $element->delete();
            $return = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollBack();
            $this->pushError($e->getMessage());
        }
        return $return;
    }
}
