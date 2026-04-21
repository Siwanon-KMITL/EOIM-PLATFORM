<?php

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
        $sql = "INSERT INTO smartmeter_readings 
                (device_id, voltage, current, power, energy, frequency, power_factor, recorded_at)
                VALUES
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
            "SELECT * FROM smartmeter_readings
             WHERE device_id = :device_id
             ORDER BY recorded_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':device_id', $deviceId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function totalPowerToday(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(power),0) AS total_power
             FROM smartmeter_readings
             WHERE DATE(recorded_at) = CURDATE()"
        );
        $row = $stmt->fetch();
        return (float)($row['total_power'] ?? 0);
    }

    public function latestReadingsWithDevice(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, d.device_name, d.device_type, d.location
         FROM smartmeter_readings r
         INNER JOIN devices d ON d.id = r.device_id
         ORDER BY r.recorded_at DESC
         LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function topDevicesByPowerToday(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
            d.id,
            d.device_name,
            d.device_type,
            d.location,
            COALESCE(SUM(r.power), 0) AS total_power,
            COALESCE(SUM(r.energy), 0) AS total_energy,
            MAX(r.recorded_at) AS latest_time
         FROM smartmeter_readings r
         INNER JOIN devices d ON d.id = r.device_id
         WHERE DATE(r.recorded_at) = CURDATE()
         GROUP BY d.id, d.device_name, d.device_type, d.location
         ORDER BY total_power DESC
         LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function totalEnergyToday(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(energy),0) AS total_energy
         FROM smartmeter_readings
         WHERE DATE(recorded_at) = CURDATE()"
        );
        $row = $stmt->fetch();

        return (float)($row['total_energy'] ?? 0);
    }

    public function totalReadingsToday(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total
         FROM smartmeter_readings
         WHERE DATE(recorded_at) = CURDATE()"
        );
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    public function latestReadingPerDevice(): array
    {
        $sql = "
        SELECT r.*, d.device_name, d.device_type, d.location
        FROM smartmeter_readings r
        INNER JOIN devices d ON d.id = r.device_id
        INNER JOIN (
            SELECT device_id, MAX(recorded_at) AS max_recorded_at
            FROM smartmeter_readings
            GROUP BY device_id
        ) latest 
            ON latest.device_id = r.device_id 
           AND latest.max_recorded_at = r.recorded_at
        ORDER BY r.recorded_at DESC
    ";

        return $this->db->query($sql)->fetchAll();
    }

    public function readingsByDevice(int $deviceId, int $limit = 50): array
{
    $stmt = $this->db->prepare(
        "SELECT *
         FROM smartmeter_readings
         WHERE device_id = :device_id
         ORDER BY recorded_at DESC, id DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':device_id', $deviceId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

public function latestByDeviceId(int $deviceId): array|false
{
    $stmt = $this->db->prepare(
        "SELECT *
         FROM smartmeter_readings
         WHERE device_id = :device_id
         ORDER BY recorded_at DESC, id DESC
         LIMIT 1"
    );
    $stmt->execute(['device_id' => $deviceId]);

    return $stmt->fetch();
}

public function summaryByDevice(int $deviceId): array
{
    $stmt = $this->db->prepare(
        "SELECT
            COUNT(*) AS total_readings,
            COALESCE(SUM(power), 0) AS total_power,
            COALESCE(SUM(energy), 0) AS total_energy,
            COALESCE(AVG(voltage), 0) AS avg_voltage,
            COALESCE(AVG(current), 0) AS avg_current,
            COALESCE(AVG(power_factor), 0) AS avg_power_factor,
            MIN(recorded_at) AS first_recorded_at,
            MAX(recorded_at) AS last_recorded_at
         FROM smartmeter_readings
         WHERE device_id = :device_id"
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
        "SELECT
            COUNT(*) AS total_readings_today,
            COALESCE(SUM(power), 0) AS total_power_today,
            COALESCE(SUM(energy), 0) AS total_energy_today,
            COALESCE(AVG(voltage), 0) AS avg_voltage_today,
            COALESCE(AVG(current), 0) AS avg_current_today,
            COALESCE(AVG(power_factor), 0) AS avg_power_factor_today
         FROM smartmeter_readings
         WHERE device_id = :device_id
           AND DATE(recorded_at) = CURDATE()"
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

    public function totalPowerTodayByUser(int $userId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(r.power),0) AS total_power
             FROM smartmeter_readings r
             INNER JOIN devices d ON d.id = r.device_id
             WHERE DATE(r.recorded_at) = CURDATE()
               AND d.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return (float)($row['total_power'] ?? 0);
    }

    public function totalEnergyTodayByUser(int $userId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(r.energy),0) AS total_energy
             FROM smartmeter_readings r
             INNER JOIN devices d ON d.id = r.device_id
             WHERE DATE(r.recorded_at) = CURDATE()
               AND d.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return (float)($row['total_energy'] ?? 0);
    }

    public function totalReadingsTodayByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM smartmeter_readings r
             INNER JOIN devices d ON d.id = r.device_id
             WHERE DATE(r.recorded_at) = CURDATE()
               AND d.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }

    public function latestReadingsWithDeviceByUser(int $limit, int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, d.device_name, d.device_type, d.location
             FROM smartmeter_readings r
             INNER JOIN devices d ON d.id = r.device_id
             WHERE d.user_id = :user_id
             ORDER BY r.recorded_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function topDevicesByPowerTodayByUser(int $limit, int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
            d.id,
            d.device_name,
            d.device_type,
            d.location,
            COALESCE(SUM(r.power), 0) AS total_power,
            COALESCE(SUM(r.energy), 0) AS total_energy,
            MAX(r.recorded_at) AS latest_time
             FROM smartmeter_readings r
             INNER JOIN devices d ON d.id = r.device_id
             WHERE DATE(r.recorded_at) = CURDATE()
               AND d.user_id = :user_id
             GROUP BY d.id, d.device_name, d.device_type, d.location
             ORDER BY total_power DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function latestReadingPerDeviceByUser(int $userId): array
    {
        $sql = "SELECT r.*, d.device_name, d.device_type, d.location
             FROM smartmeter_readings r
             INNER JOIN devices d ON d.id = r.device_id
             INNER JOIN (
                 SELECT device_id, MAX(recorded_at) AS max_recorded_at
                 FROM smartmeter_readings
                 GROUP BY device_id
             ) latest
                 ON latest.device_id = r.device_id
                AND latest.max_recorded_at = r.recorded_at
             WHERE d.user_id = :user_id
             ORDER BY r.recorded_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
