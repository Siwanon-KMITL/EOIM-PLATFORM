<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class AlertsController extends Controller
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

        $whereSql = '';
        $params = [];

        if (!$isAdmin && $userId !== null) {
            $whereSql = 'WHERE d.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $countsSql = "
            SELECT a.severity, COUNT(*) AS total
            FROM alerts a
            INNER JOIN devices d ON d.id = a.device_id
            $whereSql
            GROUP BY a.severity
        ";
        $countsStmt = $this->db->prepare($countsSql);
        $countsStmt->execute($params);
        $countRows = $countsStmt->fetchAll();

        $severityCounts = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'critical' => 0,
        ];

        foreach ($countRows as $row) {
            $severityCounts[$row['severity']] = (int)($row['total'] ?? 0);
        }

        $alertsSql = "
            SELECT
                a.*,
                d.device_name,
                d.device_type,
                d.location,
                d.status AS device_status
            FROM alerts a
            INNER JOIN devices d ON d.id = a.device_id
            $whereSql
            ORDER BY a.created_at DESC, a.id DESC
            LIMIT 50
        ";
        $alertsStmt = $this->db->prepare($alertsSql);
        $alertsStmt->execute($params);
        $alerts = $alertsStmt->fetchAll();

        $frequencySql = "
            SELECT *
            FROM (
                SELECT
                    DATE_FORMAT(a.created_at, '%d %b') AS day_label,
                    DATE_FORMAT(a.created_at, '%Y-%m-%d') AS day_key,
                    COUNT(*) AS total_alerts
                FROM alerts a
                INNER JOIN devices d ON d.id = a.device_id
                $whereSql
                GROUP BY DATE_FORMAT(a.created_at, '%Y-%m-%d'), DATE_FORMAT(a.created_at, '%d %b')
                ORDER BY day_key DESC
                LIMIT 7
            ) freq_source
            ORDER BY day_key ASC
        ";
        $frequencyStmt = $this->db->prepare($frequencySql);
        $frequencyStmt->execute($params);
        $frequency = $frequencyStmt->fetchAll();

        $totalAlerts = array_sum($severityCounts);
        $historicalCount = $totalAlerts;
        $activeAlerts = $severityCounts['critical'] + $severityCounts['high'] + $severityCounts['medium'] + $severityCounts['low'];

        $previousDayCount = 0;
        $todayCount = 0;
        if (!empty($frequency)) {
            $todayCount = (int)($frequency[count($frequency) - 1]['total_alerts'] ?? 0);
            $previousDayCount = (int)($frequency[count($frequency) - 2]['total_alerts'] ?? 0);
        }

        $frequencyChange = $previousDayCount > 0
            ? (($todayCount - $previousDayCount) / $previousDayCount) * 100
            : 0.0;

        $impactedDevicesSql = "
            SELECT COUNT(DISTINCT a.device_id) AS total_devices
            FROM alerts a
            INNER JOIN devices d ON d.id = a.device_id
            $whereSql
        ";
        $impactedDevicesStmt = $this->db->prepare($impactedDevicesSql);
        $impactedDevicesStmt->execute($params);
        $impactedDevices = (int)(($impactedDevicesStmt->fetch()['total_devices'] ?? 0));

        $latestAlertAt = $alerts[0]['created_at'] ?? null;

        $this->view('alerts/index', [
            'title' => 'System Alerts Center',
            'user' => $user,
            'isAdmin' => $isAdmin,
            'alerts' => $alerts,
            'severityCounts' => $severityCounts,
            'frequency' => $frequency,
            'frequencyChange' => $frequencyChange,
            'historicalCount' => $historicalCount,
            'activeAlerts' => $activeAlerts,
            'impactedDevices' => $impactedDevices,
            'latestAlertAt' => $latestAlertAt,
        ]);
    }
}
