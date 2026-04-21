<?php
$displayName = trim((string)($user['name'] ?? 'User'));
$users = $users ?? [];
$profile = $profile ?? $user;
$initials = strtoupper(substr($displayName, 0, 1));
$roleTone = [
    'admin' => 'bg-violet-100 border border-violet-200 text-violet-700',
    'staff' => 'bg-slate-100 border border-slate-200 text-slate-700',
    'viewer' => 'bg-blue-50 border border-blue-200 text-blue-700',
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
                        outline: "#94A3B8",
                        error: "#DC2626",
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
                    },
                    boxShadow: {
                        panel: "0 20px 45px rgba(37, 99, 235, 0.08)"
                    }
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-surface text-on-surface selection:bg-primary selection:text-white">
    <?php
    $activePage = 'settings';
    require __DIR__ . '/../partials/app_header.php';
    require __DIR__ . '/../partials/app_sidebar.php';
    ?>

    <main class="min-h-screen bg-surface p-8 pt-24 lg:ml-64">
        <header class="mb-10">
            <h1 class="mb-2 text-4xl font-extrabold tracking-tight text-on-surface">System Orchestration</h1>
            <p class="max-w-2xl text-on-surface-variant">Configure global system behavior, manage administrative access, and fine-tune your monitoring environment.</p>
        </header>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-6 rounded-lg border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-6 rounded-lg border border-error/20 bg-rose-50 px-4 py-3 text-sm font-medium text-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-3">
                <div class="sticky top-24 rounded-xl bg-surface-container p-4 shadow-panel">
                    <div class="flex flex-col gap-2">
                        <button class="flex items-center gap-3 rounded-lg border-l-4 border-primary bg-surface-container-high px-4 py-3 text-left font-semibold text-primary" type="button">
                            <span class="material-symbols-outlined">person</span>
                            <span>Profile</span>
                        </button>
                        <button class="flex items-center gap-3 rounded-lg px-4 py-3 text-left text-on-surface-variant transition-colors hover:bg-surface-container-highest" type="button">
                            <span class="material-symbols-outlined">group</span>
                            <span>User Roles</span>
                        </button>
                        <button class="flex items-center gap-3 rounded-lg px-4 py-3 text-left text-on-surface-variant transition-colors hover:bg-surface-container-highest" type="button">
                            <span class="material-symbols-outlined">tune</span>
                            <span>System Settings</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-span-12 space-y-8 lg:col-span-9">
                <section class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                    <div class="border-b border-outline-variant/30 p-6">
                        <h3 class="text-xl font-bold">Personal Profile</h3>
                        <p class="text-sm text-on-surface-variant">Update your administrative credentials and security settings.</p>
                    </div>
                    <form method="POST" action="<?= htmlspecialchars(base_path_url('/profile/update')) ?>">
                        <div class="grid grid-cols-1 gap-8 p-8 md:grid-cols-2">
                            <div class="space-y-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Full Name</label>
                                    <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" name="name" type="text" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required/>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Email Address</label>
                                    <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50" name="email" type="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required/>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Role</label>
                                    <input class="rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-on-surface-variant focus:outline-none" type="text" value="<?= htmlspecialchars(ucfirst((string)($profile['role'] ?? 'viewer'))) ?>" disabled/>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Password Security</label>
                                    <a class="inline-flex items-center justify-between rounded-lg border border-outline-variant/20 bg-white px-4 py-3 text-sm text-on-surface transition-colors hover:bg-surface-container-highest" href="<?= htmlspecialchars(base_path_url('/profile/password')) ?>">
                                        <span>Change account password</span>
                                        <span class="material-symbols-outlined text-on-surface-variant">open_in_new</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 bg-surface-container-low p-6">
                            <a class="px-6 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:text-on-surface" href="<?= htmlspecialchars(base_path_url('/dashboard')) ?>">Discard</a>
                            <button class="rounded-lg bg-gradient-to-r from-primary to-secondary px-6 py-2 font-bold text-white shadow-panel transition-all hover:brightness-110 active:scale-95" type="submit">Save Changes</button>
                        </div>
                    </form>
                </section>

                <?php if ($isAdmin): ?>
                    <section class="overflow-hidden rounded-xl bg-surface-container shadow-panel">
                        <div class="flex items-center justify-between border-b border-outline-variant/30 p-6">
                            <div>
                                <h3 class="text-xl font-bold">User Access Control</h3>
                                <p class="text-sm text-on-surface-variant">Manage team permissions and regional access tokens.</p>
                            </div>
                            <a class="flex items-center gap-2 rounded-lg border border-outline-variant/20 bg-surface-container-high px-4 py-2 transition-colors hover:bg-surface-container-highest" href="<?= htmlspecialchars(base_path_url('/users/create')) ?>">
                                <span class="material-symbols-outlined text-secondary">person_add</span>
                                <span class="text-sm font-semibold">Invite User</span>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-surface-container-low/50 text-on-surface-variant">
                                        <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest">Operator</th>
                                        <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest">Assigned Role</th>
                                        <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest">Last Activity</th>
                                        <th class="px-8 py-4 text-right text-xs font-bold uppercase tracking-widest">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20">
                                    <?php foreach ($users as $item): ?>
                                        <tr class="transition-colors hover:bg-surface-container-low/70 <?= ((int)$item['id'] % 2 === 0) ? 'bg-surface-container-low/30' : '' ?>">
                                            <td class="px-8 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full <?= ($item['role'] ?? '') === 'admin' ? 'bg-primary-container text-primary' : 'bg-slate-200 text-slate-700' ?> text-xs font-bold">
                                                        <?= htmlspecialchars(strtoupper(substr((string)($item['name'] ?? 'U'), 0, 1) . substr((string)($item['email'] ?? 'X'), 0, 1))) ?>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium"><?= htmlspecialchars($item['name']) ?></div>
                                                        <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($item['email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-4">
                                                <span class="rounded px-2 py-1 text-[10px] font-bold uppercase <?= $roleTone[$item['role']] ?? 'bg-slate-100 border border-slate-200 text-slate-700' ?>">
                                                    <?= htmlspecialchars(ucfirst((string)$item['role'])) ?>
                                                </span>
                                            </td>
                                            <td class="px-8 py-4 text-sm text-on-surface-variant">
                                                <?= (int)$item['id'] === (int)$user['id'] ? 'Active now' : 'Managed account' ?>
                                            </td>
                                            <td class="px-8 py-4 text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    <a class="material-symbols-outlined text-on-surface-variant transition-colors hover:text-on-surface" href="<?= htmlspecialchars(base_path_url('/users/edit?id=' . (int)$item['id'])) ?>">edit</a>
                                                    <?php if ((int)$item['id'] !== (int)$user['id']): ?>
                                                        <form action="<?= htmlspecialchars(base_path_url('/users/delete')) ?>" method="POST" onsubmit="return confirm('Confirm delete this user?')" style="display:inline;">
                                                            <input name="id" type="hidden" value="<?= (int)$item['id'] ?>"/>
                                                            <button class="material-symbols-outlined text-on-surface-variant transition-colors hover:text-error" type="submit">delete</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <section class="rounded-xl bg-surface-container p-6 shadow-panel">
                        <div class="mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">notifications_active</span>
                            <h3 class="text-lg font-bold">Notification Channels</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between rounded-lg border border-outline-variant/20 bg-surface-container-low p-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-container text-primary">
                                        <span class="material-symbols-outlined text-sm">mail</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold">Email Alerts</div>
                                        <div class="text-xs text-on-surface-variant">Direct system logs</div>
                                    </div>
                                </div>
                                <div class="relative h-5 w-10 rounded-full bg-primary/20">
                                    <div class="absolute right-1 top-1 h-3 w-3 rounded-full bg-primary"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-outline-variant/20 bg-surface-container-low p-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700">
                                        <span class="material-symbols-outlined text-sm">chat</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold">LINE Messenger</div>
                                        <div class="text-xs text-on-surface-variant">Critical event push</div>
                                    </div>
                                </div>
                                <div class="relative h-5 w-10 rounded-full bg-slate-300">
                                    <div class="absolute left-1 top-1 h-3 w-3 rounded-full bg-slate-500"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-xl bg-surface-container p-6 shadow-panel">
                        <div class="mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">language</span>
                            <h3 class="text-lg font-bold">Localization</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Timezone</label>
                                <select class="appearance-none rounded-lg border border-outline-variant/20 bg-white px-4 py-2.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/50">
                                    <option>(UTC+07:00) Bangkok</option>
                                    <option>(UTC+00:00) Dublin, London</option>
                                    <option>(UTC-05:00) Eastern Time (US &amp; Canada)</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Measurement Units</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button class="rounded-lg border border-primary/30 bg-primary/10 px-4 py-2 text-sm font-bold text-primary" type="button">Metric (kWh)</button>
                                    <button class="rounded-lg border border-outline-variant/20 bg-white px-4 py-2 text-sm text-on-surface-variant transition-colors hover:bg-surface-container-highest" type="button">Imperial (BTU)</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/app_footer.php'; ?>
</body>
</html>
