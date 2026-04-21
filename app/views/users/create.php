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
                <p class="page-subtitle">เพิ่มบัญชีผู้ใช้งานใหม่สำหรับระบบ</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/users')) ?>" class="button secondary">กลับหน้ารายการ</a>
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
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/users/store')) ?>">
                <div class="form-group">
                    <label>ชื่อ</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>อีเมล</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>รหัสผ่าน</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label>บทบาท</label>
                    <select name="role">
                        <option value="viewer" <?= (($old['role'] ?? '') === 'viewer') ? 'selected' : '' ?>>viewer</option>
                        <option value="staff" <?= (($old['role'] ?? '') === 'staff') ? 'selected' : '' ?>>staff</option>
                        <option value="admin" <?= (($old['role'] ?? '') === 'admin') ? 'selected' : '' ?>>admin</option>
                    </select>
                </div>
                <button type="submit" class="button">บันทึก</button>
            </form>
        </div>
    </div>
</body>
</html>
