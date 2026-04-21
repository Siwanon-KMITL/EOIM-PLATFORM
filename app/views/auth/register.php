<?php
$old = $_SESSION['old'] ?? [];
$flashMap = [
    'à¸à¸£à¸¸à¸“à¸²à¸à¸£à¸­à¸à¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¹ƒà¸«à¹‰à¸„à¸£à¸šà¸–à¹‰à¸§à¸™' => 'กรุณากรอกข้อมูลให้ครบถ้วน',
    'à¸£à¸¹à¸›à¹à¸šà¸šà¸­à¸µà¹€à¸¡à¸¥à¹„à¸¡à¹ˆà¸–à¸¹à¸à¸•à¹‰à¸­à¸‡' => 'รูปแบบอีเมลไม่ถูกต้อง',
    'à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹à¸¥à¸°à¸¢à¸·à¸™à¸¢à¸±à¸™à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹„à¸¡à¹ˆà¸•à¸£à¸‡à¸à¸±à¸™' => 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน',
    'à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¸•à¹‰à¸­à¸‡à¸¡à¸µà¸„à¸§à¸²à¸¡à¸¢à¸²à¸§à¸­à¸¢à¹ˆà¸²à¸‡à¸™à¹‰à¸­à¸¢ 6 à¸•à¸±à¸§à¸­à¸±à¸à¸©à¸£' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
    'à¸­à¸µà¹€à¸¡à¸¥à¸™à¸µà¹‰à¸–à¸¹à¸à¹ƒà¸Šà¹‰à¸‡à¸²à¸™à¹à¸¥à¹‰à¸§' => 'อีเมลนี้ถูกใช้งานแล้ว',
];
$errorMessage = $_SESSION['error'] ?? null;
if ($errorMessage !== null && isset($flashMap[$errorMessage])) {
    $errorMessage = $flashMap[$errorMessage];
}
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
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
                <p>CREATE ACCOUNT • ACCESS CONTROL</p>
            </div>

            <section class="metricon-login-panel">
                <div class="metricon-login-head">
                    <h1>ลงทะเบียนผู้ใช้งาน</h1>
                    <p>สร้างบัญชีเพื่อเชื่อมต่อระบบและเริ่มใช้งาน Smart Meter Platform</p>
                </div>

                <?php if ($errorMessage !== null): ?>
                    <div class="alert error"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars(base_path_url('/register')) ?>" class="metricon-login-form">
                    <div class="metricon-field">
                        <label for="name">ชื่อ - นามสกุล</label>
                        <div class="metricon-input-shell">
                            <input id="name" type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="metricon-field">
                        <label for="email">Email Address</label>
                        <div class="metricon-input-shell">
                            <input id="email" type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="metricon-field">
                        <label for="password">รหัสผ่าน</label>
                        <div class="metricon-input-shell">
                            <input id="password" type="password" name="password" required>
                        </div>
                    </div>

                    <div class="metricon-field">
                        <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                        <div class="metricon-input-shell">
                            <input id="confirm_password" type="password" name="confirm_password" required>
                        </div>
                    </div>

                    <button type="submit" class="metricon-primary-btn">สร้างบัญชี</button>
                </form>

                <div class="metricon-protocol-divider">
                    <span>มีบัญชีอยู่แล้ว</span>
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
