<?php

use App\Http\Controllers\API\v1\ClientReportController;
use App\Http\Middleware\CustomApiAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (){
    Route::get("/telemetry", function () {
        return response()->json([
            "status" => "success",
            "message" => "telemetry",
        ]);
    })->name("telemetry");

    Route::middleware(CustomApiAuth::class)->group(function () {
        Route::get("/report/client/up", [ClientReportController::class, "up"])->name("api.v1.report.client.up");
        Route::get("/report/client/down", [ClientReportController::class, "down"])->name("api.v1.report.client.down");
    });
});