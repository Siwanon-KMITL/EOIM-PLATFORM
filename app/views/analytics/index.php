<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$latestAt = $summary['latest_at'] ?? null;
$firstAt = $summary['first_at'] ?? null;
$formatNumber = static fn ($value, int $decimals = 1): string => number_format((float)$value, $decimals);
$formatPercent = static fn ($value, int $decimals = 1): string => number_format((float)$value, $decimals);
$activeDevices = (int)($statusCounts['active'] ?? 0);
$inactiveDevices = (int)($statusCounts['inactive'] ?? 0);
$maintenanceDevices = (int)($statusCounts['maintenance'] ?? 0);
$dateRangeLabel = $firstAt && $latestAt
    ? date('M d, Y', strtotime((string)$firstAt)) . ' - ' . date('M d, Y', strtotime((string)$latestAt))
    : 'No data available';
$maxBarValue = max(1, (float)$maxTrendValue);
$statusLabel = [
    'active' => 'Active',
    'inactive' => 'Inactive',
    'maintenance' => 'Maintenance',
];
$statusTone = [
    'active' => 'bg-blue-50 text-blue-700',
    'inactive' => 'bg-slate-200 text-slate-700',
    'maintenance' => 'bg-violet-50 text-violet-700',
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
                        "surface-container-highest": "#E0E7FF",
                        "on-surface-variant": "#64748B",
                        outline: "#94A3B8"
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
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="overflow-x-hidden bg-surface font-body text-on-surface">
    <?php
    $activePage = 'analytics';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="px-6 pb-12 pt-24 lg:ml-64">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h1 class="mb-2 text-4xl font-black tracking-tight md:text-5xl">Advanced Analytics</h1>
                    <p class="font-medium text-on-surface-variant">Historical precision data for <?= $isAdmin ? 'all grid infrastructure assets' : 'your assigned smart meter assets' ?>.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex rounded-lg bg-surface-container-low p-1">
                        <button class="rounded-md bg-white px-4 py-2 text-xs font-bold text-primary shadow-sm">Real-time</button>
                        <button class="px-4 py-2 text-xs font-bold text-on-surface-variant hover:text-on-surface">Historical</button>
                    </div>
                    <div class="flex items-center gap-3 rounded border border-outline-variant bg-white px-4 py-2 text-sm font-medium">
                        <span class="material-symbols-outlined text-sm text-on-surface-variant">calendar_today</span>
                        <span><?= htmlspecialchars($dateRangeLabel) ?></span>
                        <span class="material-symbols-outlined text-sm opacity-60">expand_more</span>
                    </div>
                    <a class="flex items-center gap-2 rounded bg-primary px-4 py-2 text-sm font-bold text-white transition-all hover:brightness-110" href="<?= htmlspecialchars(base_path_url('/billing')) ?>">
                        <span class="material-symbols-outlined text-sm">download</span>
                        <span>Export</span>
                    </a>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-xl border-l-4 border-primary bg-surface-container p-5 shadow-panel">
                    <div class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Peak Consumption</div>
                    <div class="text-3xl font-black text-on-surface"><?= $formatNumber($peakConsumption, 2) ?> <span class="text-sm font-medium text-on-surface-variant">kW</span></div>
                    <div class="mt-4 flex items-center gap-2 text-xs font-bold text-primary">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        <span><?= (int)($summary['total_readings'] ?? 0) ?> readings analyzed</span>
                    </div>
                </div>
                <div class="rounded-xl border-l-4 border-secondary bg-surface-container p-5 shadow-panel">
                    <div class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Grid Efficiency</div>
                    <div class="text-3xl font-black text-on-surface"><?= $formatPercent($gridEfficiency, 1) ?> <span class="text-sm font-medium text-on-surface-variant">%</span></div>
                    <div class="mt-4 flex items-center gap-2 text-xs font-bold text-secondary">
                        <span class="material-symbols-outlined text-xs">tune</span>
                        <span>Derived from average power factor</span>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-xl bg-surface-container p-5 shadow-panel md:col-span-2">
                    <div class="flex items-center gap-6">
                        <div class="flex-1">
                            <div class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">System Health Index</div>
                            <div class="text-3xl font-black text-on-surface"><?= htmlspecialchars($healthLabel) ?></div>
                            <div class="mt-2 text-sm text-on-surface-variant"><?= $activeDevices ?> active, <?= $maintenanceDevices ?> maintenance, <?= $inactiveDevices ?> inactive, <?= $totalAlerts ?> alerts</div>
                            <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-surface-container-low">
                                <div class="h-full bg-primary" style="width: <?= (int)$healthIndex ?>%"></div>
                            </div>
                        </div>
                        <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center opacity-20">
                            <span class="material-symbols-outlined text-7xl text-primary" style="font-variation-settings: 'FILL' 1;">security</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-xl bg-surface-container p-6 shadow-panel lg:col-span-2">
                    <div class="mb-8 flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold">Power Load Analysis</h3>
                            <p class="text-xs text-on-surface-variant">Average power trend across the latest available periods</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="material-symbols-outlined rounded bg-surface-container-high p-2 text-sm text-primary">show_chart</button>
                            <button class="material-symbols-outlined rounded bg-surface-container-low p-2 text-sm text-on-surface-variant opacity-50">bar_chart</button>
                        </div>
                    </div>
                    <div class="relative flex h-64 items-end gap-2 border-b border-outline-variant/50 px-2">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="h-32 w-full bg-gradient-to-t from-primary/20 to-transparent opacity-70 blur-xl"></div>
                        </div>
                        <?php foreach ($trendRows as $row): ?>
                            <?php $height = max(18, (int)round((((float)$row['avg_power']) / $maxBarValue) * 100)); ?>
                            <div class="flex-1 rounded-t-sm bg-primary/20 transition-all hover:bg-primary/40" style="height: <?= $height ?>%"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 flex justify-between gap-2 text-[10px] font-bold uppercase tracking-tighter text-on-surface-variant">
                        <?php foreach ($trendRows as $row): ?>
                            <span><?= htmlspecialchars($row['day_label']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="rounded-xl bg-surface-container p-6 shadow-panel">
                    <h3 class="mb-1 text-lg font-bold">Zone Distribution</h3>
                    <p class="mb-6 text-xs text-on-surface-variant">Energy allocation by location</p>
                    <div class="relative mx-auto mb-8 h-48 w-48">
                        <div class="absolute inset-0 rounded-full border-[12px] border-surface-container-low"></div>
                        <div class="absolute inset-0 rotate-45 rounded-full border-[12px] border-primary border-b-transparent border-r-transparent"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black"><?= (int)($zones[0]['share'] ?? 0) ?>%</span>
                            <span class="text-[10px] font-bold uppercase text-on-surface-variant"><?= htmlspecialchars($zones[0]['name'] ?? 'No data') ?></span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($zones as $zone): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full <?= htmlspecialchars($zone['color_class']) ?>"></span>
                                    <span class="text-sm font-medium"><?= htmlspecialchars($zone['name']) ?></span>
                                </div>
                                <span class="text-sm font-bold"><?= (int)$zone['share'] ?>%</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                <div class="flex items-center justify-between border-b border-outline-variant/40 p-6">
                    <div>
                        <h3 class="text-lg font-bold">Node Performance Comparison</h3>
                        <p class="text-xs text-on-surface-variant">Comparing current device readings against 7-day baseline</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex items-center gap-2 rounded border border-outline-variant/30 bg-surface-container-low px-3 py-1.5 text-xs font-bold">
                            <span class="material-symbols-outlined text-sm">filter_alt</span>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-xs font-black uppercase tracking-widest text-on-surface-variant">
                                <th class="px-6 py-4">Node ID</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Current Usage</th>
                                <th class="px-6 py-4">Prev. Period</th>
                                <th class="px-6 py-4">Variance</th>
                                <th class="px-6 py-4 text-right">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            <?php if (empty($comparisonRows)): ?>
                                <tr>
                                    <td class="px-6 py-4 text-sm text-on-surface-variant" colspan="6">No analytics comparison data available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($comparisonRows as $row): ?>
                                    <?php
                                    $previous = (float)($row['previous_usage'] ?? 0);
                                    $current = (float)($row['current_usage'] ?? 0);
                                    $variance = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
                                    $trendIcon = $variance > 1 ? 'trending_up' : ($variance < -1 ? 'trending_down' : 'trending_flat');
                                    $trendClass = $variance > 1 ? 'text-violet-700' : ($variance < -1 ? 'text-primary' : 'text-on-surface-variant');
                                    $status = $row['status'] ?? 'inactive';
                                    ?>
                                    <tr class="transition-colors hover:bg-surface-container-low/70">
                                        <td class="px-6 py-4 text-sm font-bold">NODE-<?= str_pad((string)($row['id'] ?? 0), 3, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase <?= htmlspecialchars($statusTone[$status] ?? 'bg-slate-200 text-slate-700') ?>">
                                                <?= htmlspecialchars($statusLabel[$status] ?? ucfirst((string)$status)) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium"><?= $formatNumber($current, 1) ?> kW</td>
                                        <td class="px-6 py-4 text-sm opacity-60"><?= $formatNumber($previous, 1) ?> kW</td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-bold <?= $trendClass ?>"><?= ($variance >= 0 ? '+' : '') . $formatPercent($variance, 1) ?>%</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="material-symbols-outlined <?= $trendClass ?>"><?= $trendIcon ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-xl border border-outline-variant/30 bg-surface-container-high p-6 shadow-panel">
                    <h4 class="mb-4 flex items-center gap-2 font-bold">
                        <span class="material-symbols-outlined text-primary">compare_arrows</span>
                        Entity Comparison
                    </h4>
                    <div class="space-y-4">
                        <?php if (empty($comparisonRows)): ?>
                            <div class="rounded bg-white p-3 text-sm text-on-surface-variant">No entities available for comparison.</div>
                        <?php else: ?>
                            <?php foreach (array_slice($comparisonRows, 0, 2) as $row): ?>
                                <div class="flex items-center justify-between rounded bg-white p-3">
                                    <span class="text-sm"><?= htmlspecialchars($row['device_name'] ?? ('Device #' . ($row['id'] ?? 0))) ?></span>
                                    <a class="material-symbols-outlined text-sm opacity-60 transition hover:opacity-100" href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . (int)($row['id'] ?? 0))) ?>">open_in_new</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button class="w-full rounded border-2 border-dashed border-outline-variant/40 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant transition-all hover:border-primary/50" type="button">
                            + Add Entity to Compare
                        </button>
                    </div>
                </div>

                <div class="flex flex-col justify-between rounded-xl bg-surface-container-low p-6 shadow-panel">
                    <div>
                        <h4 class="mb-2 font-bold">Automated Reporting</h4>
                        <p class="text-sm text-on-surface-variant">Schedule recurring data exports for stakeholder review.</p>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <button class="flex flex-col items-center justify-center gap-2 rounded bg-white p-4 transition-all hover:bg-surface-container-high" type="button">
                            <span class="material-symbols-outlined text-primary">description</span>
                            <span class="text-[10px] font-bold uppercase">CSV</span>
                        </button>
                        <button class="flex flex-col items-center justify-center gap-2 rounded bg-white p-4 transition-all hover:bg-surface-container-high" type="button">
                            <span class="material-symbols-outlined text-secondary">table_view</span>
                            <span class="text-[10px] font-bold uppercase">Excel</span>
                        </button>
                        <button class="flex flex-col items-center justify-center gap-2 rounded bg-white p-4 transition-all hover:bg-surface-container-high" type="button">
                            <span class="material-symbols-outlined text-violet-700">picture_as_pdf</span>
                            <span class="text-[10px] font-bold uppercase">PDF</span>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
