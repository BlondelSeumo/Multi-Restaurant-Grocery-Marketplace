<?php

namespace App\Exceptions;

use App\Helpers\ResponseError;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     * @param Throwable $e
     * @return void
     *
     *
     * @throws Throwable
     */

    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response
     *
     */
    public function render($request, Throwable $e): JsonResponse|Response
    {
        return $this->handleException($request, $e);
    }

    public function handleException($request, Throwable $exception): JsonResponse
    {
        if ($exception instanceof RouteNotFoundException || $exception instanceof NotFoundHttpException) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: request('language', 'en'))
            ]);
        }

        if ($exception instanceof AuthenticationException) {
            info('auth_token', [$request->header('authorization')]);
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_401,
                'message'   => __('errors.' . ResponseError::ERROR_401, locale: request('language', 'en')),
                'http'      => Response::HTTP_UNAUTHORIZED
            ]);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: request('language', 'en')),
            ]);
        }

        if ($exception instanceof ValidationException) {
            $items = $exception->validator->errors()->getMessages();
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_400,
                'message'   => __('errors.' . ResponseError::ERROR_400, locale: request('language', 'en')),
                'data'      => $items
            ]);
        }

        return $this->errorResponse(500,$exception->getMessage().' in '.$exception->getFile().":".$exception->getLine());
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

}
