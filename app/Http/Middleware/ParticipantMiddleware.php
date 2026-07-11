<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParticipantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isParticipant()) {
            return redirect()->route('login')->withErrors(['email' => 'Access Denied: Participant role required.']);
        }

        return $next($request);
    }
}
