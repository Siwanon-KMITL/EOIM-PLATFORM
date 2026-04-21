from pathlib import Path

content = '''<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SmartMeterRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO smartmeter_readings \
                (device_id, voltage, current, power, energy, frequency, power_factor, recorded_at)\
                VALUES\
                (:device_id, :voltage, :current, :power, :energy, :frequency, :power_factor, :recorded_at)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'device_id' => $data['device_id'],
            'voltage' => $data['voltage'],
            'current' => $data['current'],
            'power' => $data['power'],
            'energy' => $data['energy'],
            'frequency' => $data['frequency'],
            'power_factor' => $data['power_factor'],
            'recorded_at' => $data['recorded_at'],
        ]);
    }

    public function latestByDevice(int $deviceId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM smartmeter_readings\n             WHERE device_id = :device_id\n             ORDER BY recorded_at DESC\n             LIMIT :limit"
        );
        $stmt->bindValue(':device_id', $deviceId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function totalPowerToday(int $userId = null): float
    {
        $sql = "SELECT COALESCE(SUM(r.power),0) AS total_power\n                FROM smartmeter_readings r\n                INNER JOIN devices d ON d.id = r.device_id\n                WHERE DATE(r.recorded_at) = CURDATE()";

        $params = [];

        if ($userId !== null) {
            $sql .= " AND d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (float)($row['total_power'] ?? 0);
    }

    public function latestReadingsWithDevice(int $limit = 10, int $userId = null): array
    {
        $sql = "SELECT r.*, d.device_name, d.device_type, d.location\n                FROM smartmeter_readings r\n                INNER JOIN devices d ON d.id = r.device_id";

        $params = [];

        if ($userId !== null) {
            $sql .= " WHERE d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $sql .= " ORDER BY r.recorded_at DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if (isset($params['user_id'])) {
            $stmt->bindValue(':user_id', $params['user_id'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function topDevicesByPowerToday(int $limit = 5, int $userId = null): array
    {
        $sql = "SELECT \
            d.id,\
            d.device_name,\
            d.device_type,\
            d.location,\
            COALESCE(SUM(r.power), 0) AS total_power,\
            COALESCE(SUM(r.energy), 0) AS total_energy,\
            MAX(r.recorded_at) AS latest_time\n         FROM smartmeter_readings r\n         INNER JOIN devices d ON d.id = r.device_id\n         WHERE DATE(r.recorded_at) = CURDATE()";

        $params = [];

        if ($userId !== null) {
            $sql .= " AND d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $sql .= " GROUP BY d.id, d.device_name, d.device_type, d.location\n         ORDER BY total_power DESC\n         LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if (isset($params['user_id'])) {
            $stmt->bindValue(':user_id', $params['user_id'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function totalEnergyToday(int $userId = null): float
    {
        $sql = "SELECT COALESCE(SUM(r.energy),0) AS total_energy\n                FROM smartmeter_readings r\n                INNER JOIN devices d ON d.id = r.device_id\n                WHERE DATE(r.recorded_at) = CURDATE()";

        $params = [];

        if ($userId !== null) {
            $sql .= " AND d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (float)($row['total_energy'] ?? 0);
    }

    public function totalReadingsToday(int $userId = null): int
    {
        $sql = "SELECT COUNT(*) AS total\n                FROM smartmeter_readings r\n                INNER JOIN devices d ON d.id = r.device_id\n                WHERE DATE(r.recorded_at) = CURDATE()";

        $params = [];

        if ($userId !== null) {
            $sql .= " AND d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    public function latestReadingPerDevice(int $userId = null): array
    {
        $sql = "SELECT r.*, d.device_name, d.device_type, d.location\n                FROM smartmeter_readings r\n                INNER JOIN devices d ON d.id = r.device_id\n                INNER JOIN (\n                    SELECT device_id, MAX(recorded_at) AS max_recorded_at\n                    FROM smartmeter_readings\n                    GROUP BY device_id\n                ) latest \n                    ON latest.device_id = r.device_id \n                   AND latest.max_recorded_at = r.recorded_at";

        $params = [];

        if ($userId !== null) {
            $sql = "SELECT sub.*, d.device_name, d.device_type, d.location\n                    FROM (" + $sql + ") sub\n                    INNER JOIN devices d ON d.id = sub.device_id\n                    WHERE d.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function readingsByDevice(int $deviceId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT *\n             FROM smartmeter_readings\n             WHERE device_id = :device_id\n             ORDER BY recorded_at DESC, id DESC\n             LIMIT :limit"
        );
        $stmt->bindValue(':device_id', $deviceId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function latestByDeviceId(int $deviceId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT *\n             FROM smartmeter_readings\n             WHERE device_id = :device_id\n             ORDER BY recorded_at DESC, id DESC\n             LIMIT 1"
        );
        $stmt->execute(['device_id' => $deviceId]);

        return $stmt->fetch();
    }

    public function summaryByDevice(int $deviceId): array
    {
        $stmt = $this->db->prepare(
            "SELECT\n                COUNT(*) AS total_readings,\n                COALESCE(SUM(power), 0) AS total_power,\n                COALESCE(SUM(energy), 0) AS total_energy,\n                COALESCE(AVG(voltage), 0) AS avg_voltage,\n                COALESCE(AVG(current), 0) AS avg_current,\n                COALESCE(AVG(power_factor), 0) AS avg_power_factor,\n                MIN(recorded_at) AS first_recorded_at,\n                MAX(recorded_at) AS last_recorded_at\n             FROM smartmeter_readings\n             WHERE device_id = :device_id"
        );
        $stmt->execute(['device_id' => $deviceId]);

        $row = $stmt->fetch();

        return $row ?: [
            'total_readings' => 0,
            'total_power' => 0,
            'total_energy' => 0,
            'avg_voltage' => 0,
            'avg_current' => 0,
            'avg_power_factor' => 0,
            'first_recorded_at' => null,
            'last_recorded_at' => null,
        ];
    }

    public function summaryTodayByDevice(int $deviceId): array
    {
        $stmt = $this->db->prepare(
            "SELECT\n                COUNT(*) AS total_readings_today,\n                COALESCE(SUM(power), 0) AS total_power_today,\n                COALESCE(SUM(energy), 0) AS total_energy_today,\n                COALESCE(AVG(voltage), 0) AS avg_voltage_today,\n                COALESCE(AVG(current), 0) AS avg_current_today,\n                COALESCE(AVG(power_factor), 0) AS avg_power_factor_today\n             FROM smartmeter_readings\n             WHERE device_id = :device_id\n               AND DATE(recorded_at) = CURDATE()"
        );
        $stmt->execute(['device_id' => $deviceId]);

        $row = $stmt->fetch();

        return $row ?: [
            'total_readings_today' => 0,
            'total_power_today' => 0,
            'total_energy_today' => 0,
            'avg_voltage_today' => 0,
            'avg_current_today' => 0,
            'avg_power_factor_today' => 0,
        ];
    }
}
'''

Path('c:/xampp/htdocs/EOIM-PLATFORM/app/repositories/SmartMeterRepository.php').write_text(content, encoding='utf-8')
