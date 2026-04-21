<?php
$flashMap = [
    'à¸à¸£à¸¸à¸“à¸²à¸à¸£à¸­à¸à¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¹ƒà¸«à¹‰à¸„à¸£à¸šà¸–à¹‰à¸§à¸™' => 'กรุณากรอกข้อมูลให้ครบถ้วน',
    'à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹à¸¥à¸°à¸¢à¸·à¸™à¸¢à¸±à¸™à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹„à¸¡à¹ˆà¸•à¸£à¸‡à¸à¸±à¸™' => 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน',
    'à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¸•à¹‰à¸­à¸‡à¸¡à¸µà¸„à¸§à¸²à¸¡à¸¢à¸²à¸§à¸­à¸¢à¹ˆà¸²à¸‡à¸™à¹‰à¸­à¸¢ 6 à¸•à¸±à¸§à¸­à¸±à¸à¸©à¸£' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
];
$errorMessage = $_SESSION['error'] ?? null;
if ($errorMessage !== null && isset($flashMap[$errorMessage])) {
    $errorMessage = $flashMap[$errorMessage];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน</title>
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
                <p>RESET ACCESS • SECURITY UPDATE</p>
            </div>

            <section class="metricon-login-panel">
                <div class="metricon-login-head">
                    <h1>ตั้งรหัสผ่านใหม่</h1>
                    <p>กำหนดรหัสผ่านใหม่สำหรับบัญชีของคุณ</p>
                </div>

                <?php if ($errorMessage !== null): ?>
                    <div class="alert error"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars(base_path_url('/reset-password')) ?>" class="metricon-login-form">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="metricon-field">
                        <label for="password">รหัสผ่านใหม่</label>
                        <div class="metricon-input-shell">
                            <input id="password" type="password" name="password" required>
                        </div>
                    </div>

                    <div class="metricon-field">
                        <label for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
                        <div class="metricon-input-shell">
                            <input id="confirm_password" type="password" name="confirm_password" required>
                        </div>
                    </div>

                    <button type="submit" class="metricon-primary-btn">บันทึกรหัสผ่านใหม่</button>
                </form>
            </section>
        </main>

        <?php require __DIR__ . '/../partials/app_footer.php'; ?>
    </div>
</body>
</html>
