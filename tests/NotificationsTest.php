<?php


namespace Atxy2k\Essence\Tests;
use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Services\InteractionsTypeService;
use Atxy2k\Essence\Services\RolesService;
use Atxy2k\Essence\Services\UsersService;
use Atxy2k\Essence\Tests\Notifications\SimpleTestNotification;
use DB;
use Illuminate\Support\Collection;

class NotificationsTest extends TestCase
{
    public function testSimpleNotificationToUser()
    {
       DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [];
        $data['first_name'] = 'ivan';
        $data['last_name'] = 'alvarado';
        $data['email'] = 'dev@serprogramador.es';
        $data['email_confirmation'] = 'dev@serprogramador.es';
        $data['password'] = 'passwd';
        $data['password_confirmation'] = 'passwd';
        $data['roles'] = [$role->id];

        $item = $service->register($data);
        $this->assertNotNull($item);

        $item->notify(new SimpleTestNotification());
        $notifications = $item->notifications;
        $this->assertNotNull($notifications);
        $this->assertInstanceOf(Collection::class, $notifications);
        $this->assertEquals(1, $notifications->count());
        $this->assertEquals(SimpleTestNotification::class, $notifications->first()->type);

       DB::rollback();
    }
}