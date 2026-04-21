<?php
$displayName = trim((string)($user['name'] ?? 'User'));
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= htmlspecialchars($title) ?></title>
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
<body class="overflow-x-hidden bg-surface text-on-surface">
    <?php
    $activePage = 'devices';
    $sidebarActionHref = base_path_url('/devices');
    $sidebarActionLabel = 'Back To Devices';
    $sidebarActionIcon = 'arrow_back';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen pt-24 pb-12 pr-6 lg:pl-72">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h1 class="mb-2 text-4xl font-black tracking-tight text-on-surface">Edit Device</h1>
                    <p class="max-w-2xl text-on-surface-variant">Update device identity, ownership, status, and connectivity values without leaving the provisioning workspace.</p>
                </div>
                <a class="inline-flex items-center gap-2 rounded-lg border border-outline-variant bg-white px-5 py-2.5 text-sm font-semibold text-on-surface transition-all hover:bg-surface-container-low active:scale-95" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Back To List</span>
                </a>
            </div>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-panel">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <section class="rounded-xl bg-surface-container p-6 shadow-panel xl:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-on-surface">Device Configuration</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">Keep telemetry metadata, owner assignment, and secure credentials aligned with the actual installation.</p>
                    </div>

                    <form method="POST" action="<?= htmlspecialchars(base_path_url('/devices/update')) ?>" class="space-y-6">
                        <input type="hidden" name="id" value="<?= (int)$device['id'] ?>">

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Device Name</label>
                                <input class="rounded-lg border border-outline-variant bg-surface px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="device_name" type="text" value="<?= htmlspecialchars($device['device_name']) ?>" required>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Device Type</label>
                                <input class="rounded-lg border border-outline-variant bg-surface px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="device_type" type="text" value="<?= htmlspecialchars($device['device_type']) ?>" required>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Location</label>
                                <input class="rounded-lg border border-outline-variant bg-surface px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="location" type="text" value="<?= htmlspecialchars($device['location'] ?? '') ?>">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Status</label>
                                <select class="rounded-lg border border-outline-variant bg-surface px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="status">
                                    <option value="active" <?= $device['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $device['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="maintenance" <?= $device['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                </select>
                            </div>

                            <?php if (has_role('admin')): ?>
                                <div class="md:col-span-2 flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Assigned Owner</label>
                                    <select class="rounded-lg border border-outline-variant bg-surface px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="user_id">
                                        <?php foreach ($users as $owner): ?>
                                            <option value="<?= (int)$owner['id'] ?>" <?= ((int)$device['user_id'] === (int)$owner['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($owner['name'] . ' (' . $owner['email'] . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <div class="mb-4 flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">router</span>
                                <div>
                                    <h3 class="text-sm font-bold text-on-surface">Connectivity</h3>
                                    <p class="text-xs text-on-surface-variant">Update the meter identity fields used by your device integration layer.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">MAC Address</label>
                                    <input class="rounded-lg border border-outline-variant bg-white px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="ip_address" type="text" value="<?= htmlspecialchars($device['ip_address'] ?? '') ?>" placeholder="AA:BB:CC:DD:EE:FF">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Device Secret</label>
                                    <input class="rounded-lg border border-outline-variant bg-white px-4 py-3 text-sm text-on-surface transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" name="device_secret" type="text" value="<?= htmlspecialchars($device['device_secret'] ?? '') ?>" placeholder="Leave blank to keep current secret">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-3 border-t border-outline-variant/60 pt-4">
                            <a class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-on-surface-variant transition hover:bg-surface-container-low hover:text-on-surface" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">Cancel</a>
                            <button class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary to-secondary px-6 py-3 text-sm font-bold text-white shadow-panel transition-all hover:brightness-110 active:scale-95" type="submit">
                                <span class="material-symbols-outlined text-sm">save</span>
                                <span>Update Device</span>
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-xl bg-surface-container p-6 shadow-panel">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-[10px] uppercase tracking-widest text-on-surface-variant">Current State</p>
                                <h3 class="text-lg font-bold text-on-surface"><?= htmlspecialchars($device['device_name']) ?></h3>
                            </div>
                            <span class="material-symbols-outlined text-primary">memory</span>
                        </div>
                        <div class="space-y-3 text-sm text-on-surface-variant">
                            <div class="flex items-center justify-between rounded-lg bg-surface-container-low px-4 py-3">
                                <span>Status</span>
                                <span class="font-semibold text-on-surface"><?= htmlspecialchars(ucfirst($device['status'])) ?></span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-surface-container-low px-4 py-3">
                                <span>Location</span>
                                <span class="font-semibold text-on-surface"><?= htmlspecialchars($device['location'] ?: '-') ?></span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-surface-container-low px-4 py-3">
                                <span>MAC Address</span>
                                <span class="font-semibold text-on-surface"><?= htmlspecialchars($device['ip_address'] ?: '-') ?></span>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl bg-surface-container p-6 shadow-panel">
                        <div class="mb-4 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">shield</span>
                            <h3 class="text-lg font-bold text-on-surface">Update Notes</h3>
                        </div>
                        <div class="space-y-3 text-sm text-on-surface-variant">
                            <p>The system still stores this field internally as `ip_address` to stay compatible with the current database and API flow.</p>
                            <p>You can replace the device secret directly, or leave it unchanged by keeping the current value.</p>
                            <p>Use a consistent MAC format like `AA:BB:CC:DD:EE:FF` for easier search and support work.</p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
