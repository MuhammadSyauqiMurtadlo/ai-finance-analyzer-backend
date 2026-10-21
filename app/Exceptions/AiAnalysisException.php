<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

class AiAnalysisException extends RuntimeException
{
    public function __construct(string $message, private int $status = 502)
    {
        parent::__construct($message);
    }

    // Return true = sudah ditangani, tidak perlu masuk log (error provider sudah di-log manual).
    public function report(): bool
    {
        return true;
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], $this->status);
    }
}
