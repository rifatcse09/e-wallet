<?php

namespace App\Traits;

use Illuminate\Http\{JsonResponse};
use Illuminate\Http\Response;

trait RespondsWithHttpStatusTrait
{

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected static function success($data = [], $message, $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * Returns json response.
     *
     * @param string $error
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected static function failure($error, $errorMessages = [], $status = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        return response()->json(
            !empty($errorMessages) ?
                [
                    'success' => false,
                    'message' => $error,
                    'data' => $errorMessages,
                ] :
                [
                    'success' => false,
                    'message' => $error,
                ],
            $status
        );
    }
}
