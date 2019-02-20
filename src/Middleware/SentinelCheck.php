<?php namespace Atxy2k\Essence\Middleware;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 20/2/2019
 * Time: 10:23
 */
use Closure;
use Sentinel;
use Route;


class SentinelCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( Sentinel::check() )
        {
            return $next($request);
        }
        //$intent_url = $request->fullUrl();
        //return redirect()->to('auth')->with('intent_url', $intent_url);
        return redirect()->to(config('essence.pages.login'));
    }
}
