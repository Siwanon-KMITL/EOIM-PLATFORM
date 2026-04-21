<?php

namespace App\Services;

class EOIMService
{
    private AlertService $alertService;
    private PythonAIService $pythonAI;

    public function __construct()
    {
        $this->alertService = new AlertService();
        $this->pythonAI = new PythonAIService();
    }

    public function processReading(array $reading, array $device): void
    {
        app_log("Processing reading for device_id={$reading['device_id']}");

        $this->ruleBasedValidation($reading, $device);

        $anomaly = $this->pythonAI->detectAnomaly([
            'device' => $device,
            'reading' => $reading
        ]);

        if (($anomaly['status'] ?? '') === 'success' && ($anomaly['is_anomaly'] ?? false) === true) {
            $this->alertService->create(
                (int)$reading['device_id'],
                'anomaly_detection',
                $anomaly['message'] ?? 'พบความผิดปกติของการใช้พลังงาน',
                'high'
            );
        }
    }

    private function ruleBasedValidation(array $reading, array $device): void
    {
        if ((float)$reading['voltage'] < 180 || (float)$reading['voltage'] > 260) {
            $this->alertService->create(
                (int)$reading['device_id'],
                'voltage_abnormal',
                "แรงดันไฟฟ้าผิดปกติที่อุปกรณ์ {$device['device_name']}",
                'critical'
            );
        }

        if ((float)$reading['power_factor'] < 0.5) {
            $this->alertService->create(
                (int)$reading['device_id'],
                'low_power_factor',
                "ค่า Power Factor ต่ำผิดปกติที่อุปกรณ์ {$device['device_name']}",
                'medium'
            );
        }

        if ((float)$reading['power'] > 3000) {
            $this->alertService->create(
                (int)$reading['device_id'],
                'high_power_usage',
                "อุปกรณ์ {$device['device_name']} ใช้กำลังไฟสูงผิดปกติ",
                'high'
            );
        }
    }
}
