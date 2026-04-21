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
                <p class="page-subtitle">แก้ไขข้อมูลผู้ใช้และรีเซ็ตรหัสผ่านได้ที่นี่</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/users')) ?>" class="button secondary">กลับหน้ารายการ</a>
            </div>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/users/update')) ?>">
                <input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>">
                <div class="form-group">
                    <label>ชื่อ</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($editUser['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>อีเมล</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>บทบาท</label>
                    <select name="role">
                        <option value="viewer" <?= $editUser['role'] === 'viewer' ? 'selected' : '' ?>>viewer</option>
                        <option value="staff" <?= $editUser['role'] === 'staff' ? 'selected' : '' ?>>staff</option>
                        <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>รหัสผ่านใหม่ (เว้นว่างหากไม่เปลี่ยน)</label>
                    <input type="password" name="password">
                </div>
                <div class="form-group">
                    <label>ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_password">
                </div>
                <button type="submit" class="button">อัปเดต</button>
            </form>
        </div>
    </div>
</body>
</html>
