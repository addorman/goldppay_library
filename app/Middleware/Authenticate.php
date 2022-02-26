<?php

namespace Common\Middleware;

use Closure;
use Moogula\Auth\AuthenticationException;
use Moogula\Auth\Middleware\Authenticate as Middleware;
use Moogula\Contracts\Http\Request as RequestContract;

class Authenticate extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'index/login', 'ajax/lang',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  RequestContract  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        return parent::handle($request, $next, ...$guards);
    }

        /**
     * Handle an unauthenticated user.
     *
     * @param  RequestContract  $request
     * @param  array  $guards
     * @return void
     *
     * @throws AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            __('Unauthenticated.'), $guards, $this->redirectTo($request)
        );
    }


    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  RequestContract  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return redirect('index/login');
        }
    }
}
