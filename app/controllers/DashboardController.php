<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\DeviceRepository;
use App\Repositories\SmartMeterRepository;
use App\Repositories\AlertRepository;
use App\Repositories\UserRepository;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = auth_user();
        $userId = $user['id'] ?? null;
        $isAdmin = has_role('admin');

        $deviceRepo = new DeviceRepository();
        $smartRepo = new SmartMeterRepository();
        $alertRepo = new AlertRepository();

        $devices = $isAdmin ? $deviceRepo->all() : $deviceRepo->all($userId);
        $deviceStatusRaw = $deviceRepo->countByStatus($isAdmin ? null : $userId);
        $alertSeverityRaw = $isAdmin ? $alertRepo->countBySeverity() : $alertRepo->countBySeverityByUser($userId);

        $deviceStatus = [
            'active' => 0,
            'inactive' => 0,
            'maintenance' => 0
        ];

        foreach ($deviceStatusRaw as $row) {
            $deviceStatus[$row['status']] = (int)$row['total'];
        }

        $alertSeverity = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'critical' => 0
        ];

        foreach ($alertSeverityRaw as $row) {
            $alertSeverity[$row['severity']] = (int)$row['total'];
        }

        $userRepository = new UserRepository();

        $this->view('dashboard/index', [
            'title' => 'EOIM Dashboard',
            'user' => $user,
            'devices' => $devices,
            'totalDevices' => $deviceRepo->totalCount($isAdmin ? null : $userId),
            'totalUsers' => $isAdmin ? $userRepository->totalCount() : null,
            'totalAlerts' => $alertRepo->totalCount($isAdmin ? null : $userId),
            'totalPowerToday' => $isAdmin ? $smartRepo->totalPowerToday() : $smartRepo->totalPowerTodayByUser($userId),
            'totalEnergyToday' => $isAdmin ? $smartRepo->totalEnergyToday() : $smartRepo->totalEnergyTodayByUser($userId),
            'totalReadingsToday' => $isAdmin ? $smartRepo->totalReadingsToday() : $smartRepo->totalReadingsTodayByUser($userId),
            'deviceStatus' => $deviceStatus,
            'alertSeverity' => $alertSeverity,
            'latestReadings' => $isAdmin ? $smartRepo->latestReadingsWithDevice(10) : $smartRepo->latestReadingsWithDeviceByUser(10, $userId),
            'latestPerDevice' => $isAdmin ? $smartRepo->latestReadingPerDevice() : $smartRepo->latestReadingPerDeviceByUser($userId),
            'topDevices' => $isAdmin ? $smartRepo->topDevicesByPowerToday(5) : $smartRepo->topDevicesByPowerTodayByUser(5, $userId),
            'alerts' => $isAdmin ? $alertRepo->latest(10) : $alertRepo->latestByUser(10, $userId)
        ]);
    }
}