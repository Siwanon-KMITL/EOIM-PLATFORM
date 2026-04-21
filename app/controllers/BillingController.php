<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class BillingController extends Controller
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

        $baseRate = 4.50;
        $fuelRate = 0.35;
        $peakRate = 6.80;
        $offPeakRate = 3.20;

        $whereSql = '';
        $params = [];

        if (!$isAdmin && $userId !== null) {
            $whereSql = 'WHERE d.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $monthlySql = "
            SELECT 
                DATE_FORMAT(r.recorded_at, '%Y-%m') AS billing_key,
                DATE_FORMAT(r.recorded_at, '%b %Y') AS billing_label,
                MIN(DATE(r.recorded_at)) AS period_start,
                MAX(DATE(r.recorded_at)) AS period_end,
                COUNT(*) AS total_readings,
                COALESCE(SUM(r.energy), 0) AS total_energy,
                COALESCE(SUM(r.power), 0) AS total_power
            FROM smartmeter_readings r
            INNER JOIN devices d ON d.id = r.device_id
            $whereSql
            GROUP BY DATE_FORMAT(r.recorded_at, '%Y-%m'), DATE_FORMAT(r.recorded_at, '%b %Y')
            ORDER BY billing_key DESC
            LIMIT 12
        ";

        $stmt = $this->db->prepare($monthlySql);
        $stmt->execute($params);
        $monthlyHistory = $stmt->fetchAll();

        $currentPeriod = $monthlyHistory[0] ?? null;
        $lastPeriod = $monthlyHistory[1] ?? null;

        $history = [];
        $trend = [];

        foreach ($monthlyHistory as $index => $row) {
            $energy = (float)($row['total_energy'] ?? 0);
            $amount = $energy * ($baseRate + $fuelRate);
            $rebate = $energy >= 250 ? min(2400.0, $energy * 1.15) : 0.0;
            $netAmount = max(0, $amount - $rebate);
            $isCurrent = $index === 0;

            $history[] = [
                'invoice_id' => 'INV-' . str_replace('-', '', (string)$row['billing_key']),
                'billing_label' => $row['billing_label'],
                'period_start' => $row['period_start'],
                'period_end' => $row['period_end'],
                'consumption_mwh' => $energy / 1000,
                'consumption_kwh' => $energy,
                'gross_amount' => $amount,
                'rebate' => $rebate,
                'net_amount' => $netAmount,
                'status' => $isCurrent ? 'UNBILLED' : 'PAID',
                'readings' => (int)($row['total_readings'] ?? 0),
            ];

            $trend[] = [
                'label' => strtoupper(date('M', strtotime((string)$row['period_start']))),
                'amount' => $netAmount,
                'forecast' => $index <= 1 ? null : $netAmount * 1.06,
            ];
        }

        $currentUnbilled = $history[0] ?? null;
        $lastBilled = $history[1] ?? null;
        $currentAmount = (float)($currentUnbilled['net_amount'] ?? 0);
        $lastAmount = (float)($lastBilled['net_amount'] ?? 0);
        $periodChange = $lastAmount > 0 ? (($currentAmount - $lastAmount) / $lastAmount) * 100 : 0;
        $rebateAmount = (float)($currentUnbilled['rebate'] ?? 0);
        $maxTrendAmount = 0.0;

        foreach ($trend as $point) {
            $maxTrendAmount = max($maxTrendAmount, (float)($point['amount'] ?? 0), (float)($point['forecast'] ?? 0));
        }

        $consumptionSql = "
            SELECT 
                SUM(CASE WHEN HOUR(r.recorded_at) BETWEEN 9 AND 17 THEN r.energy ELSE 0 END) AS peak_energy,
                SUM(CASE WHEN HOUR(r.recorded_at) < 9 OR HOUR(r.recorded_at) > 17 THEN r.energy ELSE 0 END) AS offpeak_energy
            FROM smartmeter_readings r
            INNER JOIN devices d ON d.id = r.device_id
            $whereSql
        ";

        $consumptionStmt = $this->db->prepare($consumptionSql);
        $consumptionStmt->execute($params);
        $consumptionMix = $consumptionStmt->fetch() ?: ['peak_energy' => 0, 'offpeak_energy' => 0];

        $this->view('billing/index', [
            'title' => 'Billing & Tariffs',
            'user' => $user,
            'isAdmin' => $isAdmin,
            'baseRate' => $baseRate,
            'fuelRate' => $fuelRate,
            'peakRate' => $peakRate,
            'offPeakRate' => $offPeakRate,
            'currentUnbilled' => $currentUnbilled,
            'lastBilled' => $lastBilled,
            'rebateAmount' => $rebateAmount,
            'periodChange' => $periodChange,
            'trend' => array_reverse($trend),
            'maxTrendAmount' => $maxTrendAmount,
            'history' => $history,
            'consumptionMix' => $consumptionMix,
        ]);
    }
}
