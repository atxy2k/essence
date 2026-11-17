<?php namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Eloquent\ChangeEmailRequest;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Services\ChangeEmailRequestService;
use Atxy2k\Essence\Services\UsersService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Expiration checks compare "now" against a past date. Carbon 3 returns a
 * signed (negative) diff for past dates, so these tests guard that tokens
 * still expire on both Carbon 2 and Carbon 3.
 */
class TokenExpirationTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeUser(string $email): User
    {
        return User::forceCreate([
            'first_name' => 'Token',
            'last_name'  => 'Test',
            'email'      => $email,
            'password'   => Hash::make('passwd'),
            'active'     => true,
        ]);
    }

    public function testPasswordRecoveryTokenExpires()
    {
        DB::beginTransaction();
        config(['essence.password_recovery_timeout' => 30]);
        $this->makeUser('recovery@essence.test');
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);

        $token = $service->requestPasswordRecovery(['email' => 'recovery@essence.test']);
        $this->assertNotNull($token, json_encode($service->errors()));

        Carbon::setTestNow(Carbon::now()->addMinutes(5));
        $this->assertTrue($service->validateRequestPasswordRecovery($token));

        Carbon::setTestNow(Carbon::now()->addMinutes(26));
        $this->assertFalse($service->validateRequestPasswordRecovery($token),
            'A recovery token older than the timeout must be rejected');

        DB::rollBack();
    }

    private function makeChangeEmailRequest(string $email): ChangeEmailRequest
    {
        $user = $this->makeUser($email);
        return ChangeEmailRequest::create([
            'user_id'                   => $user->id,
            'token_confirmation_change' => md5('change'),
            'token_confirmation_email'  => md5('email'),
            'before_email'              => $email,
            'next_email'                => 'new-'.$email,
        ]);
    }

    public function testChangeEmailConfirmationExpires()
    {
        DB::beginTransaction();
        /** @var ChangeEmailRequestService $service */
        $service = $this->app->make(ChangeEmailRequestService::class);

        $this->makeChangeEmailRequest('fresh@essence.test');
        Carbon::setTestNow(Carbon::now()->addMinutes(30));
        $this->assertTrue($service->confirmNewEmail([
            'email' => 'fresh@essence.test',
            'token_confirmation_email' => md5('email'),
        ]), json_encode($service->errors()));
        $this->assertTrue($service->confirmProcess([
            'email' => 'fresh@essence.test',
            'token_confirmation_change' => md5('change'),
        ]), json_encode($service->errors()));

        Carbon::setTestNow();
        $this->makeChangeEmailRequest('old@essence.test');
        Carbon::setTestNow(Carbon::now()->addMinutes(61));
        $this->assertFalse($service->confirmNewEmail([
            'email' => 'old@essence.test',
            'token_confirmation_email' => md5('email'),
        ]), 'A change-email request older than 60 minutes must expire');

        Carbon::setTestNow();
        $this->makeChangeEmailRequest('old2@essence.test');
        Carbon::setTestNow(Carbon::now()->addMinutes(61));
        $this->assertFalse($service->confirmProcess([
            'email' => 'old2@essence.test',
            'token_confirmation_change' => md5('change'),
        ]), 'A change-email request older than 60 minutes must expire');

        DB::rollBack();
    }
}
