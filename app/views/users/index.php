<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$flashMap = [
    'à¹€à¸žà¸´à¹ˆà¸¡à¸œà¸¹à¹‰à¹ƒà¸Šà¹‰à¹€à¸£à¸µà¸¢à¸šà¸£à¹‰à¸­à¸¢à¹à¸¥à¹‰à¸§' => 'เพิ่มผู้ใช้เรียบร้อยแล้ว',
    'à¹à¸à¹‰à¹„à¸‚à¸œà¸¹à¹‰à¹ƒà¸Šà¹‰à¹€à¸£à¸µà¸¢à¸šà¸£à¹‰à¸­à¸¢à¹à¸¥à¹‰à¸§' => 'แก้ไขผู้ใช้เรียบร้อยแล้ว',
    'à¸¥à¸šà¸œà¸¹à¹‰à¹ƒà¸Šà¹‰à¹€à¸£à¸µà¸¢à¸šà¸£à¹‰à¸­à¸¢à¹à¸¥à¹‰à¸§' => 'ลบผู้ใช้เรียบร้อยแล้ว',
    'à¹„à¸¡à¹ˆà¸ªà¸²à¸¡à¸²à¸£à¸–à¸¥à¸šà¸šà¸±à¸à¸Šà¸µà¸•à¸±à¸§à¹€à¸­à¸‡à¹„à¸”à¹‰' => 'ไม่สามารถลบบัญชีตัวเองได้',
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
<html class="light" lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้</title>
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
    $sidebarActionHref = base_path_url('/users/create');
    $sidebarActionLabel = 'เพิ่มผู้ใช้';
    $sidebarActionIcon = 'person_add';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen bg-surface p-8 pt-24 lg:ml-64">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h1 class="text-4xl font-extrabold tracking-tight text-on-surface">จัดการผู้ใช้</h1>
                    <p class="mt-2 text-on-surface-variant">จัดการบัญชีผู้ใช้และสิทธิ์การเข้าถึงระบบของทีมงาน</p>
                </div>
                <a class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary to-secondary px-5 py-2.5 text-sm font-bold text-white shadow-panel transition-all hover:brightness-110 active:scale-95" href="<?= htmlspecialchars(base_path_url('/users/create')) ?>">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    <span>เพิ่มผู้ใช้</span>
                </a>
            </div>

            <?php if ($successMessage !== null): ?>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-panel">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if ($errorMessage !== null): ?>
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-panel">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <section class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                <div class="border-b border-outline-variant/30 p-6">
                    <h2 class="text-xl font-bold">User Access List</h2>
                    <p class="mt-1 text-sm text-on-surface-variant">รายการผู้ใช้ทั้งหมดที่มีสิทธิ์เข้าใช้งานในระบบ</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-xs font-black uppercase tracking-widest text-on-surface-variant">
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">ชื่อ</th>
                                <th class="px-6 py-4">อีเมล</th>
                                <th class="px-6 py-4">บทบาท</th>
                                <th class="px-6 py-4 text-right">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td class="px-6 py-6 text-sm text-on-surface-variant" colspan="5">ยังไม่มีผู้ใช้ในระบบ</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $item): ?>
                                    <tr class="transition-colors hover:bg-surface-container-low/70">
                                        <td class="px-6 py-4 text-sm font-semibold text-primary"><?= (int)$item['id'] ?></td>
                                        <td class="px-6 py-4 text-sm font-medium"><?= htmlspecialchars($item['name']) ?></td>
                                        <td class="px-6 py-4 text-sm text-on-surface-variant"><?= htmlspecialchars($item['email']) ?></td>
                                        <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['role']) ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a class="inline-flex items-center rounded-lg bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-100" href="<?= htmlspecialchars(base_path_url('/users/edit?id=' . $item['id'])) ?>">แก้ไข</a>
                                                <?php if ((int)$item['id'] !== (int)$user['id']): ?>
                                                    <form method="POST" action="<?= htmlspecialchars(base_path_url('/users/delete')) ?>" onsubmit="return confirm('ยืนยันการลบผู้ใช้นี้?')">
                                                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                                        <button type="submit" class="inline-flex items-center rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">ลบ</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
