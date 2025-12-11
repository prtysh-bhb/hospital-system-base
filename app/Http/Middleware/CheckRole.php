<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = auth()->user();
        // this will not let user to login who's role is Patient

        // if($user->role == 'patient'){
        //     if ($request->expectsJson()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'You can not login'
        //         ], 403);
        //     }
        //     return redirect()->route('login')->with('error', 'You can not login');
        // }

        // Check if user has any of the allowed roles
        if (! in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.',
                ], 403);
            }

            // Redirect to appropriate dashboard based on user's actual role
            return redirect()->route($user->role.'.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
