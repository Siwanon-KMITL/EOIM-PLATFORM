<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Repositories\DeviceApiLogRepository;
use App\Repositories\DeviceRepository;
use App\Repositories\SmartMeterRepository;
use App\Services\AlertService;
use App\Services\EOIMService;

class SmartMeterController extends Controller
{
    public function store(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            json_response([
                'status' => 'error',
                'message' => 'Invalid JSON payload'
            ], 400);
        }

        $required = ['device_id', 'voltage', 'current', 'power', 'energy', 'frequency', 'power_factor'];

        foreach ($required as $field) {
            if (!isset($payload[$field])) {
                json_response([
                    'status' => 'error',
                    'message' => "Missing field: {$field}"
                ], 422);
            }
        }

        $deviceId = (int)$payload['device_id'];
        $deviceToken = $_SERVER['HTTP_X_DEVICE_TOKEN'] ?? $payload['device_secret'] ?? null;
        $deviceRepository = new DeviceRepository();
        $device = $deviceRepository->findBySecret($deviceId, (string)$deviceToken);

        if (!$device) {
            json_response([
                'status' => 'error',
                'message' => 'Invalid device token or device not found'
            ], 403);
        }

        $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

        (new DeviceApiLogRepository())->create([
            'device_id' => $deviceId,
            'user_id' => $device['user_id'] ?? null,
            'ip_address' => $remoteIp,
            'endpoint' => '/api/smartmeter/store',
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE)
        ]);

        if (empty($device['ip_address']) && filter_var($remoteIp, FILTER_VALIDATE_IP)) {
            $deviceRepository->updateIpAddress($deviceId, $remoteIp);
        }

        $data = [
            'device_id' => $deviceId,
            'voltage' => (float)$payload['voltage'],
            'current' => (float)$payload['current'],
            'power' => (float)$payload['power'],
            'energy' => (float)$payload['energy'],
            'frequency' => (float)$payload['frequency'],
            'power_factor' => (float)$payload['power_factor'],
            'recorded_at' => date('Y-m-d H:i:s'),
        ];

        (new SmartMeterRepository())->create($data);

        $eoim = new EOIMService();
        $eoim->processReading($data, $device);

        json_response([
            'status' => 'success',
            'message' => 'Reading stored successfully'
        ]);
    }
}