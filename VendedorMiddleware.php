<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendedorMiddleware
{
    /**
     * R4: só deixa passar usuários do tipo "vendedor".
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->tipo !== 'vendedor') {
            abort(403);
        }

        return $next($request);
    }
}
