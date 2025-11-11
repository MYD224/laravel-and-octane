<?php

namespace App\Core\Interface\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

abstract class BaseController
{
    /**
     * Send a success JSON response.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(mixed $data = null, ?string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Send an error JSON response.
     *
     * @param  string  $message
     * @param  int  $status
     * @param  mixed|null  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * Handle exceptions and return JSON response.
     *
     * @param  \Throwable  $e
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleException(Throwable $e, int $status = 500): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
            'trace'   => config('app.debug') ? $e->getTrace() : [],
        ], $status);
    }

    /**
     * Validate incoming request and return validated data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @return array
     */
    protected function validateRequest(Request $request, array $rules): array
    {
        return $request->validate($rules);
    }
}
