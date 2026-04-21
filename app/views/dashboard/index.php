<?php
$isAdmin = has_role('admin');
$displayName = trim((string)($user['name'] ?? 'ผู้ใช้งาน'));
$roleLabel = $isAdmin ? 'ผู้ดูแลระบบ' : 'ผู้ใช้งาน';
$onlineDevices = (int)($deviceStatus['active'] ?? 0);
$inactiveDevices = (int)($deviceStatus['inactive'] ?? 0);
$maintenanceDevices = (int)($deviceStatus['maintenance'] ?? 0);
$criticalAlerts = (int)($alertSeverity['critical'] ?? 0);
$highAlerts = (int)($alertSeverity['high'] ?? 0);
$latestUpdate = $latestReadings[0]['recorded_at'] ?? null;
$statusLabels = [
    'active' => 'ออนไลน์',
    'inactive' => 'ออฟไลน์',
    'maintenance' => 'บำรุงรักษา',
];
$severityLabels = [
    'low' => 'ต่ำ',
    'medium' => 'ปานกลาง',
    'high' => 'สูง',
    'critical' => 'วิกฤต',
];
$deviceHealthPercent = $totalDevices > 0 ? (int)round(($onlineDevices / $totalDevices) * 100) : 0;
$maxTopPower = 0.0;

foreach ($topDevices as $row) {
    $maxTopPower = max($maxTopPower, (float)($row['total_power'] ?? 0));
}

