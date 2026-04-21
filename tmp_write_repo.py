from pathlib import Path
content = '''<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class DeviceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(int $userId = null): array
    {
        $sql = "SELECT d.*, u.name AS owner_name
                FROM devices d
                LEFT JOIN users u ON u.id = d.user_id";

        $params = [];

        if ($userId !== null) {
            $sql .= " WHERE d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $sql .= " ORDER BY d.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, u.name AS owner_name
             FROM devices d
             LEFT JOIN users u ON u.id = d.user_id
             WHERE d.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findForUser(int $id, int $userId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, u.name AS owner_name
             FROM devices d
             LEFT JOIN users u ON u.id = d.user_id
             WHERE d.id = :id
               AND d.user_id = :user_id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findBySecret(int $id, string $secret): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM devices
             WHERE id = :id
               AND device_secret = :device_secret
             LIMIT 1"
        );
        $stmt->execute([
            'id' => $id,
            'device_secret' => $secret,
        ]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO devices (device_name, device_type, location, status, user_id, ip_address, device_secret)
             VALUES (:device_name, :device_type, :location, :status, :user_id, :ip_address, :device_secret)"
        );

        return $stmt->execute([
            'device_name' => $data['device_name'],
            'device_type' => $data['device_type'],
            'location' => $data['location'],
            'status' => $data['status'],
            'user_id' => $data['user_id'],
            'ip_address' => $data['ip_address'],
            'device_secret' => $data['device_secret'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE devices
             SET device_name = :device_name,
                 device_type = :device_type,
                 location = :location,
                 status = :status,
                 ip_address = :ip_address,
                 device_secret = :device_secret
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'device_name' => $data['device_name'],
            'device_type' => $data['device_type'],
            'location' => $data['location'],
            'status' => $data['status'],
            'ip_address' => $data['ip_address'],
            'device_secret' => $data['device_secret'],
        ]);
    }

    public function updateIpAddress(int $id, string $ipAddress): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE devices
             SET ip_address = :ip_address
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'ip_address' => $ipAddress,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM devices WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function countByStatus(int $userId = null): array
    {
        $sql = "SELECT status, COUNT(*) AS total
                FROM devices";

        $params = [];

        if ($userId !== null) {
            $sql .= " WHERE user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $sql .= " GROUP BY status";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function totalCount(int $userId = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM devices";
        $params = [];

        if ($userId !== null) {
            $sql .= " WHERE user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }
}
'''
Path('c:/xampp/htdocs/EOIM-PLATFORM/app/repositories/DeviceRepository.php').write_text(content, encoding='utf-8')
