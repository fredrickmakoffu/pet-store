<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Validation\ValidationException;
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
        $this->renderable(function (Exception $exception, $request) {
            if($request->is('api/*')) {
                if ($exception instanceof QueryException) {
                    return response()->json([
                        'message' => 'Record not saved! Please reach out to support.',
                        'error' => $exception->getMessage()
                    ], 500);
                } elseif ($exception instanceof HttpException) {
                    return response()->json([
                        'message' => $exception->getMessage(),
                    ], $exception->getStatusCode());
                } elseif ($exception instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Validation failed!',
                        'errors' => $exception->errors()
                    ], 422);
                } else {
                    return response()->json([
                        'message' => 'Something went wrong! Please reach out to support.',
                        'error' => $exception->getMessage()
                    ], 500);
                }
            }
        });
    }
}
