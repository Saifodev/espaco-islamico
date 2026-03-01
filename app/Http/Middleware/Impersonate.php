<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Impersonate
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('impersonate')) {
            $impersonatedUserId = session()->get('impersonate');
            
            if ($request->user() && $request->user()->id != $impersonatedUserId) {
                $impersonatedUser = \App\Models\User::find($impersonatedUserId);
                
                if ($impersonatedUser) {
                    auth()->onceUsingId($impersonatedUserId);
                }
            }
        }

        return $next($request);
    }
}