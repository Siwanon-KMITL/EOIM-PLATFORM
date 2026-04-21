<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$latest = $latestReading ?: [
    'voltage' => 0,
    'current' => 0,
    'power' => 0,
    'energy' => 0,
    'frequency' => 0,
    'power_factor' => 0,
    'recorded_at' => null,
];

$recentReadings = array_slice(array_reverse($readings), -12);
$powerBars = [];
$maxPower = 0.0;

foreach ($recentReadings as $reading) {
    $value = (float)($reading['power'] ?? 0);
    $powerBars[] = $value;
    $maxPower = max($maxPower, $value);
}

if (empty($powerBars)) {
    $powerBars = array_fill(0, 12, 0);
}

$chartHeights = array_map(
    static fn ($value) => $maxPower > 0 ? max(18, (int) round(($value / $maxPower) * 100)) : 18,
    $powerBars
);

$metrics = [
    [
        'node_id' => 'NODE-' . str_pad((string) $device['id'], 3, '0', STR_PAD_LEFT) . '-TX',
        'status' => $device['status'] ?? 'inactive',
        'current_usage' => (float) ($latest['power'] ?? 0),
        'previous_usage' => (float) (($summaryToday['avg_voltage_today'] ?? 0) * ($summaryToday['avg_current_today'] ?? 0)),
    ],
    [
        'node_id' => 'NODE-' . str_pad((string) $device['id'], 3, '0', STR_PAD_LEFT) . '-PF',
        'status' => $device['status'] ?? 'inactive',
        'current_usage' => (float) ($latest['voltage'] ?? 0),
        'previous_usage' => (float) ($summaryToday['avg_voltage_today'] ?? 0),
    ],
    [
        'node_id' => 'NODE-' . str_pad((string) $device['id'], 3, '0', STR_PAD_LEFT) . '-CR',
        'status' => $device['status'] ?? 'inactive',
        'current_usage' => (float) ($latest['current'] ?? 0) * 10,
        'previous_usage' => (float) ($summaryToday['avg_current_today'] ?? 0) * 10,
    ],
    [
        'node_id' => 'NODE-' . str_pad((string) $device['id'], 3, '0', STR_PAD_LEFT) . '-EN',
        'status' => $device['status'] ?? 'inactive',
        'current_usage' => (float) ($summaryToday['total_energy_today'] ?? 0) * 10,
        'previous_usage' => (float) (($summary['total_energy'] ?? 0) / max(1, (int)($summary['total_readings'] ?? 1))) * 10,
    ],
];

$peakConsumption = !empty($readings)
    ? max(array_map(static fn ($item) => (float)($item['power'] ?? 0), $readings))
    : 0;
$avgPower = $summaryToday['total_readings_today'] > 0
    ? (float)$summaryToday['total_power_today'] / max(1, (int)$summaryToday['total_readings_today'])
    : 0;
