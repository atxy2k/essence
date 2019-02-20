<?php namespace Atxy2k\Essence\Middleware;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 20/2/2019
 * Time: 10:33
 */
use Closure;
use Sentinel;
use Illuminate\Http\Request;

class SentinelGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( Sentinel::guest() )
        {
            return $next($request);
        }
        return redirect()->to(config('essence.pages.main'));
    }
}
