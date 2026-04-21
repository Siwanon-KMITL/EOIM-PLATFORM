<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$flashMap = [
    'à¸à¸£à¸¸à¸“à¸²à¸à¸£à¸­à¸à¸Šà¸·à¹ˆà¸­à¹à¸¥à¸°à¸­à¸µà¹€à¸¡à¸¥' => 'กรุณากรอกชื่อและอีเมล',
    'à¸£à¸¹à¸›à¹à¸šà¸šà¸­à¸µà¹€à¸¡à¸¥à¹„à¸¡à¹ˆà¸–à¸¹à¸à¸•à¹‰à¸­à¸‡' => 'รูปแบบอีเมลไม่ถูกต้อง',
    'à¸­à¸µà¹€à¸¡à¸¥à¸™à¸µà¹‰à¸–à¸¹à¸à¹ƒà¸Šà¹‰à¸‡à¸²à¸™à¹à¸¥à¹‰à¸§' => 'อีเมลนี้ถูกใช้งานแล้ว',
    'à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹à¸¥à¸°à¸¢à¸·à¸™à¸¢à¸±à¸™à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¹„à¸¡à¹ˆà¸•à¸£à¸‡à¸à¸±à¸™' => 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน',
    'à¸£à¸«à¸±à¸ªà¸œà¹ˆà¸²à¸™à¸•à¹‰à¸­à¸‡à¸¡à¸µà¸„à¸§à¸²à¸¡à¸¢à¸²à¸§à¸­à¸¢à¹ˆà¸²à¸‡à¸™à¹‰à¸­à¸¢ 6 à¸•à¸±à¸§à¸­à¸±à¸à¸©à¸£' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
];
$errorMessage = $_SESSION['error'] ?? null;
if ($errorMessage !== null && isset($flashMap[$errorMessage])) {
    $errorMessage = $flashMap[$errorMessage];
}
?>
<!DOCTYPE html>
<html class="light" lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขผู้ใช้</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        surface: "#F8FAFC",
                        "on-surface": "#1E293B",
                        "outline-variant": "#E2E8F0",
                        primary: "#2563EB",
                        secondary: "#7C3AED",
                        "surface-container": "#FFFFFF",
                        "surface-container-low": "#F1F5F9",
                        "surface-container-high": "#EEF2FF",
                        "surface-container-highest": "#E0E7FF",
                        "on-surface-variant": "#64748B"
                    },
                    fontFamily: {
                        body: ["Inter"]
                    },
                    boxShadow: {
                        panel: "0 20px 45px rgba(37, 99, 235, 0.08)"
                    }
                }
            }
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <?php
    $activePage = 'settings';
    $sidebarActionHref = base_path_url('/users');
    $sidebarActionLabel = 'กลับหน้ารายการ';
    $sidebarActionIcon = 'arrow_back';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen bg-surface p-8 pt-24 lg:ml-64">
        <div class="mx-auto max-w-4xl space-y-6">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-on-surface">แก้ไขผู้ใช้</h1>
                <p class="mt-2 text-on-surface-variant">แก้ไขข้อมูลผู้ใช้และสามารถตั้งรหัสผ่านใหม่ได้จากหน้านี้</p>
            </div>

            <?php if ($errorMessage !== null): ?>
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-panel">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <section class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                <form method="POST" action="<?= htmlspecialchars(base_path_url('/users/update')) ?>">
                    <input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>">
                    <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">ชื่อ</label>
                            <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" type="text" name="name" value="<?= htmlspecialchars($editUser['name']) ?>" required>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">อีเมล</label>
                            <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">บทบาท</label>
                            <select class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" name="role">
                                <option value="viewer" <?= $editUser['role'] === 'viewer' ? 'selected' : '' ?>>viewer</option>
                                <option value="staff" <?= $editUser['role'] === 'staff' ? 'selected' : '' ?>>staff</option>
                                <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">รหัสผ่านใหม่</label>
                            <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" type="password" name="password">
                        </div>
                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">ยืนยันรหัสผ่านใหม่</label>
                            <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" type="password" name="confirm_password">
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 bg-surface-container-low p-6">
                        <a class="px-6 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:text-on-surface" href="<?= htmlspecialchars(base_path_url('/users')) ?>">ยกเลิก</a>
                        <button class="rounded-lg bg-gradient-to-r from-primary to-secondary px-6 py-2 font-bold text-white shadow-panel transition-all hover:brightness-110 active:scale-95" type="submit">อัปเดตผู้ใช้</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
