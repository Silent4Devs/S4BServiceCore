<?php

namespace Modules\Stripe\App\Http\Api\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class S4BStripeKeyMiddleware1
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('STRIPE_KEY') || !$request->hasHeader('STRIPE_SECRET')) {
            return response()->json(['error' => 'STRIPE_KEY o STRIPE_SECRET, no se encuentra en los headers'], 400);
        }

        return $next($request);
    }
}
