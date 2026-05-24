<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // SUNTIKAN GAIB: Titip pesan error pas ditendang ke login
            session()->flash('error', 'Your session has expired, please log in again.');
            
            return route('login');
        }
    }
}
