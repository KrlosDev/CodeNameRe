<?php

namespace App\Exceptions;

use App\Models\Funciones;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     *
     * @return mixed|void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception) && in_array(config('app.env'), ['production','development'])) {
            app('sentry')->captureException($exception);
        }

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
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->back()->withInput()->with('alert', Funciones::getAlert('error', 'Sesión vencida', 'Su sesión ha expirado. Intente nuevamente por favor.'));
        }
        return parent::render($request, $exception);
    }
}
