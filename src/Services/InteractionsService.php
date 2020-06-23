<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotCreatedException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\InteractionsRepository;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Validators\InteractionsValidator;
use Throwable;
use Exception;
use DB;
use Auth;

class InteractionsService extends Service
{
    /** @var InteractionsRepository */
    protected $interactionsRepository;
    /** @var InteractionsTypeRepository */
    protected $interactionsTypeRepository;

    public function __construct(InteractionsRepository $interactionsRepository,
                                InteractionsTypeRepository $interactionsTypeRepository,
                                InteractionsValidator $interactionsValidator)
    {
        parent::__construct();
        $this->interactionsRepository = $interactionsRepository;
        $this->validator = $interactionsValidator;
        $this->interactionsTypeRepository = $interactionsTypeRepository;
    }

    public function create(array $data) : ?Interaction
    {
        $interaction = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new Exception($this->validator->errors()->first()));
            $data['created_by'] = Auth::check() ? Auth::id() : null;
            $interaction = $this->interactionsRepository->create($data);
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $interaction;
    }

    public function generate(string $interaction_type_slug, object $interactuable) : ?Interaction
    {
        $interaction = null;
        try
        {
            DB::beginTransaction();
            $interaction_type = $this->interactionsTypeRepository->findBySlug($interaction_type_slug);
            throw_if(is_null($interaction_type), InteractionNotFoundException::class);
            $interaction = $this->create([
                'interaction_id' => $interaction_type->id,
                'interactuable_id' => $interactuable->id,
                'interactuable_type' => get_class($interactuable),
                'created_by'   => Auth::check() ? Auth::id() : null
            ]);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $interaction;
    }

}