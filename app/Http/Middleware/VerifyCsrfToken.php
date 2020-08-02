<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Cookie;
//use Cookie;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    protected function addCookieToResponse($request, $response)
    {
        $response->headers->setCookie(
            new Cookie('XSRF-TOKEN',
                $request->session()->token(),
                time() + 60 * 120,
                '/',
                null,
                config('session.secure'),
                true)
        );
        //dd($_COOKIE['PHPSESSID']);
        //cartalyst_sentinel
        if(isset($_COOKIE['cartalyst_sentinel'])){
            $response->headers->setCookie(
                new Cookie('cartalyst_sentinel',
                    null,
                    time() + 60 * 120,
                    '/',
                    null,
                    config('session.secure'),
                    true)
            );
        }
        

        return $response;
    }
}
