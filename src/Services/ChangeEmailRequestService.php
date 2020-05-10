<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:34
 */

use Atxy2k\Essence\Exceptions\EmailRequests\EmailNotAvailableException;
use Atxy2k\Essence\Exceptions\EmailRequests\InvalidTokenException;
use Atxy2k\Essence\Exceptions\EmailRequests\RequestConfirmedException;
use Atxy2k\Essence\Exceptions\EmailRequests\RequestExpiredException;
use Atxy2k\Essence\Exceptions\EmailRequests\RequestNotFoundException;
use Atxy2k\Essence\Exceptions\Essence\UnexpectedException;
use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Atxy2k\Essence\Eloquent\ChangeEmailRequest;
use Atxy2k\Essence\Infraestructure\Service;
use Sentinel;
use Atxy2k\Essence\Repositories\ChangeEmailRequestRepository;
use Atxy2k\Essence\Validators\ChangeEmailRequestValidator;
use Carbon\Carbon;
use Atxy2k\Essence\Repositories\UsersRepository;
use DB;
use Throwable;
use Exception;

class ChangeEmailRequestService extends Service
{
    /** @var ChangeEmailRequestRepository */
    protected $requestRepository;
    /** @var UsersRepository */
    protected $usersRepository;

    public function __construct(ChangeEmailRequestRepository $changeEmailRequestRepository,
                                ChangeEmailRequestValidator $changeEmailRequestValidator,
                                UsersRepository $usersTypeRepository)
    {
        parent::__construct();
        $this->requestRepository = $changeEmailRequestRepository;
        $this->validator = $changeEmailRequestValidator;
        $this->usersRepository = $usersTypeRepository;
    }

    /**
     * Create a request for change user's email
     * @param $data
     * @return ChangeEmailRequest|Model|null
     */
    public function create($data) : ?ChangeEmailRequest
    {
        $return = null;
        if ( $this->validator->with( $data )->passes() )
        {
            $data = $data + [
                    'token_confirmation_change' => md5(str_random(12)),
                    'token_confirmation_email'  => md5(str_random(12)),
                ];
            $return = $this->requestRepository->create( $data );
        }
        return $return;
    }

    /**
     * Need two confirmation's types, confirm the new email, and confirm process from old email before change it. This
     * function confirm new email.
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function confirmNewEmail(array $data) : bool
    {
        $return = false;
        try {
            DB::beginTransaction();
            if ( $this->validator->with($data)->passes('confirm_process_mail') )
            {
                $request = $this->requestRepository->findByEmail( $data['email'] );
                throw_if(is_null($request), RequestNotFoundException::class);
                $now = Carbon::now();
                if ( $now->diffInMinutes( $request->created_at ) <=60 )
                {
                    throw_unless($request->token_confirmation_email == $data['token_confirmation_email'], InvalidTokenException::class);
                    throw_unless($request->email_confirmed == 0, RequestConfirmedException::class);
                    $request->email_confirmed = true;
                    $request->save();
                    $this->checkComplete($request->id);
                    $return = true;
                }
                else
                {
                    $request->delete();
                    throw new RequestExpiredException;
                }
            }
            else
            {
                throw new UnexpectedException( $this->validator->errors()->first() );
            }
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollBack();
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Need two confirmation's types, confirm the new email, and confirm process from old email before change it. This
     * function confirm the process from old email.
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function confirmProcess( array $data ) : bool
    {
        $return = false;
        try {
            DB::beginTransaction();
            if ( $this->validator->with($data)->passes('confirm_process') )
            {
                $request = $this->requestRepository->findByEmail( $data['email'] );
                throw_if(is_null($request), RequestNotFoundException::class);
                $now = Carbon::now();
                if ( $now->diffInMinutes( $request->created_at ) <=60 )
                {
                    throw_unless($request->token_confirmation_change == $data['token_confirmation_change'], InvalidTokenException::class);
                    throw_unless($request->confirmated == 0, RequestConfirmedException::class);
                    $request->confirmated = true;
                    $request->save();
                    $this->checkComplete($request->id);
                    $return = true;
                }
                else
                {
                    $request->delete();
                    throw new RequestExpiredException;
                }
            }
            else
            {
                throw new UnexpectedException( $this->validator->errors()->first() );
            }
            DB::commit();
        } catch (Throwable $e)
        {
            DB::rollBack();
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * When both confirmations are completed, the change is do it, at the end of each confirm function this functions is
     * called for check if both confirms are done and do the change.
     * @param int $id
     * @return bool
     */
    public function checkComplete(int $id ) : bool
    {
        $return = false;
        $request = $this->requestRepository->find($id);
        try {
            throw_if(is_null($request), RequestNotFoundException::class);
            if ( $request->confirmated == 1 && $request->email_confirmed )
            {
                throw_if(is_null($request->user), UserNotFoundException::class);
                $new_email = $request->next_email;
                $user_with_email = $this->usersRepository->query()->where('email', $new_email)->first();
                throw_unless(is_null($user_with_email), EmailNotAvailableException::class);
                $user = $request->user;
                $user->email = $new_email;
                $user->save();
                //$user->notify( new EmailChanged() );
                $request->delete();
                $return = true;
            }
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}
