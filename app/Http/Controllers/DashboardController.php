<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $service): JsonResponse
    {
        return response()->json(['data' => $service->get()]);
    }
}
