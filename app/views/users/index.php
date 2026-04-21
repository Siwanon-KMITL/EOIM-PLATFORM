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
                <p class="page-subtitle">จัดการบัญชีผู้ใช้และสิทธิ์การเข้าถึง</p>
            </div>
            <div class="main-actions">
                <a href="<?= htmlspecialchars(base_path_url('/users/create')) ?>" class="button">เพิ่มผู้ใช้</a>
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
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>บทบาท</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5">ยังไม่มีผู้ใช้ในระบบ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $item): ?>
                                <tr>
                                    <td><?= (int)$item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= htmlspecialchars($item['email']) ?></td>
                                    <td><?= htmlspecialchars($item['role']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars(base_path_url('/users/edit?id=' . $item['id'])) ?>" class="button ghost small">แก้ไข</a>
                                        <?php if ($item['id'] !== $user['id']): ?>
                                            <form method="POST" action="<?= htmlspecialchars(base_path_url('/users/delete')) ?>" style="display:inline-block; margin:0;">
                                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                                <button type="submit" class="button danger small" onclick="return confirm('ยืนยันลบผู้ใช้นี้?')">ลบ</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
