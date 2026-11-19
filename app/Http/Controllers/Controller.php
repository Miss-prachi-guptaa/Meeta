<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class Controller
{
    protected function execute(callable $callback): JsonResponse
    {
        try {

            return $callback();

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Resource not found.',
            ], 404);

        } catch (\Illuminate\Database\QueryException $e) {

            Log::error('Database Exception', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
            ]);

            $message = 'A database error occurred. Please check that all required fields are filled.';
            if (str_contains($e->getMessage(), 'cannot be null')) {
                preg_match("/Column '([^']+)' cannot be null/", $e->getMessage(), $matches);
                $col = $matches[1] ?? 'A required field';
                $fieldLabel = ucwords(str_replace('_', ' ', $col));
                $message = "{$fieldLabel} is required and cannot be left blank.";
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                $message = 'A record with this unique value (such as SKU or barcode) already exists.';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error_detail' => app()->isLocal() ? $e->getMessage() : null,
            ], 422);

        } catch (Throwable $e) {

            Log::error('API Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while processing your request. Please try again.',
                'error_detail' => app()->isLocal() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
