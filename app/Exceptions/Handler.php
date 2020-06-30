<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Check Validation Status
        if ($exception instanceof ValidationException) {
            $exception = new HttpException(400, $exception->getMessage());
        }

        // Flatten exception for serializing
        $flatException = FlattenException::createFromThrowable($exception);
        $message = $flatException->getMessage();
        $status = $flatException->getStatusCode();

        // Sometimes, message is empty
        if (empty($message)) {
            switch ($flatException->getStatusCode()) {
                case 404:
                    $message = 'That resource could not be found.';
                    break;
                default:
                    $message = 'Whoops, looks like something went wrong.';
            }
        }

        // Format error consistently
        $response = [
            'status' => $status,
            'error' => [
                'message' => $message,
            ]
        ];

        return response()->json($response, $response['status']);
    }
}
