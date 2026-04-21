<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AlertRepository;
use App\Repositories\DeviceRepository;
use App\Repositories\SmartMeterRepository;

class MonitoringController extends Controller
{
    public function deviceDetail(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            exit('Invalid device id');
        }

        $deviceRepo = new DeviceRepository();
        $smartRepo = new SmartMeterRepository();
        $alertRepo = new AlertRepository();

        $user = auth_user();
        $device = has_role('admin') ? $deviceRepo->find($id) : $deviceRepo->findForUser($id, $user['id']);

        if (!$device) {
            http_response_code(404);
            exit('Device not found');
        }

        $latestReading = $smartRepo->latestByDeviceId($id);
        $summary = $smartRepo->summaryByDevice($id);
        $summaryToday = $smartRepo->summaryTodayByDevice($id);
        $readings = $smartRepo->readingsByDevice($id, 50);
        $alerts = $alertRepo->latestByDevice($id, 20);
        $alertCount = $alertRepo->countByDevice($id);

        $this->view('monitoring/device_detail', [
            'title' => 'รายละเอียดอุปกรณ์',
            'user' => auth_user(),
            'device' => $device,
            'latestReading' => $latestReading,
            'summary' => $summary,
            'summaryToday' => $summaryToday,
            'readings' => $readings,
            'alerts' => $alerts,
            'alertCount' => $alertCount
        ]);
    }
}
