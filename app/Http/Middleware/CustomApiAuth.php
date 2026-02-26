<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $software_id = $request->get("softId");
        $allowed_router = config("customapiauth.allowed_router");
        if ($software_id !== $allowed_router) {
            return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
            ], 401);
        }
        return $next($request);
    }
}
