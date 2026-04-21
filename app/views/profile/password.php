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
                <p class="page-subtitle">เปลี่ยนรหัสผ่านใหม่สำหรับบัญชีของคุณ</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/profile')) ?>" class="button secondary">กลับโปรไฟล์</a>
            </div>
        </div>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/profile/password')) ?>">
                <div class="form-group">
                    <label>รหัสผ่านปัจจุบัน</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>รหัสผ่านใหม่</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="button">บันทึก</button>
            </form>
        </div>
    </div>
</body>
</html>