$gridEfficiency = (float)($latest['power_factor'] ?? 0) * 100;
$healthIndex = $device['status'] === 'active' ? 82 : ($device['status'] === 'maintenance' ? 58 : 24);
$industrialShare = min(78, max(18, (int) round(($peakConsumption / max(1, $peakConsumption + $avgPower)) * 100)));
$dataCenterShare = min(50, max(8, (int) round(($gridEfficiency / 100) * 28)));
$residentialShare = max(6, 100 - $industrialShare - $dataCenterShare - 11);
$publicShare = max(5, 100 - $industrialShare - $dataCenterShare - $residentialShare);
$format = static fn ($value, int $decimals = 2): string => number_format((float)$value, $decimals);
$statusLabel = [
    'active' => 'Active',
    'inactive' => 'Inactive',
    'maintenance' => 'Maintenance',
];
$statusTone = [
    'active' => 'bg-primary/10 text-primary',
    'inactive' => 'bg-slate-500/10 text-slate-300',
    'maintenance' => 'bg-secondary/10 text-secondary',
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
                        error: "#DC2626",
                        "surface-container": "#FFFFFF",
                        "surface-container-low": "#F1F5F9",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container-high": "#EEF2FF",
                        "surface-container-highest": "#E0E7FF",
                        "on-surface-variant": "#64748B",
                        outline: "#94A3B8",
                        "on-primary": "#FFFFFF",
                        "primary-container": "#DBEAFE",
                        "on-primary-container": "#1D4ED8"
                    },
                    borderRadius: {
                        DEFAULT: "0.125rem",
                        lg: "0.25rem",
                        xl: "0.5rem",
                        full: "0.75rem"
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
        .glass-overlay {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
        }
        body {
            font-family: 'Inter', sans-serif;
        }
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
                    <p class="font-medium text-on-surface-variant">Historical precision data for <?= htmlspecialchars($device['device_name']) ?><?= !empty($device['location']) ? ' in ' . htmlspecialchars($device['location']) : '' ?>.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex rounded-lg bg-surface-container-low p-1">
                        <button class="rounded-md bg-surface-container-high px-4 py-2 text-xs font-bold text-primary shadow-sm">Real-time</button>
                        <button class="px-4 py-2 text-xs font-bold text-on-surface-variant hover:text-on-surface">Historical</button>
                    </div>
                    <div class="flex items-center gap-3 rounded border border-outline-variant/15 bg-surface-container-lowest px-4 py-2 text-sm font-medium">
                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                        <span><?= htmlspecialchars($latest['recorded_at'] ? date('M d, Y', strtotime((string)$latest['recorded_at'])) : 'No data range') ?></span>
                        <span class="material-symbols-outlined text-sm opacity-60">expand_more</span>
                    </div>
                    <a class="flex items-center gap-2 rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary transition-all hover:brightness-110" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">
                        <span class="material-symbols-outlined text-sm">download</span>
                        <span>Export</span>
                    </a>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-lg border-l-4 border-primary bg-surface-container p-5">
                    <div class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Peak Consumption</div>
                    <div class="text-3xl font-black text-on-surface"><?= $format($peakConsumption, 2) ?> <span class="text-sm font-medium text-on-surface-variant">W</span></div>
                    <div class="mt-4 flex items-center gap-2 text-xs font-bold text-primary">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        <span><?= $format($avgPower, 2) ?> avg power</span>
                    </div>
                </div>
                <div class="rounded-lg border-l-4 border-secondary bg-surface-container p-5">
                    <div class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Grid Efficiency</div>
                    <div class="text-3xl font-black text-on-surface"><?= $format($gridEfficiency, 1) ?> <span class="text-sm font-medium text-on-surface-variant">%</span></div>
                    <div class="mt-4 flex items-center gap-2 text-xs font-bold text-secondary">
                        <span class="material-symbols-outlined text-xs">trending_down</span>
                        <span><?= $format(100 - $gridEfficiency, 1) ?>% threshold variance</span>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-lg bg-surface-container p-5 md:col-span-2">
                    <div class="flex items-center gap-6">
                        <div class="flex-1">
                            <div class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">System Health Index</div>
                            <div class="text-3xl font-black text-on-surface"><?= $healthIndex >= 75 ? 'Optimal' : ($healthIndex >= 45 ? 'Observe' : 'Critical') ?></div>
                            <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-surface-container-high">
                                <div class="h-full bg-primary" style="width: <?= max(8, min(100, $healthIndex)) ?>%"></div>
                            </div>
                        </div>
                        <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center opacity-30">
                            <span class="material-symbols-outlined text-7xl text-primary" style="font-variation-settings: 'FILL' 1;">security</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-lg bg-surface-container p-6 lg:col-span-2">
                    <div class="mb-8 flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold">Power Load Analysis</h3>
                            <p class="text-xs text-on-surface-variant">Kilowatt-hours per transmission interval</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="material-symbols-outlined rounded bg-surface-container-high p-2 text-sm">show_chart</button>
                            <button class="material-symbols-outlined rounded bg-surface-container-lowest p-2 text-sm opacity-50">bar_chart</button>
                        </div>
                    </div>
                    <div class="relative flex h-64 items-end gap-2 border-b border-outline-variant/20 px-2">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="h-32 w-full bg-gradient-to-t from-primary to-transparent opacity-10 blur-xl"></div>
                        </div>
                        <?php foreach ($chartHeights as $height): ?>
                            <div class="h-[40%] flex-1 rounded-t-sm bg-primary/20 transition-all hover:bg-primary/40" style="height: <?= (int)$height ?>%"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 flex justify-between text-[10px] font-bold uppercase tracking-tighter text-on-surface-variant">
                        <span>00:00</span><span>04:00</span><span>08:00</span><span>12:00</span><span>16:00</span><span>20:00</span><span>24:00</span>
                    </div>
                </div>

                <div class="rounded-lg bg-surface-container p-6">
                    <h3 class="mb-1 text-lg font-bold">Zone Distribution</h3>
                    <p class="mb-6 text-xs text-on-surface-variant">Energy allocation by device behavior profile</p>
                    <div class="relative mx-auto mb-8 h-48 w-48">
                        <div class="absolute inset-0 rounded-full border-[12px] border-surface-container-high"></div>
                        <div class="absolute inset-0 rotate-45 rounded-full border-[12px] border-primary border-b-transparent border-r-transparent"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black"><?= $industrialShare ?>%</span>
                            <span class="text-[10px] font-bold uppercase text-on-surface-variant">Industrial</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-primary"></span>
                                <span class="text-sm font-medium">Industrial Hub A</span>
                            </div>
                            <span class="text-sm font-bold"><?= $industrialShare ?>%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-secondary"></span>
                                <span class="text-sm font-medium">Data Centers</span>
                            </div>
                            <span class="text-sm font-bold"><?= $dataCenterShare ?>%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                                <span class="text-sm font-medium">Residential Grid</span>
                            </div>
                            <span class="text-sm font-bold"><?= $residentialShare ?>%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-outline"></span>
                                <span class="text-sm font-medium">Public Infra</span>
                            </div>
                            <span class="text-sm font-bold"><?= $publicShare ?>%</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-lg bg-surface-container">
                <div class="flex items-center justify-between border-b border-outline-variant/10 p-6">
                    <div>
                        <h3 class="text-lg font-bold">Node Performance Comparison</h3>
                        <p class="text-xs text-on-surface-variant">Comparing current period against derived baseline</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex items-center gap-2 rounded border border-outline-variant/20 bg-surface-container-high px-3 py-1.5 text-xs font-bold">
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
                        <tbody class="divide-y divide-outline-variant/10">
                            <?php foreach ($metrics as $row): ?>
                                <?php
                                $previous = (float)$row['previous_usage'];
                                $current = (float)$row['current_usage'];
                                $variance = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
                                $trendIcon = $variance > 1 ? 'trending_up' : ($variance < -1 ? 'trending_down' : 'trending_flat');
                                $trendClass = $variance > 1 ? 'text-orange-300' : ($variance < -1 ? 'text-primary' : 'text-on-surface-variant');
                                $status = $row['status'] ?? 'inactive';
                                ?>
                                <tr class="transition-colors hover:bg-surface-container-highest/30">
                                    <td class="px-6 py-4 text-sm font-bold"><?= htmlspecialchars($row['node_id']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase <?= htmlspecialchars($statusTone[$status] ?? 'bg-slate-500/10 text-slate-300') ?>">
                                            <?= htmlspecialchars($statusLabel[$status] ?? ucfirst($status)) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium"><?= $format($current, 1) ?> kW</td>
                                    <td class="px-6 py-4 text-sm opacity-60"><?= $format($previous, 1) ?> kW</td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold <?= $trendClass ?>"><?= ($variance >= 0 ? '+' : '') . $format($variance, 1) ?>%</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="material-symbols-outlined <?= $trendClass ?>"><?= $trendIcon ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-outline-variant/10 bg-surface-container-high p-6">
                    <h4 class="mb-4 flex items-center gap-2 font-bold">
                        <span class="material-symbols-outlined text-primary">compare_arrows</span>
                        Entity Comparison
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded bg-surface-container-lowest p-3">
                            <span class="text-sm"><?= htmlspecialchars($device['device_name']) ?></span>
                            <span class="material-symbols-outlined text-sm opacity-40">close</span>
                        </div>
                        <div class="flex items-center justify-between rounded bg-surface-container-lowest p-3">
                            <span class="text-sm"><?= htmlspecialchars($device['location'] ?? 'Main Facility') ?></span>
                            <span class="material-symbols-outlined text-sm opacity-40">close</span>
                        </div>
                        <button class="w-full rounded border-2 border-dashed border-outline-variant/30 py-3 text-xs font-bold uppercase tracking-widest text-on-surface-variant transition-all hover:border-primary/50">
                            + Add Entity to Compare
                        </button>
                    </div>
                </div>

                <div class="flex flex-col justify-between rounded-lg bg-surface-container-low p-6">
                    <div>
                        <h4 class="mb-2 font-bold">Automated Reporting</h4>
                        <p class="text-sm text-on-surface-variant">Schedule recurring data exports for stakeholder review.</p>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <button class="flex flex-col items-center justify-center gap-2 rounded bg-surface-container p-4 transition-all hover:bg-surface-container-high">
                            <span class="material-symbols-outlined text-primary">description</span>
                            <span class="text-[10px] font-bold uppercase">CSV</span>
                        </button>
                        <button class="flex flex-col items-center justify-center gap-2 rounded bg-surface-container p-4 transition-all hover:bg-surface-container-high">
                            <span class="material-symbols-outlined text-secondary">table_view</span>
                            <span class="text-[10px] font-bold uppercase">Excel</span>
                        </button>
                        <button class="flex flex-col items-center justify-center gap-2 rounded bg-surface-container p-4 transition-all hover:bg-surface-container-high">
                            <span class="material-symbols-outlined text-orange-300">picture_as_pdf</span>
                            <span class="text-[10px] font-bold uppercase">PDF</span>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <button class="fixed bottom-8 right-8 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-on-primary shadow-2xl transition-all hover:scale-110 active:scale-95">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
    </button>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
