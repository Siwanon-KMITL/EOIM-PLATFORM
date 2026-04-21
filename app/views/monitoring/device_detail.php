<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(base_path_url('/assets/css/style.css')) ?>">
</head>

<body>
    <div class="topbar">
        <div>
            <h1 class="page-title"><?= htmlspecialchars($title) ?>: <?= htmlspecialchars($device['device_name']) ?></h1>
            <p class="page-subtitle">ผู้ใช้: <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)</p>
        </div>
        <div class="main-actions">
            <a href="<?= htmlspecialchars(base_path_url('/dashboard')) ?>" class="button secondary">Dashboard</a>
            <a href="<?= htmlspecialchars(base_path_url('/devices')) ?>" class="button ghost">รายการอุปกรณ์</a>
            <a href="<?= htmlspecialchars(base_path_url('/logout')) ?>" class="button secondary">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <div class="grid-2">
            <div class="card">
                <h2>ข้อมูลอุปกรณ์</h2>
                <p><strong>ID:</strong> <?= (int)$device['id'] ?></p>
                <p><strong>ชื่ออุปกรณ์:</strong> <?= htmlspecialchars($device['device_name']) ?></p>
                <p><strong>ประเภท:</strong> <?= htmlspecialchars($device['device_type']) ?></p>
                <p><strong>ตำแหน่ง:</strong> <?= htmlspecialchars($device['location'] ?? '-') ?></p>
                <p><strong>IP Address:</strong> <?= htmlspecialchars($device['ip_address'] ?? '-') ?></p>
                <p><strong>เจ้าของ:</strong> <?= htmlspecialchars($device['owner_name'] ?? '-') ?></p>
                <p><strong>สถานะ:</strong> <span class="badge badge-<?= htmlspecialchars($device['status']) ?>"><?= htmlspecialchars($device['status']) ?></span></p>
                <p><strong>สร้างเมื่อ:</strong> <?= htmlspecialchars($device['created_at']) ?></p>
                <p><strong>แก้ไขล่าสุด:</strong> <?= htmlspecialchars($device['updated_at']) ?></p>
            </div>
            <div class="card">
                <h2>Reading ล่าสุด</h2>
                <?php if (!$latestReading): ?>
                    <p>ยังไม่มีข้อมูลการวัดของอุปกรณ์นี้</p>
                <?php else: ?>
                    <p><strong>Voltage:</strong> <?= number_format((float)$latestReading['voltage'], 2) ?></p>
                    <p><strong>Current:</strong> <?= number_format((float)$latestReading['current'], 2) ?></p>
                    <p><strong>Power:</strong> <?= number_format((float)$latestReading['power'], 2) ?> W</p>
                    <p><strong>Energy:</strong> <?= number_format((float)$latestReading['energy'], 3) ?></p>
                    <p><strong>Frequency:</strong> <?= number_format((float)$latestReading['frequency'], 2) ?> Hz</p>
                    <p><strong>Power Factor:</strong> <?= number_format((float)$latestReading['power_factor'], 2) ?></p>
                    <p><strong>เวลา:</strong> <?= htmlspecialchars($latestReading['recorded_at']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h3>จำนวน Readings ทั้งหมด</h3>
                <p class="metric"><?= (int)$summary['total_readings'] ?></p>
            </div>
            <div class="card">
                <h3>จำนวน Alerts ทั้งหมด</h3>
                <p class="metric"><?= (int)$alertCount ?></p>
            </div>
            <div class="card">
                <h3>Power รวมทั้งหมด</h3>
                <p class="metric"><?= number_format((float)$summary['total_power'], 2) ?></p>
                <div class="muted">หน่วย: W</div>
            </div>
            <div class="card">
                <h3>Energy รวมทั้งหมด</h3>
                <p class="metric"><?= number_format((float)$summary['total_energy'], 3) ?></p>
            </div>
        </div>
    </div>
</body>
</html>
