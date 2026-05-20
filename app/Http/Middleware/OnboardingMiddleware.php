<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingMiddleware {
    public function handle(Request $request, Closure $next) {
        $user = Auth::user();
        if ($user && !$user->isAdmin() && !$user->onboarding_completed) {
            $step = $user->onboarding_step ?: 1;
            if (!$request->routeIs('onboarding.*')) {
                return redirect()->route('onboarding.step', ['step' => $step]);
            }
        }
        return $next($request);
    }
}