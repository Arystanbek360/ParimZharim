<?php

declare(strict_types=1);

namespace Modules\Shared\Core\Adapters\Api;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;
use Modules\Shared\Core\Domain\BaseCollection;
use Illuminate\Support\Facades\Log;

/**
 * Class BaseApiController
 */
abstract class BaseApiController
{
    protected BaseTransformer|null $transformer = null;

    /**
     * Return generic json response with the given data.
     */
    protected function respond($data, int $statusCode = 200, array $headers = []): JsonResponse
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * Respond with data after applying transformer.
     * @throws InvalidDataTransformer
     */
    protected function respondWithTransformer($data, int $statusCode = 200, array $headers = []): JsonResponse
    {
        $this->checkTransformer();

        if ($data instanceof BaseCollection) {
            $data = $this->transformer->collection($data);
        } else {
            $data = $this->transformer->item($data);
        }
        Log::info('Data:', $data); // Лог данных для проверки
        return $this->respond($data, $statusCode, $headers);
    }

    /**
     * Respond with success.
     */
    protected function respondSuccess(): JsonResponse
    {
        return $this->respond(null, 204);
    }

    /**
     * Respond with error.
     */
    protected function respondError(string $message, int $statusCode): JsonResponse
    {
        return $this->respond([
            'message' => $message,
            'status_code' => $statusCode
        ], $statusCode);
    }

    /**
     * Respond with unauthorized.
     */
    protected function respondUnauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with forbidden.
     */
    protected function respondForbidden($message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     */
    protected function respondNotFound($message = 'Not Found'): JsonResponse
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     */
    protected function respondInternalError($message = 'Internal Error'): JsonResponse
    {
        return $this->respondError($message, 500);
    }

    /**
     * Check if valid transformer is set.
     * @throws InvalidDataTransformer
     */
    private function checkTransformer(): void
    {
        if (!$this->transformer instanceof BaseTransformer) {
            throw new InvalidDataTransformer();
        }
    }
}
