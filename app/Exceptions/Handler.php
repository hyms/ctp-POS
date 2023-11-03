<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        $status = $response->status();

        return match ($status) {
            404 => Inertia::render('404')->toResponse($request)->setStatusCode($status),
//                500, 503 => Inertia::render('errors/500')->toResponse($request)->setStatusCode($status),
//                403 => Inertia::render('errors/403')->toResponse($request)->setStatusCode($status),
//                401 => Inertia::render('errors/401')->toResponse($request)->setStatusCode($status),
            419 => redirect()->back()->withErrors(['status' => __('Session Expirada, Intente de nuevo.')]),
            default => $response
        };
    }
}
