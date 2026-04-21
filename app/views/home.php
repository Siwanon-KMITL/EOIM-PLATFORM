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
                <p class="page-subtitle">ระบบ EOIM สำหรับบริหารจัดการสมาร์ทมิเตอร์และการใช้งานพลังงาน</p>
            </div>
        </div>

        <div class="card">
            <p><?= htmlspecialchars($message) ?></p>
        </div>
    </div>
</body>
</html>
