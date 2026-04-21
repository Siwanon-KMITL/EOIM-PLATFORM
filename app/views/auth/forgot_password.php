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
                <p class="page-subtitle">กรอกอีเมลของคุณเพื่อรับลิงก์รีเซ็ตรหัสผ่าน</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/login')) ?>" class="button secondary">กลับเข้าสู่ระบบ</a>
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
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/forgot-password')) ?>">
                <div class="form-group">
                    <label>อีเมล</label>
                    <input type="email" name="email" required>
                </div>
                <button type="submit" class="button">ส่งลิงก์รีเซ็ตรหัสผ่าน</button>
            </form>
        </div>
    </div>
</body>
</html>
