<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$formatMoney = static fn ($value): string => number_format((float)$value, 2);
$formatEnergy = static fn ($value, int $decimals = 2): string => number_format((float)$value, $decimals);
$currentLabel = $currentUnbilled['billing_label'] ?? 'Current Period';
$lastLabel = $lastBilled['billing_label'] ?? 'Previous Period';
$peakEnergy = (float)($consumptionMix['peak_energy'] ?? 0);
$offPeakEnergy = (float)($consumptionMix['offpeak_energy'] ?? 0);
$suggestedSavings = ($peakEnergy * max(0, $peakRate - $offPeakRate)) * 0.18;
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
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
                        "surface-container-low": "#F1F5F9",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container": "#FFFFFF",
                        "surface-container-high": "#EEF2FF",
                        "surface-container-highest": "#E2E8F0",
                        "on-surface-variant": "#64748B",
                        primary: "#2563EB",
                        secondary: "#7C3AED",
                        outline: "#94A3B8",
                        error: "#DC2626",
                        "primary-container": "#DBEAFE",
                        "inverse-primary": "#1D4ED8",
                        "on-primary": "#FFFFFF",
                        "on-primary-fixed": "#FFFFFF"
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
            vertical-align: middle;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-surface text-on-surface">
    <?php
    $activePage = 'billing';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen px-6 pb-12 pt-24 lg:ml-64">
        <div class="mx-auto max-w-7xl space-y-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h1 class="mb-1 text-4xl font-extrabold tracking-tighter text-on-surface">Financial Intelligence</h1>
                    <p class="text-sm font-medium text-on-surface-variant">Billing Cycle: <?= htmlspecialchars($currentLabel) ?></p>
                </div>
                <div class="flex gap-3">
                    <button class="flex items-center gap-2 rounded-lg bg-surface-container-high px-4 py-2 text-sm font-semibold text-on-surface transition-all hover:bg-surface-container-highest">
                        <span class="material-symbols-outlined text-sm">calendar_month</span> Historical Data
                    </button>
                    <button class="rounded-lg bg-gradient-to-r from-primary to-secondary px-4 py-2 text-sm font-bold text-white transition-transform active:scale-95">
                        Pay Balance
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="rounded-xl border-l-4 border-primary bg-surface-container p-6 shadow-panel md:col-span-2">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Current Unbilled Amount</span>
                        <span class="material-symbols-outlined text-primary">pending_actions</span>
                    </div>
                    <div class="mt-4">
                        <h2 class="text-5xl font-black tracking-tighter text-primary">THB <?= $formatMoney($currentUnbilled['net_amount'] ?? 0) ?></h2>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs font-bold text-secondary"><?= ($periodChange >= 0 ? '+' : '') . number_format($periodChange, 1) ?>% vs last period</span>
                            <div class="h-1 w-24 overflow-hidden rounded-full bg-surface-container-highest">
                                <div class="h-full bg-secondary" style="width: <?= max(8, min(100, abs((int)round($periodChange * 4)))) ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-between rounded-xl bg-surface-container p-6 shadow-panel">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Last Month Total</span>
                        <span class="material-symbols-outlined text-on-surface-variant">event_available</span>
                    </div>
                    <div class="mt-4">
                        <h2 class="text-3xl font-bold tracking-tight text-on-surface">THB <?= $formatMoney($lastBilled['net_amount'] ?? 0) ?></h2>
                        <p class="mt-1 text-xs text-on-surface-variant"><?= htmlspecialchars($lastLabel) ?></p>
                    </div>
                </div>
                <div class="flex flex-col justify-between rounded-xl bg-surface-container p-6 shadow-panel">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Efficiency Rebate</span>
                        <span class="material-symbols-outlined text-secondary">bolt</span>
                    </div>
                    <div class="mt-4">
                        <h2 class="text-3xl font-bold tracking-tight text-secondary">-THB <?= $formatMoney($rebateAmount) ?></h2>
                        <p class="mt-1 text-xs text-on-surface-variant"><?= $rebateAmount > 0 ? 'High-efficiency credit applied' : 'No rebate earned this period' ?></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="h-fit rounded-xl bg-surface-container p-6 shadow-panel lg:col-span-1">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-bold tracking-tight text-on-surface">Tariff Configuration</h3>
                        <span class="material-symbols-outlined cursor-pointer text-on-surface-variant">edit</span>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded border border-outline-variant/30 bg-surface-container-low p-4">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Base Rate</label>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold">THB <?= $formatMoney($baseRate) ?> / kWh</span>
                                <span class="rounded bg-primary/10 px-2 py-1 text-[10px] font-bold text-primary">FIXED</span>
                            </div>
                        </div>
                        <div class="rounded border border-outline-variant/30 bg-surface-container-low p-4">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Fuel Transmission (FT)</label>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold">THB <?= $formatMoney($fuelRate) ?> / kWh</span>
                                <span class="rounded bg-secondary/10 px-2 py-1 text-[10px] font-bold text-secondary">VARIABLE</span>
                            </div>
                        </div>
                        <div class="rounded border-l-2 border-primary bg-surface-container-high p-4">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-primary">TOU - Peak (09:00 - 18:00)</label>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-on-surface">THB <?= $formatMoney($peakRate) ?> / kWh</span>
                                <span class="material-symbols-outlined text-sm text-primary">trending_up</span>
                            </div>
                        </div>
                        <div class="rounded border border-outline-variant/30 bg-surface-container-low p-4">
                            <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">TOU - Off-Peak</label>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold">THB <?= $formatMoney($offPeakRate) ?> / kWh</span>
                                <span class="material-symbols-outlined text-sm text-on-surface-variant">trending_down</span>
                            </div>
                        </div>
                    </div>
                    <button class="mt-6 w-full rounded-lg border border-outline-variant/60 py-3 text-sm font-semibold text-on-surface transition-all hover:bg-surface-container-highest">
                        Simulate New Tariffs
                    </button>
                </div>

                <div class="flex flex-col rounded-xl bg-surface-container p-6 shadow-panel lg:col-span-2">
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold tracking-tight text-on-surface">Spending Trends</h3>
                            <p class="text-xs text-on-surface-variant">Annualized cost forecast based on current energy usage</p>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex items-center gap-1.5 rounded bg-surface-container-high px-3 py-1 text-[10px] font-bold">
                                <span class="h-2 w-2 rounded-full bg-primary"></span> ACTUAL
                            </div>
                            <div class="flex items-center gap-1.5 rounded bg-surface-container-high px-3 py-1 text-[10px] font-bold">
                                <span class="h-2 w-2 rounded-full bg-outline"></span> FORECAST
                            </div>
                        </div>
                    </div>
                    <div class="relative flex min-h-[300px] flex-1 items-end gap-2">
                        <div class="pointer-events-none absolute inset-0 flex flex-col justify-between py-2 opacity-10">
                            <div class="w-full border-t border-on-surface"></div>
                            <div class="w-full border-t border-on-surface"></div>
                            <div class="w-full border-t border-on-surface"></div>
                            <div class="w-full border-t border-on-surface"></div>
                        </div>
                        <?php foreach ($trend as $point): ?>
                            <?php
                            $actualHeight = $maxTrendAmount > 0 ? max(12, (int)round(($point['amount'] / $maxTrendAmount) * 100)) : 12;
                            $forecastHeight = $point['forecast'] !== null && $maxTrendAmount > 0 ? max(12, (int)round(($point['forecast'] / $maxTrendAmount) * 100)) : null;
                            ?>
                            <div class="flex flex-1 items-end gap-1">
                                <div class="group relative w-full rounded-t-sm bg-gradient-to-t from-primary to-primary/15" style="height: <?= $actualHeight ?>%">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap rounded bg-surface-container-highest px-2 py-1 text-[10px] opacity-0 transition-opacity group-hover:opacity-100">THB <?= $formatMoney($point['amount']) ?></div>
                                </div>
                                <?php if ($forecastHeight !== null): ?>
                                    <div class="w-full rounded-t-sm border-2 border-dashed border-outline-variant/40" style="height: <?= $forecastHeight ?>%"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 flex justify-between px-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                        <?php foreach ($trend as $point): ?>
                            <span><?= htmlspecialchars($point['label']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                <div class="flex items-center justify-between border-b border-outline-variant/30 p-6">
                    <h3 class="text-lg font-bold tracking-tight text-on-surface">Billing History</h3>
                    <div class="relative">
                        <input class="w-64 rounded border border-outline-variant/40 bg-surface-container-lowest px-4 py-2 pr-10 text-xs focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Search invoices..." type="text"/>
                        <span class="material-symbols-outlined absolute right-3 top-2 text-sm text-on-surface-variant">search</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-[10px] font-black uppercase tracking-widest text-on-surface-variant">
                                <th class="px-6 py-4">Invoice ID</th>
                                <th class="px-6 py-4">Billing Period</th>
                                <th class="px-6 py-4">Consumption (MWh)</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            <?php if (empty($history)): ?>
                                <tr>
                                    <td class="px-6 py-5 text-sm text-on-surface-variant" colspan="6">No billing records available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($history as $invoice): ?>
                                    <tr class="group transition-colors hover:bg-surface-container-high">
                                        <td class="px-6 py-5 text-sm font-medium"><?= htmlspecialchars($invoice['invoice_id']) ?></td>
                                        <td class="px-6 py-5 text-sm text-on-surface-variant"><?= htmlspecialchars($invoice['billing_label']) ?></td>
                                        <td class="px-6 py-5 font-mono text-sm"><?= $formatEnergy($invoice['consumption_mwh'], 3) ?></td>
                                        <td class="px-6 py-5 text-sm font-bold text-on-surface">THB <?= $formatMoney($invoice['net_amount']) ?></td>
                                        <td class="px-6 py-5 text-sm">
                                            <span class="flex w-fit items-center gap-1 rounded px-2 py-1 text-[10px] font-bold <?= $invoice['status'] === 'PAID' ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700' ?>">
                                                <span class="h-1 w-1 rounded-full <?= $invoice['status'] === 'PAID' ? 'bg-emerald-600' : 'bg-violet-600' ?>"></span>
                                                <?= htmlspecialchars($invoice['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <a class="flex items-center justify-end gap-1 text-xs font-bold text-primary hover:underline" href="<?= htmlspecialchars(base_path_url('/devices')) ?>">
                                                View Invoice <span class="material-symbols-outlined text-sm">open_in_new</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-center bg-surface-container-low p-4">
                    <button class="flex items-center gap-2 text-xs font-bold text-on-surface-variant transition-colors hover:text-primary">
                        Load More Records <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <div class="fixed bottom-6 right-6 z-30">
        <div class="glass-panel max-w-xs rounded-xl border border-outline-variant/60 p-4 shadow-2xl">
            <div class="flex items-start gap-3">
                <div class="rounded bg-secondary/10 p-2">
                    <span class="material-symbols-outlined text-secondary">lightbulb</span>
                </div>
                <div>
                    <h4 class="mb-1 text-xs font-black uppercase tracking-tighter text-on-surface">Optimization Alert</h4>
                    <p class="text-[10px] leading-relaxed text-on-surface-variant">
                        Peak-hour usage is currently <?= $formatEnergy($peakEnergy, 2) ?> kWh. Shifting 18% of that load to off-peak hours could save an estimated <strong>THB <?= $formatMoney($suggestedSavings) ?></strong> on the next bill.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
