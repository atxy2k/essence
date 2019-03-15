<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:46
 */

use Atxy2k\Essence\Eloquent\Municipality;
use Atxy2k\Essence\Exceptions\Municipalities\MunicipalityNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\MunicipalitiesRepository;
use Atxy2k\Essence\Validators\MunicipalitiesValidator;
use DB;
use Throwable;
use Sentinel;
use Illuminate\Support\Str;

class MunicipalitiesService extends Service
{
    /** @var MunicipalitiesRepository  */
    protected $municipalitiesRepository;

    /**
     * MunicipalitiesService constructor.
     * @param MunicipalitiesRepository $municipalitiesRepository
     * @param MunicipalitiesValidator $municipalitiesValidator
     */
    public function __construct(MunicipalitiesRepository $municipalitiesRepository,
                                MunicipalitiesValidator $municipalitiesValidator)
    {
        parent::__construct();
        $this->municipalitiesRepository = $municipalitiesRepository;
        $this->validator = $municipalitiesValidator;
    }

    /**
     * @param array $data
     * @return Municipality|null
     * @throws Throwable
     */
    public function create(array $data = []) : ?Municipality
    {
        $return = null;
        if ($this->validator->with($data)->passes() )
        {
            try
            {
                DB::beginTransaction();
                throw_unless($this->municipalitiesRepository->slugFromTextIsAvailable( $data['state_id'], $data['name'] ),
                    MunicipalityNotFoundException::class);
                $data['slug'] = Str::slug($data['name']);
                $data['user_id'] = Sentinel::check() ? Sentinel::getUser()->getUserId() : null;
                /** @var Municipality|null $return */
                $return = $this->municipalitiesRepository->create($data);
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
     * @return Municipality|null
     * @throws Throwable
     */
    public function update(int $id, array $data = []) : ?Municipality
    {
        $return = null;
        if ($this->validator->with( $data )->passes('update'))
        {
            try
            {
                DB::beginTransaction();
                if( $this->municipalitiesRepository->slugFromTextIsAvailable( $data['state_id'], $data['name'], $id ) )
                {
                    $data['slug'] = Str::slug($data['name']);
                    if( $this->municipalitiesRepository->update($id, $data) )
                    {
                        /** @var Municipality|null $return */
                        $return = $this->municipalitiesRepository->find($id);
                    }
                }
                else
                {
                    $this->pushError('El nombre no está disponible.');
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
     * @param $id
     * @return bool
     * @throws Throwable
     */
    public function delete($id) : bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $element = $this->municipalitiesRepository->find($id);
            throw_if($element === null, MunicipalityNotFoundException::class);
            $element->delete();
            $return = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollBack();
        }
        return $return;
    }

}
