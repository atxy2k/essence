<?php namespace Atxy2k\Essence\Middleware;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 20/2/2019
 * Time: 10:35
 */

use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Closure;
use Sentinel;
use Illuminate\Http\Request;

class SentinelAdministrator
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws \Throwable
     */
    public function handle($request, Closure $next)
    {
        if ( Sentinel::check() )
        {
            $user = Sentinel::getUser();
            throw_if(is_null($user), UserNotFoundException::class);
            if($user->is_admin)
                return $next($request);
        }
        //$intent_url = $request->fullUrl();
        //return redirect()->to('auth')->with('intent_url', $intent_url);
        return redirect()->to(config('essence.pages.main'));
    }
}
