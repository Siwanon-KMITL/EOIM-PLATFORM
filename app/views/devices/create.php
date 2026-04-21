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
                <p class="page-subtitle">เพิ่มสมาร์ทมิเตอร์ใหม่พร้อมข้อมูล IoT ที่จำเป็น</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/devices')) ?>" class="button secondary">กลับหน้ารายการ</a>
            </div>
        </div>

        <?php
            $old = $_SESSION['old'] ?? [];
            unset($_SESSION['old']);
        ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/devices/store')) ?>">
                <div class="form-group">
                    <label>ชื่ออุปกรณ์</label>
                    <input type="text" name="device_name" value="<?= htmlspecialchars($old['device_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>ประเภทอุปกรณ์</label>
                    <input type="text" name="device_type" value="<?= htmlspecialchars($old['device_type'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>ตำแหน่งติดตั้ง</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($old['location'] ?? '') ?>">
                </div>

                <?php if (has_role('admin')): ?>
                    <div class="form-group">
                        <label>เจ้าของอุปกรณ์</label>
                        <select name="user_id">
                            <?php foreach ($users as $owner): ?>
                                <option value="<?= (int)$owner['id'] ?>" <?= ((int)($old['user_id'] ?? 0) === (int)$owner['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($owner['name'] . ' (' . $owner['email'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>IP Address ของสมาร์ทมิเตอร์</label>
                    <input type="text" name="ip_address" value="<?= htmlspecialchars($old['ip_address'] ?? '') ?>" placeholder="192.168.1.100">
                </div>
                <div class="form-group">
                    <label>Device Secret (สำหรับ IoT token)</label>
                    <input type="text" name="device_secret" value="<?= htmlspecialchars($old['device_secret'] ?? '') ?>" placeholder="เว้นว่างเพื่อสร้างอัตโนมัติ">
                </div>
                <div class="form-group">
                    <label>สถานะ</label>
                    <select name="status">
                        <option value="active" <?= (($old['status'] ?? '') === 'active') ? 'selected' : '' ?>>active</option>
                        <option value="inactive" <?= (($old['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>inactive</option>
                        <option value="maintenance" <?= (($old['status'] ?? '') === 'maintenance') ? 'selected' : '' ?>>maintenance</option>
                    </select>
                </div>
                <button type="submit" class="button">บันทึก</button>
            </form>
        </div>
    </div>
</body>
</html>
