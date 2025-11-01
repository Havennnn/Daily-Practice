<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\isNull;

trait ApiResponse
{
    protected function successResponse(mixed $data = null, string $message = 'Success', int $status = 200) : JsonResponse{
        $payload = [
            'success' => true,
            'message' => $message
        ];

        if(! is_null($data)) {
            if ($data instanceof AnonymousResourceCollection) {
                $array = $data->response()->getData(true);
                $payload['data'] = $array['data'] ?? [];

                if (isset($array['meta'])) {
                    $meta = $array['meta'];
                    $links = $array['links'];
                    $trimmed = [
                        'current_page' => $meta['current_page'] ?? null,
                        'last_page' => $meta['last_page'] ?? null,
                        'per_page' => $meta['per_page'] ?? null,
                        'total' => $meta['total'] ?? null,
                        'next_page' => $links['next'] ?? null,
                        'prev_page' => $links['prev'] ?? null
                    ];

                    $payload['meta'] = $trimmed;
                }
                return response()->json($payload, $status);
            }

            if ($data instanceof JsonResource) {
                $payload['data'] = $data->resolve();
                return response()->json($payload, $status);
            }
        }

        return response()->json($payload, $status);
    }

    protected function createdResponse($data = null, string $message = 'Response Created', int $status = 201) : JsonResponse {
        return $this->successResponse($data, $message, $status);
    }

    protected function noContentResponse(string $message = '') : JsonResponse {
        return response()->json(null, 204);
    }

    protected function errorResponse(string $message = 'Error', array|object|null $errors = null, int $status = 400) : JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message
        ];

        if(! is_null($errors)) {
            $payload['error'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function validationErrorResponse(array $errors, string $message = 'Validation Failed', int $status = 422) : JsonResponse {
        return $this->errorResponse($message, $errors, $status);
    }
}
