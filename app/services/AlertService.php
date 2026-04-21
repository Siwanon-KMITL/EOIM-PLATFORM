<?php

namespace App\Services;

use App\Repositories\AlertRepository;

class AlertService
{
    public function create(int $deviceId, string $type, string $message, string $severity = 'low'): void
    {
        (new AlertRepository())->create([
            'device_id' => $deviceId,
            'alert_type' => $type,
            'message' => $message,
            'severity' => $severity,
        ]);

        app_log("Alert created: device={$deviceId}, type={$type}, severity={$severity}");
    }
}