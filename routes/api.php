<?php

use App\Http\Controllers\Api\PeriodStatsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('period-stats')->group(function () {
    Route::get('/current', [PeriodStatsController::class, 'current']);
    Route::get('/timeline', [PeriodStatsController::class, 'timeline']);
    Route::get('/overview', [PeriodStatsController::class, 'overview']);
});