$formatNumber = static fn ($value, int $decimals = 0): string => number_format((float)$value, $decimals);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(base_path_url('/assets/css/style.css')) ?>">
</head>
<body class="dashboard-body">
    <div class="dashboard-shell">
        <section class="dashboard-hero">
            <div class="dashboard-hero-copy">
                <span class="dashboard-eyebrow"><?= $isAdmin ? 'SYSTEM OVERVIEW' : 'MY ENERGY OVERVIEW' ?></span>
                <h1><?= $isAdmin ? 'ภาพรวมระบบ EOIM' : 'แดชบอร์ดพลังงานของคุณ' ?></h1>
                <p>
                    <?= $isAdmin
                        ? 'ติดตามสถานะอุปกรณ์ ผู้ใช้งาน การแจ้งเตือน และพลังงานรวมของทั้งระบบในมุมมองเดียว'
                        : 'ดูการใช้พลังงาน สถานะอุปกรณ์ และการแจ้งเตือนล่าสุดของอุปกรณ์ที่อยู่ในความรับผิดชอบของคุณ'; ?>
                </p>
                <div class="dashboard-meta">
                    <span><?= htmlspecialchars($displayName) ?></span>
                    <span><?= $roleLabel ?></span>
                    <span><?= $latestUpdate ? 'อัปเดตล่าสุด ' . htmlspecialchars($latestUpdate) : 'ยังไม่มีข้อมูลการวัดล่าสุด' ?></span>
                </div>
            </div>
            <div class="dashboard-hero-side">
                <div class="dashboard-hero-panel">
                    <div class="dashboard-hero-panel-label">สถานะระบบโดยรวม</div>
                    <div class="dashboard-hero-panel-value"><?= $deviceHealthPercent ?>%</div>
                    <p>อุปกรณ์ออนไลน์ <?= $onlineDevices ?> จากทั้งหมด <?= (int)$totalDevices ?> เครื่อง</p>
                    <div class="dashboard-progress-track" aria-hidden="true">
                        <div class="dashboard-progress-fill" style="width: <?= max(0, min(100, $deviceHealthPercent)) ?>%"></div>
                    </div>
                </div>
                <div class="dashboard-action-group">
                    <a href="<?= htmlspecialchars(base_path_url('/devices')) ?>" class="button">อุปกรณ์<?= $isAdmin ? 'ทั้งหมด' : 'ของฉัน' ?></a>
                    <?php if ($isAdmin): ?>
                        <a href="<?= htmlspecialchars(base_path_url('/users')) ?>" class="button ghost">จัดการผู้ใช้</a>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars(base_path_url('/profile')) ?>" class="button ghost">โปรไฟล์</a>
                    <a href="<?= htmlspecialchars(base_path_url('/logout')) ?>" class="button secondary">ออกจากระบบ</a>
                </div>
            </div>
        </section>

        <section class="dashboard-kpi-grid">
            <article class="dashboard-kpi-card dashboard-kpi-card-primary">
                <span class="dashboard-kpi-label"><?= $isAdmin ? 'ผู้ใช้งานทั้งหมด' : 'อุปกรณ์ทั้งหมด' ?></span>
                <strong class="dashboard-kpi-value"><?= $isAdmin ? (int)$totalUsers : (int)$totalDevices ?></strong>
                <p class="dashboard-kpi-footnote"><?= $isAdmin ? 'บัญชีผู้ใช้งานในระบบ' : 'จำนวนอุปกรณ์ที่คุณเข้าถึงได้' ?></p>
            </article>
            <article class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">กำลังไฟรวมวันนี้</span>
                <strong class="dashboard-kpi-value"><?= $formatNumber($totalPowerToday, 2) ?></strong>
                <p class="dashboard-kpi-footnote">หน่วยวัตต์รวมของ readings วันนี้</p>
            </article>
            <article class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">พลังงานรวมวันนี้</span>
                <strong class="dashboard-kpi-value"><?= $formatNumber($totalEnergyToday, 3) ?></strong>
                <p class="dashboard-kpi-footnote">พลังงานสะสมจากอุปกรณ์ทั้งหมดวันนี้</p>
            </article>
            <article class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">จำนวน readings วันนี้</span>
                <strong class="dashboard-kpi-value"><?= (int)$totalReadingsToday ?></strong>
                <p class="dashboard-kpi-footnote">ข้อมูลวัดล่าสุดที่ระบบรับเข้ามา</p>
            </article>
            <article class="dashboard-kpi-card">
                <span class="dashboard-kpi-label">การแจ้งเตือนทั้งหมด</span>
                <strong class="dashboard-kpi-value"><?= (int)$totalAlerts ?></strong>
                <p class="dashboard-kpi-footnote">สูง <?= $highAlerts ?> รายการ, วิกฤต <?= $criticalAlerts ?> รายการ</p>
            </article>
        </section>

        <section class="dashboard-main-grid">
            <div class="dashboard-column-main">
                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Daily Focus</span>
                            <h2>อุปกรณ์ที่ใช้พลังงานสูงสุดวันนี้</h2>
                        </div>
                        <a href="<?= htmlspecialchars(base_path_url('/devices')) ?>" class="dashboard-inline-link">ดูรายการอุปกรณ์</a>
                    </div>

                    <?php if (empty($topDevices)): ?>
                        <div class="dashboard-empty-state">วันนี้ยังไม่มีข้อมูลพลังงานของอุปกรณ์</div>
                    <?php else: ?>
                        <div class="dashboard-rank-list">
                            <?php foreach ($topDevices as $row): ?>
                                <?php
                                $power = (float)($row['total_power'] ?? 0);
                                $barWidth = $maxTopPower > 0 ? (int)round(($power / $maxTopPower) * 100) : 0;
                                ?>
                                <div class="dashboard-rank-item">
                                    <div class="dashboard-rank-head">
                                        <div>
                                            <strong><?= htmlspecialchars($row['device_name']) ?></strong>
                                            <span><?= htmlspecialchars($row['device_type']) ?><?= !empty($row['location']) ? ' • ' . htmlspecialchars($row['location']) : '' ?></span>
                                        </div>
                                        <div class="dashboard-rank-metric">
                                            <strong><?= $formatNumber($power, 2) ?> W</strong>
                                            <span><?= $formatNumber($row['total_energy'] ?? 0, 3) ?> kWh</span>
                                        </div>
                                    </div>
                                    <div class="dashboard-rank-track" aria-hidden="true">
                                        <div class="dashboard-rank-fill" style="width: <?= $power > 0 ? max(8, min(100, $barWidth)) : 0 ?>%"></div>
                                    </div>
                                    <div class="dashboard-rank-foot">
                                        <span>อัปเดตล่าสุด <?= htmlspecialchars($row['latest_time'] ?? '-') ?></span>
                                        <a href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . (int)$row['id'])) ?>">ดูรายละเอียด</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>

                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Latest Data</span>
                            <h2>ข้อมูลวัดล่าสุดของแต่ละอุปกรณ์</h2>
                        </div>
                    </div>
                    <div class="table-wrapper dashboard-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>อุปกรณ์</th>
                                    <th>ตำแหน่ง</th>
                                    <th>Voltage</th>
                                    <th>Current</th>
                                    <th>Power</th>
                                    <th>Energy</th>
                                    <th>Power Factor</th>
                                    <th>เวลา</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latestPerDevice)): ?>
                                    <tr>
                                        <td colspan="8">ยังไม่มีข้อมูลการวัดของอุปกรณ์</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($latestPerDevice as $row): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($row['device_name']) ?></strong>
                                                <div class="dashboard-cell-subtext"><?= htmlspecialchars($row['device_type']) ?></div>
                                            </td>
                                            <td><?= htmlspecialchars($row['location'] ?? '-') ?></td>
                                            <td><?= $formatNumber($row['voltage'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['current'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['power'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['energy'] ?? 0, 3) ?></td>
                                            <td><?= $formatNumber($row['power_factor'] ?? 0, 2) ?></td>
                                            <td><?= htmlspecialchars($row['recorded_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Recent Stream</span>
                            <h2>10 readings ล่าสุด</h2>
                        </div>
                    </div>
                    <div class="table-wrapper dashboard-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>เวลา</th>
                                    <th>อุปกรณ์</th>
                                    <th>Voltage</th>
                                    <th>Current</th>
                                    <th>Power</th>
                                    <th>Energy</th>
                                    <th>Frequency</th>
                                    <th>PF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latestReadings)): ?>
                                    <tr>
                                        <td colspan="8">ยังไม่มี readings ล่าสุด</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($latestReadings as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['recorded_at']) ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['device_name']) ?></strong>
                                                <div class="dashboard-cell-subtext"><?= htmlspecialchars($row['device_type']) ?></div>
                                            </td>
                                            <td><?= $formatNumber($row['voltage'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['current'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['power'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['energy'] ?? 0, 3) ?></td>
                                            <td><?= $formatNumber($row['frequency'] ?? 0, 2) ?></td>
                                            <td><?= $formatNumber($row['power_factor'] ?? 0, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <aside class="dashboard-column-side">
                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Device Health</span>
                            <h2>สถานะอุปกรณ์</h2>
                        </div>
                    </div>
                    <div class="dashboard-stat-stack">
                        <div class="dashboard-stat-row">
                            <div>
                                <strong>ออนไลน์</strong>
                                <span><?= $statusLabels['active'] ?></span>
                            </div>
                            <span class="badge badge-active"><?= $onlineDevices ?></span>
                        </div>
                        <div class="dashboard-stat-row">
                            <div>
                                <strong>ออฟไลน์</strong>
                                <span><?= $statusLabels['inactive'] ?></span>
                            </div>
                            <span class="badge badge-inactive"><?= $inactiveDevices ?></span>
                        </div>
                        <div class="dashboard-stat-row">
                            <div>
                                <strong>บำรุงรักษา</strong>
                                <span><?= $statusLabels['maintenance'] ?></span>
                            </div>
                            <span class="badge badge-maintenance"><?= $maintenanceDevices ?></span>
                        </div>
                    </div>
                </article>

                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Alert Watch</span>
                            <h2>สรุประดับการแจ้งเตือน</h2>
                        </div>
                    </div>
                    <div class="dashboard-severity-list">
                        <?php foreach ($alertSeverity as $severity => $count): ?>
                            <div class="dashboard-severity-item">
                                <div>
                                    <strong><?= htmlspecialchars($severityLabels[$severity] ?? ucfirst($severity)) ?></strong>
                                    <span><?= htmlspecialchars($severity) ?></span>
                                </div>
                                <span class="badge badge-<?= htmlspecialchars($severity) ?>"><?= (int)$count ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Assigned Devices</span>
                            <h2><?= $isAdmin ? 'ภาพรวมอุปกรณ์ล่าสุด' : 'อุปกรณ์ของคุณ' ?></h2>
                        </div>
                    </div>
                    <?php if (empty($devices)): ?>
                        <div class="dashboard-empty-state">ยังไม่มีอุปกรณ์ในระบบ</div>
                    <?php else: ?>
                        <div class="dashboard-device-list">
                            <?php foreach (array_slice($devices, 0, 6) as $device): ?>
                                <div class="dashboard-device-card">
                                    <div class="dashboard-device-card-head">
                                        <div>
                                            <strong><?= htmlspecialchars($device['device_name']) ?></strong>
                                            <span><?= htmlspecialchars($device['device_type']) ?></span>
                                        </div>
                                        <span class="badge badge-<?= htmlspecialchars($device['status']) ?>"><?= htmlspecialchars($statusLabels[$device['status']] ?? $device['status']) ?></span>
                                    </div>
                                    <p><?= htmlspecialchars($device['location'] ?? 'ไม่ระบุตำแหน่ง') ?></p>
                                    <a href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . (int)$device['id'])) ?>">เปิดหน้าติดตามอุปกรณ์</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>

                <article class="dashboard-panel">
                    <div class="dashboard-panel-head">
                        <div>
                            <span class="dashboard-panel-kicker">Latest Alerts</span>
                            <h2>การแจ้งเตือนล่าสุด</h2>
                        </div>
                    </div>
                    <?php if (empty($alerts)): ?>
                        <div class="dashboard-empty-state">ยังไม่มีการแจ้งเตือนล่าสุด</div>
                    <?php else: ?>
                        <div class="dashboard-alert-list">
                            <?php foreach ($alerts as $alert): ?>
                                <div class="dashboard-alert-item">
                                    <div class="dashboard-alert-head">
                                        <span class="badge badge-<?= htmlspecialchars($alert['severity']) ?>"><?= htmlspecialchars($severityLabels[$alert['severity']] ?? $alert['severity']) ?></span>
                                        <span><?= htmlspecialchars($alert['created_at']) ?></span>
                                    </div>
                                    <strong><?= htmlspecialchars($alert['alert_type']) ?></strong>
                                    <p><?= htmlspecialchars($alert['message']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            </aside>
        </section>
    </div>
</body>
</html>
