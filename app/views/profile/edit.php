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
                <p class="page-subtitle">ปรับปรุงข้อมูลบัญชีของคุณได้ที่นี่</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/dashboard')) ?>" class="button secondary">กลับหน้าหลัก</a>
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
            <form method="POST" action="<?= htmlspecialchars(base_path_url('/profile/update')) ?>">
                <div class="form-group">
                    <label>ชื่อ</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>อีเมล</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
                </div>
                <button type="submit" class="button">บันทึก</button>
            </form>
            <div style="margin-top: 16px;">
                <a href="<?= htmlspecialchars(base_path_url('/profile/password')) ?>" class="button ghost">เปลี่ยนรหัสผ่าน</a>
            </div>
        </div>
    </div>
</body>
</html>
