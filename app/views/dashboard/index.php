<?php
$isAdmin = has_role('admin');
$displayName = trim((string)($user['name'] ?? 'User'));
$roleLabel = $isAdmin ? 'Admin' : 'User';
$onlineDevices = (int)($deviceStatus['active'] ?? 0);
$inactiveDevices = (int)($deviceStatus['inactive'] ?? 0);
$maintenanceDevices = (int)($deviceStatus['maintenance'] ?? 0);
$criticalAlerts = (int)($alertSeverity['critical'] ?? 0);
$highAlerts = (int)($alertSeverity['high'] ?? 0);
$mediumAlerts = (int)($alertSeverity['medium'] ?? 0);
$lowAlerts = (int)($alertSeverity['low'] ?? 0);
$latestUpdate = $latestReadings[0]['recorded_at'] ?? null;
$deviceHealthPercent = $totalDevices > 0 ? (int)round(($onlineDevices / $totalDevices) * 100) : 0;
$estimatedCostToday = (float)$totalEnergyToday * 4.5;
$topDevice = $topDevices[0] ?? null;
$peakPower = $topDevice ? (float)$topDevice['total_power'] : 0;
$chartBars = [40, 45, 38, 52, 65, 78, 85, 72, 60, 55, 48, 42, 35, 30, 28, 33, 40, 58, 75, 92, 88, 65, 50, 42];
$statusTone = [
    'active' => 'bg-primary/10 text-primary',
    'inactive' => 'bg-error/10 text-error',
    'maintenance' => 'bg-amber-100 text-amber-700',
];
$statusLabel = [
    'active' => 'ONLINE',
    'inactive' => 'OFFLINE',
    'maintenance' => 'SERVICE',
];
$severityLabel = [
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High',
    'critical' => 'Critical',
];
$severityTone = [
    'low' => 'bg-sky-100 text-sky-700',
    'medium' => 'bg-amber-100 text-amber-700',
    'high' => 'bg-rose-100 text-rose-700',
    'critical' => 'bg-red-100 text-red-800',
];
$formatNumber = static fn ($value, int $decimals = 0): string => number_format((float)$value, $decimals);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
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
                        "on-primary": "#FFFFFF",
                        secondary: "#7C3AED",
                        "on-secondary": "#FFFFFF",
                        "surface-container": "#FFFFFF",
                        "surface-container-high": "#F1F5F9",
                        "surface-container-highest": "#E2E8F0",
                        "on-surface-variant": "#64748B",
                        error: "#EF4444",
                        success: "#10B981"
                    },
                    fontFamily: {
                        headline: ["Inter"],
                        body: ["Inter"],
                        label: ["Inter"]
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
    $activePage = 'dashboard';
    $sidebarActionHref = base_path_url('/analytics');
    $sidebarActionLabel = 'Export Report';
    $sidebarActionIcon = 'file_download';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen bg-surface pt-16 lg:ml-64">
        <div class="mx-auto max-w-7xl p-8">
            <div class="mb-8 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h1 class="mb-1 text-3xl font-black uppercase tracking-tight text-on-surface">System Overview</h1>
                    <p class="font-medium text-on-surface-variant">
                        <?= $isAdmin ? 'Real-time overview across all users, devices, and alerts.' : 'Real-time monitoring for your connected smart meter devices.' ?>
                    </p>
                    <?php if ($latestUpdate): ?>
                        <p class="mt-2 text-sm text-on-surface-variant">Latest update: <?= htmlspecialchars($latestUpdate) ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a class="flex items-center gap-2 rounded-lg border border-outline-variant bg-white px-5 py-2.5 text-sm font-semibold text-on-surface transition-all hover:bg-surface-container-high active:scale-95" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">
                        <span class="material-symbols-outlined text-sm">memory</span> Devices
                    </a>
                    <a class="flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/20 transition-all hover:brightness-110 active:scale-95" href="<?= htmlspecialchars(base_path_url('/settings')) ?>">
                        <span class="material-symbols-outlined text-sm">person</span> <?= htmlspecialchars($roleLabel) ?>
                    </a>
                </div>
            </div>

            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-4 lg:grid-cols-6">
                <div class="group relative overflow-hidden rounded-xl border border-outline-variant bg-surface-container p-6 shadow-sm md:col-span-2 lg:col-span-2">
                    <div class="mb-6 flex items-start justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-primary">Energy Total</span>
                        <span class="material-symbols-outlined text-primary opacity-50">bolt</span>
                    </div>
                    <div class="relative z-10">
                        <span class="text-5xl font-black tracking-tighter text-on-surface"><?= $formatNumber($totalEnergyToday, 3) ?></span>
                        <span class="ml-1 text-xl font-bold text-on-surface-variant">kWh</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-success">
                        <span class="material-symbols-outlined text-sm">trending_up</span>
                        <span class="text-xs font-semibold"><?= (int)$totalReadingsToday ?> readings today</span>
                    </div>
                    <div class="pointer-events-none absolute bottom-0 right-0 h-32 w-32 opacity-[0.03] transition-opacity group-hover:opacity-[0.06]">
                        <span class="material-symbols-outlined translate-x-8 translate-y-8 text-9xl">electric_meter</span>
                    </div>
                </div>

                <div id="billing-section" class="rounded-xl border border-l-4 border-l-secondary border-outline-variant bg-surface-container p-6 shadow-sm md:col-span-2 lg:col-span-2">
                    <div class="mb-6 flex items-start justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Est. Daily Cost</span>
                        <span class="material-symbols-outlined text-on-surface-variant">payments</span>
                    </div>
                    <div>
                        <span class="text-5xl font-black tracking-tighter text-on-surface"><?= $formatNumber($estimatedCostToday, 2) ?></span>
                        <span class="ml-1 text-xl font-bold text-on-surface-variant">THB</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-secondary">
                        <span class="material-symbols-outlined text-sm">insights</span>
                        <span class="text-xs font-semibold">Calculated from today's energy usage</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 md:col-span-4 lg:col-span-2">
                    <div class="flex flex-col justify-between rounded-xl border border-outline-variant bg-surface-container p-5 shadow-sm">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Nodes Online</span>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-primary"><?= $onlineDevices ?></span>
                            <div class="mb-2 h-2 w-2 rounded-full bg-primary shadow-[0_0_8px_rgba(37,99,235,0.4)]"></div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-between rounded-xl border border-outline-variant bg-surface-container p-5 shadow-sm">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Nodes Offline</span>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-error"><?= $inactiveDevices ?></span>
                            <div class="mb-2 h-2 w-2 rounded-full bg-error shadow-[0_0_8px_rgba(239,68,68,0.4)]"></div>
                        </div>
                    </div>
                </div>

                <div id="analytics-section" class="flex min-h-[400px] flex-col rounded-xl border border-outline-variant bg-surface-container p-8 shadow-sm md:col-span-4 lg:col-span-4">
                    <div class="mb-10 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold tracking-tight text-on-surface">Load Distribution (24h)</h3>
                            <p class="text-xs text-on-surface-variant">Visual summary of energy activity across today.</p>
                        </div>
                        <div class="flex gap-1 rounded-lg bg-surface-container-high p-1">
                            <button class="rounded-md bg-white px-4 py-1.5 text-xs font-bold text-primary shadow-sm">LIVE</button>
                            <button class="px-4 py-1.5 text-xs font-bold text-on-surface-variant hover:text-on-surface">TODAY</button>
                        </div>
                    </div>
                    <div class="group relative flex flex-grow items-end gap-1.5 px-2">
                        <div class="pointer-events-none absolute inset-0 flex flex-col justify-between border-b border-outline-variant/50">
                            <div class="w-full border-t border-outline-variant/30"></div>
                            <div class="w-full border-t border-outline-variant/30"></div>
                            <div class="w-full border-t border-outline-variant/30"></div>
                            <div class="w-full border-t border-outline-variant/30"></div>
                        </div>
                        <?php foreach ($chartBars as $index => $height): ?>
                            <?php $isPeak = $height >= 75; ?>
                            <div class="flex-1 rounded-t-sm <?= $isPeak ? 'border-t-2 border-secondary bg-secondary/20 hover:bg-secondary/30' : 'bg-primary/10 hover:bg-primary/20' ?> transition-all" style="height: <?= (int)$height ?>%"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 flex justify-between px-1 text-[10px] font-bold text-on-surface-variant opacity-60">
                        <span>00:00</span>
                        <span>06:00</span>
                        <span>12:00</span>
                        <span>18:00</span>
                        <span>23:59</span>
                    </div>
                </div>

                <div class="flex flex-col gap-6 md:col-span-4 lg:col-span-2">
                    <div class="flex-grow rounded-xl border border-outline-variant bg-surface-container p-6 shadow-sm">
                        <h4 class="mb-4 text-sm font-bold uppercase tracking-widest text-on-surface">Device Status</h4>
                        <div class="space-y-4">
                            <?php if (empty($devices)): ?>
                                <div class="rounded-lg bg-surface-container-high p-4 text-sm text-on-surface-variant">No devices found.</div>
                            <?php else: ?>
                                <?php foreach (array_slice($devices, 0, 3) as $device): ?>
                                    <?php $deviceState = $device['status'] ?? 'inactive'; ?>
                                    <div class="group flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-outline-variant bg-surface-container-high">
                                                <span class="material-symbols-outlined text-xl <?= $deviceState === 'inactive' ? 'text-error' : 'text-primary' ?>">memory</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold"><?= htmlspecialchars($device['device_name']) ?></div>
                                                <div class="text-[10px] font-medium uppercase text-on-surface-variant"><?= htmlspecialchars($device['location'] ?? 'No location') ?></div>
                                            </div>
                                        </div>
                                        <span class="rounded px-2 py-0.5 text-[10px] font-bold <?= htmlspecialchars($statusTone[$deviceState] ?? 'bg-slate-100 text-slate-700') ?>"><?= htmlspecialchars($statusLabel[$deviceState] ?? strtoupper($deviceState)) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <a class="mt-6 block w-full rounded-lg border border-outline-variant py-2.5 text-center text-xs font-bold text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">Manage All Devices</a>
                    </div>

                    <div class="rounded-xl border border-outline-variant bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined animate-pulse text-secondary">warning</span>
                            <div class="text-xs">
                                <span class="block font-bold text-on-surface">Alert Overview</span>
                                <span class="text-on-surface-variant">Critical <?= $criticalAlerts ?>, high <?= $highAlerts ?>, medium <?= $mediumAlerts ?>, low <?= $lowAlerts ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-12 overflow-hidden rounded-xl border border-outline-variant bg-surface-container shadow-sm">
                <div class="flex items-center justify-between bg-surface-container-high/50 p-6">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-on-surface-variant">Latest Device Readings</h3>
                    <div class="flex items-center gap-4 text-[10px] font-bold uppercase text-on-surface-variant">
                        <div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-surface-container-highest"></div> Stable</div>
                        <div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-primary/40"></div> Active</div>
                        <div class="flex items-center gap-2"><div class="h-2 w-2 rounded-full bg-secondary"></div> Peak</div>
                    </div>
                </div>
                <div class="relative h-48 w-full bg-surface-container-high">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-full overflow-x-auto px-6">
                            <table class="w-full min-w-[900px] border-collapse">
                                <thead>
                                    <tr class="text-left text-[11px] uppercase tracking-widest text-on-surface-variant">
                                        <th class="pb-3">Device</th>
                                        <th class="pb-3">Location</th>
                                        <th class="pb-3">Voltage</th>
                                        <th class="pb-3">Current</th>
                                        <th class="pb-3">Power</th>
                                        <th class="pb-3">Energy</th>
                                        <th class="pb-3">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-on-surface">
                                    <?php if (empty($latestPerDevice)): ?>
                                        <tr>
                                            <td class="py-3 text-on-surface-variant" colspan="7">No readings available.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($latestPerDevice, 0, 4) as $row): ?>
                                            <tr class="border-t border-outline-variant/60">
                                                <td class="py-3 font-semibold"><?= htmlspecialchars($row['device_name']) ?></td>
                                                <td class="py-3 text-on-surface-variant"><?= htmlspecialchars($row['location'] ?? '-') ?></td>
                                                <td class="py-3"><?= $formatNumber($row['voltage'] ?? 0, 2) ?></td>
                                                <td class="py-3"><?= $formatNumber($row['current'] ?? 0, 2) ?></td>
                                                <td class="py-3"><?= $formatNumber($row['power'] ?? 0, 2) ?></td>
                                                <td class="py-3"><?= $formatNumber($row['energy'] ?? 0, 3) ?></td>
                                                <td class="py-3 text-on-surface-variant"><?= htmlspecialchars($row['recorded_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div id="alerts-section" class="rounded-xl border border-outline-variant bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-on-surface">Top Power Device</h3>
                            <p class="text-xs text-on-surface-variant">Highest consumption recorded today</p>
                        </div>
                        <span class="material-symbols-outlined text-primary">electric_bolt</span>
                    </div>
                    <?php if ($topDevice): ?>
                        <div class="space-y-4">
                            <div>
                                <div class="text-xl font-bold"><?= htmlspecialchars($topDevice['device_name']) ?></div>
                                <div class="text-sm text-on-surface-variant"><?= htmlspecialchars($topDevice['device_type']) ?><?= !empty($topDevice['location']) ? ' • ' . htmlspecialchars($topDevice['location']) : '' ?></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="rounded-lg bg-surface-container-high p-4">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Total Power</div>
                                    <div class="mt-2 text-2xl font-black text-on-surface"><?= $formatNumber($peakPower, 2) ?> <span class="text-base font-bold text-on-surface-variant">W</span></div>
                                </div>
                                <div class="rounded-lg bg-surface-container-high p-4">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Total Energy</div>
                                    <div class="mt-2 text-2xl font-black text-on-surface"><?= $formatNumber($topDevice['total_energy'] ?? 0, 3) ?> <span class="text-base font-bold text-on-surface-variant">kWh</span></div>
                                </div>
                            </div>
                            <a class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white" href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . (int)$topDevice['id'])) ?>">
                                <span class="material-symbols-outlined text-sm">open_in_new</span> View Device Detail
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="rounded-lg bg-surface-container-high p-4 text-sm text-on-surface-variant">No device power data for today.</div>
                    <?php endif; ?>
                </div>

                <div class="rounded-xl border border-outline-variant bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-on-surface">Latest Alerts</h3>
                            <p class="text-xs text-on-surface-variant">Recent system notifications</p>
                        </div>
                        <span class="material-symbols-outlined text-secondary">notifications</span>
                    </div>
                    <div class="space-y-3">
                        <?php if (empty($alerts)): ?>
                            <div class="rounded-lg bg-surface-container-high p-4 text-sm text-on-surface-variant">No alerts available.</div>
                        <?php else: ?>
                            <?php foreach (array_slice($alerts, 0, 4) as $alert): ?>
                                <?php $sev = $alert['severity'] ?? 'low'; ?>
                                <div class="rounded-lg border border-outline-variant bg-surface-container-high p-4">
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <span class="rounded px-2 py-1 text-[10px] font-bold uppercase <?= htmlspecialchars($severityTone[$sev] ?? 'bg-slate-100 text-slate-700') ?>"><?= htmlspecialchars($severityLabel[$sev] ?? $sev) ?></span>
                                        <span class="text-xs text-on-surface-variant"><?= htmlspecialchars($alert['created_at']) ?></span>
                                    </div>
                                    <div class="font-semibold text-on-surface"><?= htmlspecialchars($alert['alert_type']) ?></div>
                                    <p class="mt-1 text-sm text-on-surface-variant"><?= htmlspecialchars($alert['message']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>

    <nav class="fixed bottom-0 left-0 z-50 flex h-16 w-full items-center justify-around border-t border-outline-variant bg-white md:hidden">
        <a class="flex flex-col items-center gap-1 text-primary" href="<?= htmlspecialchars(base_path_url('/dashboard')) ?>">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-[10px] font-bold uppercase">Dashboard</span>
        </a>
        <a class="flex flex-col items-center gap-1 text-on-surface-variant" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">
            <span class="material-symbols-outlined">memory</span>
            <span class="text-[10px] font-bold uppercase">Devices</span>
        </a>
        <a class="flex flex-col items-center gap-1 text-on-surface-variant" href="<?= htmlspecialchars(base_path_url('/analytics')) ?>">
            <span class="material-symbols-outlined">insights</span>
            <span class="text-[10px] font-bold uppercase">Analytics</span>
        </a>
        <a class="flex flex-col items-center gap-1 text-on-surface-variant" href="<?= htmlspecialchars(base_path_url('/billing')) ?>">
            <span class="material-symbols-outlined">receipt_long</span>
            <span class="text-[10px] font-bold uppercase">Billing</span>
        </a>
        <a class="flex flex-col items-center gap-1 text-on-surface-variant" href="<?= htmlspecialchars(base_path_url('/alerts')) ?>">
            <span class="material-symbols-outlined">warning</span>
            <span class="text-[10px] font-bold uppercase">Alerts</span>
        </a>
    </nav>
</body>
</html>
