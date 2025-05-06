<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            // Get the model name from the exception
            $model = $exception->getModel();

            if ($model === \App\Models\Patient::class) {
                return response()->json([
                    'message' => 'Patient not found.',
                ], 404);
            }

            // Default message for other models
            return response()->json([
                'message' => 'Resource not found.',
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
