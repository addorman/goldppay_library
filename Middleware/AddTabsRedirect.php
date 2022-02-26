<?php

namespace App\Middleware;

use Moogula\Contracts\Http\Request as RequestContract;
use Moogula\Contracts\Http\Response as ResponseContract;

class AddTabsRedirect
{

    /**
     * Handle an incoming request.
     *
     * @param  RequestContract  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @return mixed
     *
     * @throws ResponseContract
     */
    public function handle($request, $next, $module = '')
    {
        // 非选项卡时重定向
        if ($request->method() == 'GET' && !$request->get('addtabs', 0) && $request->get("ref") == 'addtabs') {
            $url = preg_replace_callback("/([\?|&]+)ref=addtabs(&?)/i", function ($matches) {
                return $matches[2] == '&' ? $matches[1] : '';
            }, $request->fullUrl());

            return redirect("{$module}", 302, ['referer' => $url]);
        }

        return $next($request);
    }
}
