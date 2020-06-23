<?php


namespace Atxy2k\Essence\Middleware;
use Atxy2k\Essence\Exceptions\Mobile\TokenNotFoundException;
use Atxy2k\Essence\Exceptions\Mobile\UnAuthorizedException;
use Closure;
use Illuminate\Http\Response;
use Mobile;
use Throwable;

class MobileAuthenticatedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next )
    {
        $token = $request->get('token', $request->bearerToken());
        try
        {
            throw_if(is_null($token), TokenNotFoundException::class);
            /** Initializing mobile data */
            Mobile::with($token);
            throw_unless( Mobile::check(), UnAuthorizedException::class );
            return $next($request);
        }
        catch (Throwable $e)
        {
            abort(Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }
}