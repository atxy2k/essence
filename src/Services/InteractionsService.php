<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Exceptions\Essence\NameIsNotAvailableException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\Services\InteractionsServiceInterface;
use Atxy2k\Essence\Repositories\InteractionsRepository;
use Atxy2k\Essence\Validators\InteractionsValidator;
use Illuminate\Support\Str;
use Throwable;
use Essence;
use DB;
use Exception;

class InteractionsService extends Service implements InteractionsServiceInterface
{

    /** @var InteractionsRepository */
    protected $interactionsRepository;

    public function __construct(InteractionsRepository $interactionsRepository,
                                InteractionsValidator $interactionsValidator)
    {
        parent::__construct();
        $this->interactionsRepository = $interactionsRepository;
        $this->validator = $interactionsValidator;
    }

    function create(array $data): ?Interaction
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new Exception($this->validator->errors()->first()));
            throw_unless($this->interactionsRepository->slugFromTextIsAvailable($data['name']),
                NameIsNotAvailableException::class);
            $data['slug'] = Str::slug($data['name']);
            $interaction = $this->interactionsRepository->create($data);
            $return = $interaction;
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollback();
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }
}