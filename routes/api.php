<?php

use App\Core\Router;
use App\Controllers\Api\SmartMeterController;

Router::post('/api/smartmeter/store', [SmartMeterController::class, 'store']);