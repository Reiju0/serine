<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use PublicFunction;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //Illuminate\Http\Exceptions\PostTooLargeException

        if ($request->is('api/v2/*') == true) {
            $return = false;

            if($exception){
                $return = PublicFunction::errorMessage('MSG50001');
            }

            if($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException){
                $return = PublicFunction::errorMessage('MSG20001');
            }

            if($exception instanceof \Illuminate\Session\TokenMismatchException){
                $return = PublicFunction::errorMessage('MSG40103');
            }

            if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
                $return = PublicFunction::errorMessage('MSG40401');
            }

            if($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){
                $return = PublicFunction::errorMessage('MSG40501');
            }

            if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $return = PublicFunction::errorMessage('MSG40103');
            }

            if($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $return = PublicFunction::errorMessage('MSG40103');
            }

            if($return != false){
                return response()
                    ->json($return)
                    ->withCallback($request->input('callback'));
            }
        }else{
            if($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException){
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Your File(s) is too large', 200);
                } else {
                    return redirect()
                      ->back()
                      ->withInput($request->except('_token'))
                      ->withErrors(['Your File(s) is too large']);
                }
            }

            if($exception instanceof \Illuminate\Session\TokenMismatchException){
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Form is expired, please try again by refreshing this page.', 200);
                } else {
                    return redirect()
                      ->back()
                      ->withInput($request->except('_token'))
                      ->withErrors(['Form is Expired, please try again.']);
                }
            }

            if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Page Not Found.', 404);
                }else{
                    return response()->view('errors.404', [], 404);
                }
            }

            if($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Method Not Allowed.', 405);
                }else{
                    return response()->view('errors.405', [], 405);
                }
            }

        }
        

        return parent::render($request, $exception);
    }
}
