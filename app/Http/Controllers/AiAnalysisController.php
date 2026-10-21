<?php

namespace App\Http\Controllers;

use App\Services\AiAnalysisService;
use Illuminate\Http\JsonResponse;

class AiAnalysisController extends Controller
{
    public function __invoke(AiAnalysisService $service): JsonResponse
    {
        return response()->json(['data' => $service->analyze()]);
    }
}
