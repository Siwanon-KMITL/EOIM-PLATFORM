<?php
$isAdmin = has_role('admin');
$displayName = trim((string)($user['name'] ?? 'User'));
$totalDevices = count($devices);
$onlineDevices = 0;
$offlineDevices = 0;
$maintenanceDevices = 0;

foreach ($devices as $device) {
    $status = $device['status'] ?? 'inactive';
    if ($status === 'active') {
        $onlineDevices++;
    } elseif ($status === 'maintenance') {
        $maintenanceDevices++;
    } else {
        $offlineDevices++;
    }
}

$healthPercent = $totalDevices > 0 ? round(($onlineDevices / $totalDevices) * 100, 1) : 0;
$statusLabel = [
    'active' => 'Online',
    'inactive' => 'Offline',
    'maintenance' => 'Maintenance',
];
$statusTone = [
    'active' => 'bg-blue-50 text-blue-700',
    'inactive' => 'bg-slate-200 text-slate-700',
    'maintenance' => 'bg-violet-50 text-violet-700',
];
$statusDot = [
    'active' => 'bg-blue-600',
    'inactive' => 'bg-slate-500',
    'maintenance' => 'bg-violet-600',
];
$deviceTypeIcon = [
    'air_conditioner' => 'ac_unit',
    'refrigerator' => 'kitchen',
    'washing_machine' => 'local_laundry_service',
];
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
<body class="overflow-x-hidden bg-surface text-on-surface font-body">
    <?php
    $activePage = 'devices';
    $sidebarActionHref = base_path_url('/devices/create');
    $sidebarActionLabel = 'Add Device';
    $sidebarActionIcon = 'add';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen pt-24 pb-12 pr-6 lg:pl-72">
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h1 class="mb-2 text-4xl font-black tracking-tight text-on-surface">Device Management</h1>
                    <p class="max-w-lg text-on-surface-variant">
                        <?= $isAdmin ? 'Manage and monitor all registered smart meter devices across the full platform.' : 'Manage and monitor your assigned smart meter devices from a single analytics workspace.' ?>
                    </p>
                </div>
                <a class="flex h-12 items-center gap-3 rounded-lg bg-gradient-to-r from-primary to-secondary px-8 font-bold text-white transition-all hover:brightness-110 active:scale-95" href="<?= htmlspecialchars(base_path_url('/devices/create')) ?>">
                    <span class="material-symbols-outlined">add</span>
                    <span>Add Meter</span>
                </a>
            </div>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700 shadow-panel">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-panel">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="flex flex-col items-center gap-4 rounded-xl bg-surface-container p-4 shadow-panel lg:flex-row">
                <div class="relative w-full lg:flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                    <input class="w-full rounded-lg border border-outline-variant bg-surface pl-12 pr-4 py-3 text-sm transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Search by Device ID, name, location, or MAC address..." type="text"/>
                </div>
                <div class="flex w-full gap-4 lg:w-auto">
                    <div class="relative flex-1 lg:w-48">
                        <select class="w-full rounded-lg border border-outline-variant bg-surface-container-low px-4 py-3 text-sm appearance-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option>All Zones</option>
                            <?php foreach (array_unique(array_filter(array_map(static fn($device) => $device['location'] ?? '', $devices))) as $location): ?>
                                <option><?= htmlspecialchars($location) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant">expand_more</span>
                    </div>
                    <div class="relative flex-1 lg:w-40">
                        <select class="w-full rounded-lg border border-outline-variant bg-surface-container-low px-4 py-3 text-sm appearance-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option>All Status</option>
                            <option>Online</option>
                            <option>Offline</option>
                            <option>Maintenance</option>
                        </select>
                        <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant">tune</span>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-outline-variant/60 bg-surface-container-low text-[10px] uppercase tracking-widest text-on-surface-variant">
                                <th class="px-6 py-5 font-bold">Meter ID</th>
                                <th class="px-6 py-5 font-bold">Device Name</th>
                                <th class="px-6 py-5 font-bold">Zone / Building</th>
                                <th class="px-6 py-5 font-bold">Status</th>
                                <th class="px-6 py-5 font-bold">MAC Address</th>
                                <?php if ($isAdmin): ?>
                                    <th class="px-6 py-5 font-bold">Owner</th>
                                <?php endif; ?>
                                <th class="px-6 py-5 font-bold">Last Updated</th>
                                <th class="px-6 py-5 text-right font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/40">
                            <?php if (empty($devices)): ?>
                                <tr>
                                    <td class="px-6 py-8 text-sm text-on-surface-variant" colspan="<?= $isAdmin ? 8 : 7 ?>">No devices found in the platform.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($devices as $index => $device): ?>
                                    <?php
                                    $status = $device['status'] ?? 'inactive';
                                    $rowTint = $index % 2 === 1 ? 'bg-slate-50/70' : 'bg-white';
                                    $icon = $deviceTypeIcon[$device['device_type'] ?? ''] ?? 'bolt';
                                    ?>
                                    <tr class="<?= $rowTint ?> group transition-colors hover:bg-blue-50/70">
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-sm font-semibold text-primary">MT-<?= str_pad((string)($device['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-container-high text-primary">
                                                    <span class="material-symbols-outlined text-sm"><?= htmlspecialchars($icon) ?></span>
                                                </div>
                                                <span class="text-sm font-medium"><?= htmlspecialchars($device['device_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm"><?= htmlspecialchars($device['location'] ?? 'Unassigned zone') ?></span>
                                                <span class="text-xs text-on-surface-variant"><?= htmlspecialchars(str_replace('_', ' ', $device['device_type'] ?? 'device')) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 rounded px-2 py-1 text-[10px] font-bold uppercase <?= htmlspecialchars($statusTone[$status] ?? 'bg-slate-100 text-slate-700') ?>">
                                                <span class="h-1.5 w-1.5 rounded-full <?= htmlspecialchars($statusDot[$status] ?? 'bg-slate-500') ?>"></span>
                                                <?= htmlspecialchars($statusLabel[$status] ?? ucfirst($status)) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-semibold tracking-tight text-on-surface"><?= htmlspecialchars($device['ip_address'] ?? '-') ?></span>
                                        </td>
                                        <?php if ($isAdmin): ?>
                                            <td class="px-6 py-4">
                                                <span class="text-sm text-on-surface-variant"><?= htmlspecialchars($device['owner_name'] ?? 'Unassigned') ?></span>
                                            </td>
                                        <?php endif; ?>
                                        <td class="px-6 py-4">
                                            <span class="text-xs text-on-surface-variant"><?= htmlspecialchars($device['updated_at'] ?? $device['created_at'] ?? '-') ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-primary transition hover:bg-blue-100" href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . (int)$device['id'])) ?>">View</a>
                                                <a class="inline-flex items-center rounded-lg bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700 transition hover:bg-violet-100" href="<?= htmlspecialchars(base_path_url('/devices/edit?id=' . (int)$device['id'])) ?>">Edit</a>
                                                <?php if (has_role(['admin', 'staff'])): ?>
                                                    <form method="POST" action="<?= htmlspecialchars(base_path_url('/devices/delete')) ?>" onsubmit="return confirm('Confirm delete this device?')" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= (int)$device['id'] ?>">
                                                        <button type="submit" class="inline-flex items-center rounded-lg bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-rose-100 hover:text-rose-700">Delete</button>
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
                <div class="flex flex-col items-center justify-between gap-4 border-t border-outline-variant/40 bg-surface-container-low px-6 py-4 sm:flex-row">
                    <span class="text-xs text-on-surface-variant">
                        Showing <span class="font-bold text-on-surface">1-<?= $totalDevices ?></span> of <span class="font-bold text-on-surface"><?= $totalDevices ?></span> meters
                    </span>
                    <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                        <span>Rows per page:</span>
                        <span class="rounded-md bg-white px-3 py-1.5 font-semibold text-on-surface shadow-sm"><?= max($totalDevices, 10) ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-xl border-l-4 border-primary/50 bg-surface-container-high p-6 shadow-panel">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="mb-1 text-[10px] uppercase tracking-widest text-on-surface-variant">Total Meters</p>
                            <h3 class="text-3xl font-black tracking-tighter"><?= $totalDevices ?></h3>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-primary/30">sensors</span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px]">
                        <span class="font-bold text-primary"><?= $onlineDevices ?> online now</span>
                        <span class="opacity-50 text-on-surface-variant">|</span>
                        <span class="text-on-surface-variant"><?= $maintenanceDevices ?> in maintenance</span>
                    </div>
                </div>

                <div class="rounded-xl border-l-4 border-secondary/50 bg-surface-container-high p-6 shadow-panel">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="mb-1 text-[10px] uppercase tracking-widest text-on-surface-variant">Health Status</p>
                            <h3 class="text-3xl font-black tracking-tighter"><?= number_format($healthPercent, 1) ?>%</h3>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-secondary/30">health_metrics</span>
                    </div>
                    <div class="mt-2 h-1 w-full rounded-full bg-white">
                        <div class="h-1 rounded-full bg-secondary" style="width: <?= max(0, min(100, $healthPercent)) ?>%"></div>
                    </div>
                    <p class="mt-3 text-[10px] text-on-surface-variant">Calculated from currently online devices in this view</p>
                </div>

                <div class="rounded-xl border-l-4 border-slate-400 bg-surface-container-high p-6 shadow-panel">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="mb-1 text-[10px] uppercase tracking-widest text-on-surface-variant">Attention Needed</p>
                            <h3 class="text-3xl font-black tracking-tighter text-slate-700"><?= $offlineDevices + $maintenanceDevices ?></h3>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-slate-400">warning</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-slate-500 <?= ($offlineDevices + $maintenanceDevices) > 0 ? 'animate-pulse' : '' ?>"></span>
                        <span class="text-[10px] font-medium text-on-surface-variant">Offline <?= $offlineDevices ?> devices, maintenance <?= $maintenanceDevices ?> devices</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
