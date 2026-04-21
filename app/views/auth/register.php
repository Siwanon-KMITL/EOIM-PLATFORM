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
                <p class="page-subtitle">สมัครสมาชิกเพื่อเชื่อมต่อสมาร์ทมิเตอร์และใช้บริการ</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/login')) ?>" class="button secondary">กลับเข้าสู่ระบบ</a>
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
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/register')) ?>">
                <div class="form-group">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>อีเมล</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>รหัสผ่าน</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="button">สมัครสมาชิก</button>
            </form>
        </div>
    </div>
</body>
</html>
