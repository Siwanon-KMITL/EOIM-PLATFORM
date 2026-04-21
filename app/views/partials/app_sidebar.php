<?php
$activePage = $activePage ?? '';
$sidebarTitle = $sidebarTitle ?? 'Grid Control';
$sidebarSubtitle = $sidebarSubtitle ?? 'Precision Monitoring';
$sidebarActionHref = $sidebarActionHref ?? base_path_url('/analytics');
$sidebarActionLabel = $sidebarActionLabel ?? 'Export Report';
$sidebarActionIcon = $sidebarActionIcon ?? 'file_download';
$sidebarItems = [
    'dashboard' => ['label' => 'Dashboard', 'href' => base_path_url('/dashboard'), 'icon' => 'dashboard'],
    'devices' => ['label' => 'Devices', 'href' => base_path_url('/devices'), 'icon' => 'memory'],
    'analytics' => ['label' => 'Analytics', 'href' => base_path_url('/analytics'), 'icon' => 'insights'],
    'billing' => ['label' => 'Billing', 'href' => base_path_url('/billing'), 'icon' => 'receipt_long'],
    'alerts' => ['label' => 'Alerts', 'href' => base_path_url('/alerts'), 'icon' => 'warning'],
    'settings' => ['label' => 'Settings', 'href' => base_path_url('/settings'), 'icon' => 'settings'],
];
?>
<aside class="fixed left-0 top-0 z-40 hidden h-full w-64 flex-col gap-4 border-r border-outline-variant bg-white pb-8 pt-20 lg:flex">
    <div class="mb-6 px-6">
        <h2 class="text-lg font-bold text-on-surface"><?= htmlspecialchars($sidebarTitle) ?></h2>
        <p class="text-xs tracking-wide text-on-surface-variant"><?= htmlspecialchars($sidebarSubtitle) ?></p>
    </div>
    <nav class="flex flex-1 flex-col gap-1 px-3">
        <?php foreach ($sidebarItems as $key => $item): ?>
            <a
                class="<?= $activePage === $key ? 'flex items-center gap-3 rounded-r-lg border-l-4 border-primary bg-blue-50 px-3 py-3 font-semibold text-primary' : 'flex items-center gap-3 rounded-lg px-3 py-3 text-on-surface-variant transition-all hover:bg-slate-100 hover:text-on-surface' ?>"
                href="<?= htmlspecialchars($item['href']) ?>"
            >
                <span class="material-symbols-outlined"><?= htmlspecialchars($item['icon']) ?></span>
                <span class="text-sm tracking-wide"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
        <?php if (function_exists('has_role') && has_role('admin')): ?>
            <a class="flex items-center gap-3 rounded-lg px-3 py-3 text-on-surface-variant transition-all hover:bg-slate-100 hover:text-on-surface" href="<?= htmlspecialchars(base_path_url('/users')) ?>">
                <span class="material-symbols-outlined">group</span>
                <span class="text-sm tracking-wide">Users</span>
            </a>
        <?php endif; ?>
    </nav>
    <div class="px-6 pb-4">
        <a class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-primary to-secondary py-2 text-xs font-bold text-white shadow-panel transition-all hover:brightness-110 active:scale-95" href="<?= htmlspecialchars($sidebarActionHref) ?>">
            <span class="material-symbols-outlined text-sm"><?= htmlspecialchars($sidebarActionIcon) ?></span>
            <span><?= htmlspecialchars($sidebarActionLabel) ?></span>
        </a>
    </div>
</aside>
