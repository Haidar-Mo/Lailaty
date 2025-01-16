<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\{
    QueryException,
    Eloquent\ModelNotFoundException,
    UniqueConstraintViolationException,
};
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Exception|Throwable $e)
    {

        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }


        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => explode('\\', $e->getModel())[2] . ' Not Found.',
            ], 404);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => '.هذا الإجراء غير مصرح به',
            ], 403);
        }

        if ($e instanceof UniqueConstraintViolationException) {
            return response()->json([
                'message' => '.هذا السجل موجود بالفعل',
            ]);
        }

       /* if ($e instanceof QueryException) {
            return response()->json([
                'message' => 'Unknown sql error.',
            ]);
        }*/

        if ($e instanceof RouteNotFoundException) {
            return response()->json([
                'message' => '.الرجاء تسجيل الدخول أولا',
            ], 401);
        }

        if (
            $e instanceof InActiveAccountException ||
            $e instanceof IncorrectBillException
        ) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        if ($e instanceof ProductNotExistForSupplierException) {
            return response()->json([
                'message' => $e->getMessage(),
                'product_id' => $e->product->id
            ],$e->getCode());
        }

        // if ($e instanceof PusherException) {
        //     // Log the Pusher exception
        //     Log::error('PusherException: ' . $e->getMessage());
        //     // Return an appropriate response (e.g., 500 Internal Server Error)
        //     return response()->json([
        //         'message' => '.حدث خطأ ما، حاول مرة أخرى لاحقًا'
        //     ], 500);
        // }

        if ($e instanceof ConnectionException) {
            // Log the queue connection exception
            Log::error('Queue ConnectionException: ' . $e->getMessage());
            // Return an appropriate response (e.g., 503 Service Unavailable)
            return response()->json([
                'message' => '.حدث خطأ ما، حاول مرة أخرى لاحقًا'
            ], 503);
        }

        if ($e instanceof JobTimeoutException) {
            // Log the job timeout exception
            Log::error('JobTimeoutException: ' . $e->getMessage());
            // Return an appropriate response (e.g., 504 Gateway Timeout)
            return response()->json([
                'message' => '.حدث خطأ ما، حاول مرة أخرى لاحقًا'
            ], 504);
        }

        // // general case exception message.
        // if ($e instanceof Throwable) {
        //     return response()->json('something went wrong, try again later.',500);
        // }


        return parent::render($request, $e);
    }
}
