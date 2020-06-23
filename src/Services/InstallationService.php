<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Constants\Interactions;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotCreatedException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotCreatedException;
use Atxy2k\Essence\Exceptions\Users\UserNotCreatedException;
use Atxy2k\Essence\Infraestructure\Service;
use DB;
use Throwable;

class InstallationService extends Service
{
    /** @var RolesService */
    protected $rolesService;
    /** @var UsersService */
    protected $usersService;
    /** @var InteractionsTypeService */
    protected $interactionTypeService;

    /**
     * InstallationService constructor.
     */
    public function __construct(RolesService $rolesService,
        UsersService $usersService, InteractionsTypeService $interactionTypeService)
    {
        parent::__construct();
        $this->rolesService = $rolesService;
        $this->usersService = $usersService;
        $this->interactionTypeService = $interactionTypeService;
    }

    public function install() : bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();

            /************************************************************
             * Create interactions
             ************************************************************/
            $interaction_create_type = $this->interactionTypeService->create([
                'name' => Interactions::CREATE,
                'description' => 'Create element'
            ]);
            throw_if(is_null($interaction_create_type), InteractionNotCreatedException::class);

            $interaction_update_type = $this->interactionTypeService->create([
                'name' => Interactions::UPDATE,
                'description' => 'Update element'
            ]);
            throw_if(is_null($interaction_update_type), InteractionNotCreatedException::class);

            $interaction_delete_type = $this->interactionTypeService->create([
                'name' => Interactions::DELETE,
                'description' => 'Delete element'
            ]);
            throw_if(is_null($interaction_delete_type), InteractionNotCreatedException::class);

            $interaction_login_type = $this->interactionTypeService->create([
                'name' => Interactions::LOGIN,
                'description' => 'User login'
            ]);
            throw_if(is_null($interaction_login_type), InteractionNotCreatedException::class);

            $interaction_authenticate_type = $this->interactionTypeService->create([
                'name' => Interactions::AUTHENTICATE,
                'description' => 'Throw when someone authenticate user'
            ]);
            throw_if(is_null($interaction_authenticate_type), InteractionNotCreatedException::class);

            $interaction_activate_type = $this->interactionTypeService->create([
                'name' => Interactions::ACTIVATE,
                'description' => 'Throw when someone activate user'
            ]);
            throw_if(is_null($interaction_activate_type), InteractionNotCreatedException::class);

            $interaction_deactivate_type = $this->interactionTypeService->create([
                'name' => Interactions::DEACTIVATE,
                'description' => 'Throw when someone deactivate user'
            ]);
            throw_if(is_null($interaction_deactivate_type), InteractionNotCreatedException::class);

            /************************************************************
             * Create developer role
             ************************************************************/

            $role_data = [
                'name' => ucfirst(config('essence.admin_role_slug','developer'))
            ];
            $developer_role = $this->rolesService->create($role_data);
            throw_if(is_null($developer_role),
                new ValidationException($this->rolesService->errors()->first()));

            $data = [
                'first_name' => config('essence.default_user.first_name'),
                'last_name' => config('essence.default_user.last_name'),
                'email'     => config('essence.default_user.email'),
                'email_confirmation' => config('essence.default_user.email'),
                'password'  => config('essence.default_user.password'),
                'password_confirmation' => config('essence.default_user.password'),
                'roles'     => [$developer_role->id],
                'activate'  => true
            ];

            $user = $this->usersService->register($data);
            throw_if(is_null($user),
                new ValidationException($this->usersService->errors()->first()));

            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $return;
    }
}