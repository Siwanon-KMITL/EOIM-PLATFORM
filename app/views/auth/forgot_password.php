<?php
$flashMap = [
    'à¸à¸£à¸¸à¸“à¸²à¸à¸£à¸­à¸à¸­à¸µà¹€à¸¡à¸¥à¹ƒà¸«à¹‰à¸–à¸¹à¸à¸•à¹‰à¸­à¸‡' => 'กรุณากรอกอีเมลให้ถูกต้อง',
    'à¸«à¸²à¸à¸­à¸µà¹€à¸¡à¸¥à¸­à¸¢à¸¹à¹ˆà¹ƒà¸™à¸£à¸°à¸šà¸š à¹€à¸£à¸²à¹„à¸”à¹‰à¸ªà¹ˆà¸‡à¸¥à¸´à¸‡à¸à¹Œà¸£à¸µà¹€à¸‹à¹‡à¸•à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹ƒà¸«à¹‰à¹à¸¥à¹‰à¸§' => 'หากอีเมลอยู่ในระบบ เราได้ส่งลิงก์รีเซ็ตรหัสผ่านให้แล้ว',
];
$successMessage = $_SESSION['success'] ?? null;
$errorMessage = $_SESSION['error'] ?? null;
if ($successMessage !== null && isset($flashMap[$successMessage])) {
    $successMessage = $flashMap[$successMessage];
}
if ($errorMessage !== null && isset($flashMap[$errorMessage])) {
    $errorMessage = $flashMap[$errorMessage];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(base_path_url('/assets/css/style.css')) ?>">
</head>
<body class="metricon-login-body">
    <div class="metricon-login-page">
        <div class="metricon-login-bg" aria-hidden="true">
            <div class="metricon-login-pattern"></div>
            <div class="metricon-login-orb metricon-login-orb-primary"></div>
            <div class="metricon-login-orb metricon-login-orb-secondary"></div>
        </div>

        <main class="metricon-login-wrap">
            <div class="metricon-login-brand">
                <h1>SMART METER</h1>
                <p>RECOVERY PROTOCOL • PASSWORD RESET</p>
            </div>

            <section class="metricon-login-panel">
                <div class="metricon-login-head">
                    <h1>ลืมรหัสผ่าน</h1>
                    <p>กรอกอีเมลของคุณเพื่อรับลิงก์สำหรับตั้งรหัสผ่านใหม่</p>
                </div>

                <?php if ($successMessage !== null): ?>
                    <div class="alert success"><?= htmlspecialchars($successMessage) ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if ($errorMessage !== null): ?>
                    <div class="alert error"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars(base_path_url('/forgot-password')) ?>" class="metricon-login-form">
                    <div class="metricon-field">
                        <label for="email">Email Address</label>
                        <div class="metricon-input-shell">
                            <input id="email" type="email" name="email" required>
                        </div>
                    </div>

                    <button type="submit" class="metricon-primary-btn">ส่งลิงก์รีเซ็ต</button>
                </form>

                <div class="metricon-protocol-divider">
                    <span>กลับสู่ระบบ</span>
                </div>

                <div class="metricon-protocol-grid">
                    <a href="<?= htmlspecialchars(base_path_url('/login')) ?>" class="metricon-protocol-card">
                        <span>เข้าสู่ระบบ</span>
                    </a>
                </div>
            </section>
        </main>

        <?php require __DIR__ . '/../partials/app_footer.php'; ?>
    </div>
</body>
</html>
