<?php

namespace App\Http\Middleware;

use Closure;

class InstallMiddleware
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

        if(getenv('IS_INSTALLED') == 'true'){

            // is already installed, so permit to access the request route, expect install actions.
            if(substr($request->path(), 0, 7) == 'install') {
                return redirect('/');
            }

            return $next($request);
        }
        else {
            // not installed yet, has to redirect to install action whatever the current request route is, except path to install.

            if(substr($request->path(), 0, 7) != 'install') {
                return redirect('install');
            }

            return $next($request);
        }
    }
}
