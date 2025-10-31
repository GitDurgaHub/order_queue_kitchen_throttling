<?php
namespace App\Http\Exceptions;

use Exception;
use Throwable;

/**
 * Unified HTTP Exception handler for Slim 3 APIs
 * Handles both string messages and JSON-encoded payloads
 */
class HttpException extends Exception
{
    protected $statusCode;
    protected $errorPayload;

    public function __construct(
        string $message = '',
        int $statusCode = 400,
        Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;

        // Detect if message is JSON
        $decoded = json_decode($message, true);
        $this->errorPayload = json_last_error() === JSON_ERROR_NONE ? $decoded : $message;
    }

    public function getStatus(): int
    {
        return $this->statusCode;
    }

    public function getErrorPayload()
    {
        return $this->errorPayload;
    }

    public function toArray(): array
    {
        // If message is an array (like validation errors)
        if (is_array($this->errorPayload)) {
            return [
                'success' => false,
                'error' => [
                    'code' => $this->statusCode,
                    'details' => $this->errorPayload
                ]
            ];
        }

        // Simple string message
        return [
            'success' => false,
            'error' => [
                'code' => $this->statusCode,
                'message' => $this->errorPayload
            ]
        ];
    }
}
