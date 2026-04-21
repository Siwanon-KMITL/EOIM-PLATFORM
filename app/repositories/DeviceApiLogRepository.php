<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class DeviceApiLogRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO device_api_logs
                (device_id, user_id, endpoint, request_method, request_path, ip_address, user_agent, payload)
             VALUES
                (:device_id, :user_id, :endpoint, :request_method, :request_path, :ip_address, :user_agent, :payload)"
        );

        return $stmt->execute([
            'device_id' => $data['device_id'],
            'user_id' => $data['user_id'] ?? null,
            'endpoint' => $data['endpoint'],
            'request_method' => $data['request_method'] ?? ($_SERVER['REQUEST_METHOD'] ?? 'POST'),
            'request_path' => $data['request_path'] ?? parse_url($_SERVER['REQUEST_URI'] ?? $data['endpoint'], PHP_URL_PATH),
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? null),
            'payload' => $data['payload'],
        ]);
    }
}
