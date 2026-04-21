<?php
$activePage = $activePage ?? '';
$displayName = trim((string)($displayName ?? ($user['name'] ?? 'User')));
$initial = strtoupper(substr($displayName, 0, 1));
$topNavItems = [
    'dashboard' => ['label' => 'Dashboard', 'href' => base_path_url('/dashboard')],
    'devices' => ['label' => 'Devices', 'href' => base_path_url('/devices')],
    'analytics' => ['label' => 'Analytics', 'href' => base_path_url('/analytics')],
    'billing' => ['label' => 'Billing', 'href' => base_path_url('/billing')],
];
?>
<header class="fixed top-0 z-50 flex h-16 w-full items-center justify-between border-b border-outline-variant bg-white/95 px-6 backdrop-blur">
    <div class="flex items-center gap-8">
        <span class="text-xl font-black tracking-tighter text-primary">SMART METER</span>
        <nav class="hidden h-full items-center gap-6 md:flex">
            <?php foreach ($topNavItems as $key => $item): ?>
                <a
                    class="<?= $activePage === $key ? 'flex h-full items-center border-b-2 border-primary px-2 font-bold text-primary' : 'font-medium tracking-tight text-on-surface-variant transition-colors duration-200 hover:text-on-surface' ?>"
                    href="<?= htmlspecialchars($item['href']) ?>"
                >
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="flex items-center gap-4">
        <a class="material-symbols-outlined rounded p-2 text-on-surface-variant transition-all duration-200 hover:bg-slate-100" href="<?= htmlspecialchars(base_path_url('/alerts')) ?>">notifications</a>
        <a class="material-symbols-outlined rounded p-2 text-on-surface-variant transition-all duration-200 hover:bg-slate-100" href="<?= htmlspecialchars(base_path_url('/settings')) ?>">settings</a>
        <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-surface-container-high text-xs font-bold text-primary ring-2 ring-primary/15">
            <?= htmlspecialchars($initial) ?>
        </div>
    </div>
</header>
