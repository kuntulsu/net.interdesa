<?php

use App\Http\Controllers\API\v1\ClientReportController;
use App\Http\Controllers\API\v1\ServerReportController;
use Illuminate\Support\Facades\Schedule;




Schedule::call(fn () => ClientReportController::health_report())
    ->hourly()
    ->name("Notify Telegram About Client Health")
    ->timezone('Asia/Jakarta');
Schedule::call(fn() => ServerReportController::serverReport())
    ->hourly()
    ->name("Notify Telegram About Server Health")
    ->timezone('Asia/Jakarta');