<?php

namespace App\Services;

class PythonAIService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/python_service.php';
        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->timeout = (int)$config['timeout'];
    }

    private function post(string $endpoint, array $payload): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        if ($ch === false) {
            return ['status' => 'error', 'message' => 'Unable to initialize cURL'];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => $this->timeout,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            app_log("PythonAIService error: {$error}");
            return ['status' => 'error', 'message' => $error];
        }

        if ($statusCode >= 400) {
            app_log("PythonAIService HTTP {$statusCode}: {$response}");
            return ['status' => 'error', 'message' => "AI service returned HTTP {$statusCode}"];
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : ['status' => 'error', 'message' => 'Invalid AI response'];
    }

    public function classifyDevice(array $payload): array
    {
        return $this->post('/classify-device', $payload);
    }

    public function detectAnomaly(array $payload): array
    {
        return $this->post('/detect-anomaly', $payload);
    }

    public function forecastUsage(array $payload): array
    {
        return $this->post('/forecast-usage', $payload);
    }

    public function optimizeUsage(array $payload): array
    {
        return $this->post('/optimize-usage', $payload);
    }
}
