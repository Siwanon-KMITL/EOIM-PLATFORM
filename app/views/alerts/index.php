<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$criticalCount = (int)($severityCounts['critical'] ?? 0);
$warningCount = (int)(($severityCounts['high'] ?? 0) + ($severityCounts['medium'] ?? 0));
$infoCount = (int)($severityCounts['low'] ?? 0);
$latestIncidentLabel = $latestAlertAt ? date('M d, Y H:i', strtotime((string)$latestAlertAt)) : 'No recent incident';
$maxFrequency = 1;
foreach ($frequency as $point) {
    $maxFrequency = max($maxFrequency, (int)($point['total_alerts'] ?? 0));
}
$severityTone = [
    'critical' => 'bg-rose-100 text-rose-700',
    'high' => 'bg-violet-100 text-violet-700',
    'medium' => 'bg-amber-100 text-amber-700',
    'low' => 'bg-blue-50 text-blue-700',
];
$severityLabel = [
    'critical' => 'Critical',
    'high' => 'Warning',
    'medium' => 'Warning',
    'low' => 'Information',
];
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F8FAFC; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <?php
    $activePage = 'alerts';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="px-8 pb-12 pt-24 lg:ml-64">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h1 class="mb-2 text-4xl font-extrabold tracking-tight text-on-surface">System Alerts</h1>
                    <p class="font-medium text-on-surface-variant">Real-time infrastructure health and anomaly detection</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button class="flex items-center gap-2 rounded border border-outline-variant bg-surface-container px-4 py-2 text-sm transition-colors hover:bg-surface-container-high" type="button">
                        <span class="material-symbols-outlined text-lg text-primary">filter_list</span>
                        <span>Severity: All</span>
                    </button>
                    <button class="flex items-center gap-2 rounded border border-outline-variant bg-surface-container px-4 py-2 text-sm transition-colors hover:bg-surface-container-high" type="button">
                        <span class="material-symbols-outlined text-lg text-secondary">bolt</span>
                        <span>Voltage Drop</span>
                    </button>
                    <button class="flex items-center gap-2 rounded border border-outline-variant bg-surface-container px-4 py-2 text-sm transition-colors hover:bg-surface-container-high" type="button">
                        <span class="material-symbols-outlined text-lg text-error">dangerous</span>
                        <span>Overload</span>
                    </button>
                    <button class="flex items-center gap-2 rounded border border-outline-variant bg-surface-container px-4 py-2 text-sm transition-colors hover:bg-surface-container-high" type="button">
                        <span class="material-symbols-outlined text-lg text-on-surface-variant">cloud_off</span>
                        <span>Offline</span>
                    </button>
                </div>
            </div>

            <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="relative overflow-hidden rounded-xl bg-surface-container p-6 shadow-panel">
                    <div class="absolute right-0 top-0 h-full w-1.5 bg-error"></div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Critical</p>
                    <h3 class="text-3xl font-black text-error"><?= $criticalCount ?></h3>
                    <p class="mt-2 text-[10px] text-on-surface-variant">REQUIRES IMMEDIATE ACTION</p>
                </div>
                <div class="relative overflow-hidden rounded-xl bg-surface-container p-6 shadow-panel">
                    <div class="absolute right-0 top-0 h-full w-1.5 bg-secondary"></div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Warning</p>
                    <h3 class="text-3xl font-black text-secondary"><?= $warningCount ?></h3>
                    <p class="mt-2 text-[10px] text-on-surface-variant">SYSTEM AT THRESHOLD</p>
                </div>
                <div class="relative overflow-hidden rounded-xl bg-surface-container p-6 shadow-panel">
                    <div class="absolute right-0 top-0 h-full w-1.5 bg-primary"></div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Info</p>
                    <h3 class="text-3xl font-black text-primary"><?= $infoCount ?></h3>
                    <p class="mt-2 text-[10px] text-on-surface-variant">ROUTINE OPERATIONS</p>
                </div>
                <div class="relative overflow-hidden rounded-xl bg-surface-container p-6 shadow-panel">
                    <div class="absolute right-0 top-0 h-full w-1.5 bg-slate-300"></div>
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Historical</p>
                    <h3 class="text-3xl font-black text-on-surface"><?= (int)$historicalCount ?></h3>
                    <p class="mt-2 text-[10px] text-on-surface-variant">TOTAL LOGGED EVENTS</p>
                </div>
            </div>

            <div class="flex flex-col overflow-hidden rounded-xl bg-surface-container shadow-panel">
                <div class="grid grid-cols-12 gap-4 border-b border-outline-variant/50 bg-surface-container-low px-6 py-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/80">
                    <div class="col-span-1">Status</div>
                    <div class="col-span-2">Severity</div>
                    <div class="col-span-5">Incident Description</div>
                    <div class="col-span-2">Timestamp</div>
                    <div class="col-span-2 text-right">Action</div>
                </div>
                <?php if (empty($alerts)): ?>
                    <div class="px-6 py-8 text-sm text-on-surface-variant">No alerts available in the system.</div>
                <?php else: ?>
                    <?php foreach ($alerts as $alert): ?>
                        <?php
                        $severity = $alert['severity'] ?? 'low';
                        $isCritical = $severity === 'critical';
                        $severityClass = $severityTone[$severity] ?? 'bg-blue-50 text-blue-700';
                        $severityText = $severityLabel[$severity] ?? ucfirst((string)$severity);
                        $typeText = trim((string)($alert['alert_type'] ?? 'System Event'));
                        $messageText = trim((string)($alert['message'] ?? ''));
                        $statusText = $isCritical ? 'animate-pulse bg-error' : ($severity === 'low' ? 'bg-primary' : 'bg-secondary');
                        $actionLabel = $severity === 'low' ? 'Acknowledge' : 'Review';
                        ?>
                        <div class="group grid cursor-pointer grid-cols-12 items-center gap-4 border-b border-outline-variant/20 px-6 py-5 transition-colors hover:bg-surface-container-low">
                            <div class="col-span-1 flex items-center">
                                <span class="flex h-3 w-3 rounded-full <?= $statusText ?>"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="inline-flex rounded px-2 py-1 text-[10px] font-bold uppercase leading-none <?= $severityClass ?>"><?= htmlspecialchars($severityText) ?></span>
                            </div>
                            <div class="col-span-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($typeText) ?><?= !empty($alert['device_name']) ? ' - ' . htmlspecialchars($alert['device_name']) : '' ?></span>
                                    <span class="mt-1 text-xs text-on-surface-variant">
                                        <?= htmlspecialchars($messageText !== '' ? $messageText : 'No incident details available.') ?>
                                        <?php if (!empty($alert['location'])): ?>
                                            <?= ' Location: ' . htmlspecialchars($alert['location']) . '.' ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-span-2 text-xs font-mono text-on-surface-variant"><?= htmlspecialchars((string)($alert['created_at'] ?? '-')) ?></div>
                            <div class="col-span-2 text-right opacity-0 transition-opacity group-hover:opacity-100">
                                <?php if (!empty($alert['device_id'])): ?>
                                    <a class="text-[10px] font-bold uppercase text-primary hover:underline" href="<?= htmlspecialchars(base_path_url('/monitoring/device?id=' . (int)$alert['device_id'])) ?>"><?= htmlspecialchars($actionLabel) ?></a>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold uppercase text-on-surface-variant">Logged</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-xl border border-outline-variant/40 bg-surface-container-low p-5 shadow-panel">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-xs font-black uppercase tracking-widest text-on-surface-variant">Incident Frequency</h4>
                        <span class="text-[10px] <?= $frequencyChange >= 0 ? 'text-error' : 'text-primary' ?>">
                            <?= ($frequencyChange >= 0 ? '+' : '') . number_format($frequencyChange, 1) ?>% vs previous period
                        </span>
                    </div>
                    <div class="flex h-16 items-end gap-1">
                        <?php if (empty($frequency)): ?>
                            <div class="h-1/3 flex-1 rounded-t-sm bg-slate-300"></div>
                        <?php else: ?>
                            <?php foreach ($frequency as $point): ?>
                                <?php
                                $height = max(20, (int)round((((int)$point['total_alerts']) / $maxFrequency) * 100));
                                $barClass = ((int)$point['total_alerts']) === $maxFrequency ? 'bg-error shadow-[0_0_10px_rgba(220,38,38,0.18)]' : 'bg-slate-300';
                                ?>
                                <div class="flex-1 rounded-t-sm <?= $barClass ?>" style="height: <?= $height ?>%"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl border border-outline-variant/40 bg-surface-container-low p-5 shadow-panel md:col-span-2">
                    <div class="flex gap-8">
                        <div>
                            <p class="mb-1 text-[10px] font-bold uppercase text-on-surface-variant">Latest Incident</p>
                            <h5 class="text-xl font-black text-on-surface"><?= htmlspecialchars($latestIncidentLabel) ?></h5>
                        </div>
                        <div>
                            <p class="mb-1 text-[10px] font-bold uppercase text-on-surface-variant">Active Alerts</p>
                            <h5 class="text-xl font-black text-on-surface"><?= (int)$activeAlerts ?></h5>
                        </div>
                        <div>
                            <p class="mb-1 text-[10px] font-bold uppercase text-on-surface-variant">Impacted Devices</p>
                            <h5 class="text-xl font-black text-on-surface"><?= (int)$impactedDevices ?></h5>
                        </div>
                    </div>
                    <div class="text-right">
                        <a class="rounded-lg bg-white px-6 py-2 text-xs font-bold uppercase tracking-widest transition-all active:scale-95 hover:bg-surface-container-high" href="<?= htmlspecialchars(base_path_url('/analytics')) ?>">
                            System Diagnostics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
