<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
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
            if ($data instanceof LengthAwarePaginator) {
                $payload['data'] = $data->items();
                $payload['meta'] = [
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                ];
            } else {
                $payload['data'] = $data;
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
