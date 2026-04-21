<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(base_path_url('/assets/css/style.css')) ?>">
</head>

<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
                <p class="page-subtitle">
                    <?= has_role('admin') ? 'ดูอุปกรณ์ทั้งหมดของระบบ' : 'ดูอุปกรณ์ของคุณ' ?>
                    | ผู้ใช้: <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
                </p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/dashboard')) ?>" class="button secondary">Dashboard</a>
                <a href="<?= htmlspecialchars(base_path_url('/devices/create')) ?>" class="button">เพิ่มอุปกรณ์</a>
                <a href="<?= htmlspecialchars(base_path_url('/logout')) ?>" class="button secondary">ออกจากระบบ</a>
            </div>
        </div>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่ออุปกรณ์</th>
                        <th>ประเภท</th>
                        <th>ตำแหน่ง</th>
                        <th>IP</th>
                        <?php if (has_role('admin')): ?>
                            <th>เจ้าของ</th>
                        <?php endif; ?>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($devices)): ?>
                        <tr>
                            <td colspan="<?= has_role('admin') ? 7 : 6 ?>">ยังไม่มีข้อมูลอุปกรณ์</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($devices as $device): ?>
                            <tr>
                                <td><?= (int)$device['id'] ?></td>
                                <td><?= htmlspecialchars($device['device_name']) ?></td>
                                <td><?= htmlspecialchars($device['device_type']) ?></td>
                                <td><?= htmlspecialchars($device['location'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($device['ip_address'] ?? '-') ?></td>
                                <?php if (has_role('admin')): ?>
                                    <td><?= htmlspecialchars($device['owner_name'] ?? '-') ?></td>
                                <?php endif; ?>
                                <td><span class="badge badge-<?= htmlspecialchars($device['status']) ?>"><?= htmlspecialchars($device['status']) ?></span></td>
                                <td>
                                    <a href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . $device['id'])) ?>" class="button ghost">ดูรายละเอียด</a>
                                    <a href="<?= htmlspecialchars(base_path_url('/devices/edit?id=' . $device['id'])) ?>" class="button ghost">แก้ไข</a>
                                    <?php if (has_role(['admin', 'staff'])): ?>
                                        <form method="POST" action="<?= htmlspecialchars(base_path_url('/devices/delete')) ?>" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= (int)$device['id'] ?>">
                                            <button type="submit" class="button secondary" onclick="return confirm('ยืนยันการลบอุปกรณ์นี้?')">ลบ</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
