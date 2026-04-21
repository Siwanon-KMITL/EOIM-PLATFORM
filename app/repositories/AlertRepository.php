<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class AlertRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO alerts (device_id, alert_type, message, severity)
             VALUES (:device_id, :alert_type, :message, :severity)"
        );

        return $stmt->execute([
            'device_id' => $data['device_id'],
            'alert_type' => $data['alert_type'],
            'message' => $data['message'],
            'severity' => $data['severity'],
        ]);
    }

    public function latest(int $limit = 20): array
    {
        $stmt = $this->db->prepare("SELECT * FROM alerts ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countBySeverity(): array
    {
        $stmt = $this->db->query(
            "SELECT severity, COUNT(*) AS total
         FROM alerts
         GROUP BY severity"
        );

        return $stmt->fetchAll();
    }

    public function totalCount(int $userId = null): int
    {
        if ($userId === null) {
            $stmt = $this->db->query("SELECT COUNT(*) AS total FROM alerts");
            $row = $stmt->fetch();

            return (int)($row['total'] ?? 0);
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM alerts a
             INNER JOIN devices d ON d.id = a.device_id
             WHERE d.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    public function latestByUser(int $limit, int $userId): array
    {
        $sql = "SELECT a.* FROM alerts a
                INNER JOIN devices d ON d.id = a.device_id
                WHERE d.user_id = :user_id
                ORDER BY a.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countBySeverityByUser(int $userId): array
    {
        $sql = "SELECT a.severity, COUNT(*) AS total
                FROM alerts a
                INNER JOIN devices d ON d.id = a.device_id
                WHERE d.user_id = :user_id
                GROUP BY a.severity";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function latestByDevice(int $deviceId, int $limit = 20): array
{
    $stmt = $this->db->prepare(
        "SELECT *
         FROM alerts
         WHERE device_id = :device_id
         ORDER BY created_at DESC, id DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':device_id', $deviceId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

public function countByDevice(int $deviceId): int
{
    $stmt = $this->db->prepare(
        "SELECT COUNT(*) AS total
         FROM alerts
         WHERE device_id = :device_id"
    );
    $stmt->execute(['device_id' => $deviceId]);
    $row = $stmt->fetch();

    return (int)($row['total'] ?? 0);
}
}
