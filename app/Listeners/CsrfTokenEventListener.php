<?php

namespace Common\Listeners;

use Moogula\Contracts\Http\Request as RequestContract;
use Moogula\Session\Exception\TokenMismatchException;

class CsrfTokenEventListener
{


    public function handle()
    {
        $request = $this->getRequest();

        if (
            $this->isReading($request) ||
            $this->tokensMatch($request)
        ) {

        } else {
            $e = new TokenMismatchException(419, 'CSRF token mismatch.');
            $e->setHeaders(['_token' => session()->token()]);

            throw $e;
        }
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  RequestContract  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  RequestContract  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);

        return is_string(session()->token()) &&
            is_string($token) &&
            hash_equals(session()->token(), $token);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  RequestContract  $request
     * @return string
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: '';

        return $token;
    }

    /**
     * get request instance
     * @return RequestContract
     */
    protected function getRequest()
    {
        return app(RequestContract::class);
    }
}
