<?php

namespace App\Exceptions;

use App\Http\Response\ApiResponse;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Symfony\Component\HttpFoundation\Response as ResponseHTTP;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\ErrorHandler\Error\FatalError as FatalError;
use http\Exception\BadMethodCallException;

class Handler extends ExceptionHandler
{
    /**
     * Current active menu of sidebar
     * 
     * @var string
     */
    public $apiResponse;

    /**
     * Instantiate a new controller instance
     * 
     * @return void
     */
    // public function __construct(ApiResponse $apiResponse)
    // {
    //     $this->apiResponse = $apiResponse;
    // }

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        // print_r($exception->getMessage());
        // exit;
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $ex)
    {
        if (!$request->wantsJson()) {
            switch (true) {
                case $ex instanceof \Illuminate\Database\Eloquent\ModelNotFoundException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_NOT_FOUND, 'message' => 'Record not found']);
                    break;
                case $ex instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_NOT_FOUND, 'message' => 'Not found']);
                    break;
                case $ex instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_FORBIDDEN, 'message' => 'Access denied']);
                    break;
                case $ex instanceof \Symfony\Component\HttpKernel\Exception\BadRequestHttpException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_BAD_REQUEST, 'message' => 'Bad request']);
                    break;
                case $ex instanceof BadMethodCallException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_BAD_REQUEST, 'message' => 'Bad method Call']);
                    break;
                case $ex instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_FORBIDDEN, 'message' => 'Method not found']);
                    break;
                case $ex instanceof \Illuminate\Database\QueryException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_UNPROCESSABLE_ENTITY, 'message' => 'Some thing went wrong with your query']);
                    break;
                case $ex instanceof \Illuminate\Http\Exceptions\HttpResponseException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Something went wrong with our system']);
                    break;
                    // case $ex instanceof \Illuminate\Auth\AuthenticationException:
                    //     return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_UNAUTHORIZED, 'message' => 'Unauthorized request']);
                    //     break;
                    // case $ex instanceof \Illuminate\Validation\ValidationException:
                    //     return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_UNPROCESSABLE_ENTITY, 'message' => 'In valid request']);                
                    //     break;
                case $ex instanceof NotAcceptableHttpException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_UNAUTHORIZED, 'message' => 'Unauthorized request']);
                    break;
                case $ex instanceof \Illuminate\Validation\UnauthorizedException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_UNAUTHORIZED, 'message' => 'Unauthorized request']);
                    break;
                case $ex instanceof FatalError:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Something went wrong with our system']);
                    break;
                case $ex instanceof \Illuminate\Http\Exceptions\PostTooLargeException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_REQUEST_ENTITY_TOO_LARGE, 'message' => 'File too large!']);
                    break;
                case $ex instanceof \Carbon\Exceptions\InvalidFormatException:
                    return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Something went wrong with our system']);
                    break;
                    // case $ex instanceof \Exception:
                    //     return response()->view('errors.default', ['code' => ResponseHTTP::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Something went wrong with our system']);
                    //     break;
            }
        }

        return parent::render($request, $ex);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $this->apiResponse = new ApiResponse();

        return $this->apiResponse->respondValidationError($exception->getMessage(), $this->transformErrors($exception));
    }

    /**
     * transform the error messages
     * 
     * @param $exception
     * @return $errors array
     */
    private function transformErrors(ValidationException $exception)
    {
        $errors = [];
        foreach ($exception->errors() as $field => $message) {
            $errors[] = [
                'field' => $field,
                'message' => $message[0],
            ];
        }

        return $errors;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request  $request
     * @param AuthenticationException $exception
     * @return RedirectResponse|JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->wantsJson()) {
            $this->apiResponse = new ApiResponse();
            return $this->apiResponse->respondUnauthorized("Unauthorized request");
        }
        return redirect()->guest(route('login'));
    }
}
