<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class AnalyticsController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index(): void
    {
        $user = auth_user();
        $isAdmin = has_role('admin');
        $userId = $user['id'] ?? null;

        $deviceWhere = '';
        $readingWhere = '';
        $params = [];

        if (!$isAdmin && $userId !== null) {
            $deviceWhere = 'WHERE d.user_id = :user_id';
            $readingWhere = 'WHERE d.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $summarySql = "
            SELECT
                COALESCE(MAX(r.power), 0) AS peak_power,
                COALESCE(AVG(r.power_factor), 0) AS avg_power_factor,
                COALESCE(COUNT(r.id), 0) AS total_readings,
                MAX(r.recorded_at) AS latest_at,
                MIN(r.recorded_at) AS first_at
            FROM devices d
            LEFT JOIN smartmeter_readings r ON r.device_id = d.id
            $deviceWhere
        ";
        $summaryStmt = $this->db->prepare($summarySql);
        $summaryStmt->execute($params);
        $summary = $summaryStmt->fetch() ?: [];

        $statusSql = "
            SELECT d.status, COUNT(*) AS total
            FROM devices d
            $deviceWhere
            GROUP BY d.status
        ";
        $statusStmt = $this->db->prepare($statusSql);
        $statusStmt->execute($params);
        $statusRows = $statusStmt->fetchAll();

        $statusCounts = [
            'active' => 0,
            'inactive' => 0,
            'maintenance' => 0,
        ];

        foreach ($statusRows as $row) {
            $statusCounts[$row['status']] = (int)($row['total'] ?? 0);
        }

        $alertSql = "
            SELECT COUNT(*) AS total_alerts
            FROM alerts a
            INNER JOIN devices d ON d.id = a.device_id
            $readingWhere
        ";
        $alertStmt = $this->db->prepare($alertSql);
        $alertStmt->execute($params);
        $totalAlerts = (int)(($alertStmt->fetch()['total_alerts'] ?? 0));

        $trendSql = "
            SELECT *
            FROM (
                SELECT
                    DATE_FORMAT(r.recorded_at, '%d %b') AS day_label,
                    DATE_FORMAT(r.recorded_at, '%Y-%m-%d') AS day_key,
                    COALESCE(AVG(r.power), 0) AS avg_power
                FROM smartmeter_readings r
                INNER JOIN devices d ON d.id = r.device_id
                $readingWhere
                GROUP BY DATE_FORMAT(r.recorded_at, '%Y-%m-%d'), DATE_FORMAT(r.recorded_at, '%d %b')
                ORDER BY day_key DESC
                LIMIT 12
            ) trend_source
            ORDER BY day_key ASC
        ";
        $trendStmt = $this->db->prepare($trendSql);
        $trendStmt->execute($params);
        $trendRows = $trendStmt->fetchAll();

        $zoneSql = "
            SELECT *
            FROM (
                SELECT
                    COALESCE(NULLIF(TRIM(d.location), ''), 'Unassigned Zone') AS zone_name,
                    COALESCE(SUM(r.energy), 0) AS total_energy
                FROM smartmeter_readings r
                INNER JOIN devices d ON d.id = r.device_id
                $readingWhere
                GROUP BY COALESCE(NULLIF(TRIM(d.location), ''), 'Unassigned Zone')
                ORDER BY total_energy DESC
                LIMIT 4
            ) zone_source
            ORDER BY total_energy DESC
        ";
        $zoneStmt = $this->db->prepare($zoneSql);
        $zoneStmt->execute($params);
        $zoneRows = $zoneStmt->fetchAll();

        $comparisonSql = "
            SELECT
                d.id,
                d.device_name,
                d.status,
                COALESCE(curr.power, 0) AS current_usage,
                COALESCE(prev.avg_power, 0) AS previous_usage
            FROM devices d
            LEFT JOIN (
                SELECT r1.device_id, r1.power, r1.recorded_at
                FROM smartmeter_readings r1
                INNER JOIN (
                    SELECT device_id, MAX(recorded_at) AS max_recorded_at
                    FROM smartmeter_readings
                    GROUP BY device_id
                ) latest ON latest.device_id = r1.device_id
                    AND latest.max_recorded_at = r1.recorded_at
            ) curr ON curr.device_id = d.id
            LEFT JOIN (
                SELECT device_id, AVG(power) AS avg_power
                FROM smartmeter_readings
                WHERE recorded_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY device_id
            ) prev ON prev.device_id = d.id
            $deviceWhere
            ORDER BY current_usage DESC, d.id DESC
            LIMIT 4
        ";
        $comparisonStmt = $this->db->prepare($comparisonSql);
        $comparisonStmt->execute($params);
        $comparisonRows = $comparisonStmt->fetchAll();

        $totalDevices = array_sum($statusCounts);
        $activeDevices = (int)($statusCounts['active'] ?? 0);
        $activeRatio = $totalDevices > 0 ? ($activeDevices / $totalDevices) * 100 : 0;
        $alertPenalty = min(30, $totalAlerts * 3);
        $healthIndex = max(18, min(100, (int)round($activeRatio - ($alertPenalty / 2))));
        $healthLabel = $healthIndex >= 75 ? 'Optimal' : ($healthIndex >= 45 ? 'Observe' : 'Critical');
        $peakConsumption = (float)($summary['peak_power'] ?? 0) / 1000;
        $gridEfficiency = (float)($summary['avg_power_factor'] ?? 0) * 100;

        $maxTrendValue = 0.0;
        foreach ($trendRows as $row) {
            $maxTrendValue = max($maxTrendValue, (float)($row['avg_power'] ?? 0));
        }

        if (empty($trendRows)) {
            $trendRows = [
                ['day_label' => 'No Data', 'avg_power' => 0],
            ];
        }

        $totalZoneEnergy = 0.0;
        foreach ($zoneRows as $zone) {
            $totalZoneEnergy += (float)($zone['total_energy'] ?? 0);
        }

        $zoneColors = ['bg-primary', 'bg-secondary', 'bg-orange-400', 'bg-slate-400'];
        $zones = [];

        foreach ($zoneRows as $index => $zone) {
            $energy = (float)($zone['total_energy'] ?? 0);
            $zones[] = [
                'name' => $zone['zone_name'],
                'energy' => $energy,
                'share' => $totalZoneEnergy > 0 ? (int)round(($energy / $totalZoneEnergy) * 100) : 0,
                'color_class' => $zoneColors[$index] ?? 'bg-slate-400',
            ];
        }

        if (empty($zones)) {
            $zones[] = [
                'name' => 'No Active Zones',
                'energy' => 0,
                'share' => 0,
                'color_class' => 'bg-slate-400',
            ];
        }

        $this->view('analytics/index', [
            'title' => 'Analytics & Reports',
            'user' => $user,
            'isAdmin' => $isAdmin,
            'summary' => $summary,
            'statusCounts' => $statusCounts,
            'totalDevices' => $totalDevices,
            'totalAlerts' => $totalAlerts,
            'peakConsumption' => $peakConsumption,
            'gridEfficiency' => $gridEfficiency,
            'healthIndex' => $healthIndex,
            'healthLabel' => $healthLabel,
            'trendRows' => $trendRows,
            'maxTrendValue' => $maxTrendValue,
            'zones' => $zones,
            'comparisonRows' => $comparisonRows,
        ]);
    }
}
