<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(base_path_url('/assets/css/style.css')) ?>">
</head>
<body class="metricon-login-body">
    <div class="metricon-login-page">
        <div class="metricon-login-bg" aria-hidden="true">
            <div class="metricon-login-pattern"></div>
            <div class="metricon-login-orb metricon-login-orb-primary"></div>
            <div class="metricon-login-orb metricon-login-orb-secondary"></div>
        </div>

        <main class="metricon-login-wrap">
            <div class="metricon-login-brand">
                <h1>METRICON</h1>
                <p>GRID CONTROL &bull; PRECISION MONITORING</p>
            </div>

            <section class="metricon-login-panel">
                <div class="metricon-login-head">
                    <h1>System Access</h1>
                    <p>Enter your operator credentials to initialize session.</p>
                </div>

                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= htmlspecialchars(base_path_url('/login')) ?>" class="metricon-login-form">
                    <div class="metricon-field">
                        <label for="email">Email Address</label>
                        <div class="metricon-input-shell">
                            <span class="metricon-input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <path d="M3 8l9 6 9-6"></path>
                                </svg>
                            </span>
                            <input id="email" type="email" name="email" placeholder="operator@metricon.energy" required>
                        </div>
                    </div>

                    <div class="metricon-field">
                        <div class="metricon-field-top">
                            <label for="password">Access Code</label>
                            <a href="<?= htmlspecialchars(base_path_url('/forgot-password')) ?>" class="metricon-forgot-link">Forgot PIN?</a>
                        </div>
                        <div class="metricon-input-shell">
                            <span class="metricon-input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="11" width="16" height="9" rx="2"></rect>
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                                </svg>
                            </span>
                            <input id="password" type="password" name="password" placeholder="••••••••••••" required>
                        </div>
                    </div>

                    <label class="metricon-remember" for="remember">
                        <input id="remember" type="checkbox" name="remember">
                        <span>Maintain persistent session</span>
                    </label>

                    <button type="submit" class="metricon-primary-btn">Authorize Access</button>
                </form>

                <div class="metricon-protocol-divider">
                    <span>Secondary Protocols</span>
                </div>

                <div class="metricon-protocol-grid">
                    <a href="<?= htmlspecialchars(base_path_url('/register')) ?>" class="metricon-protocol-card">
                        <span class="metricon-protocol-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"></path>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.14-4.53z" fill="#EA4335"></path>
                            </svg>
                        </span>
                        <span>Google</span>
                    </a>
                    <a href="<?= htmlspecialchars(base_path_url('/forgot-password')) ?>" class="metricon-protocol-card">
                        <span class="metricon-protocol-icon metricon-protocol-icon-sso" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                <path d="M8 20h8"></path>
                                <path d="M12 16v4"></path>
                            </svg>
                        </span>
                        <span>SSO</span>
                    </a>
                </div>
            </section>

            <p class="metricon-login-note">
                Protected by Metricon Multi-Factor Security Protocol.<br>
                Unauthorized access attempts are monitored and recorded.
            </p>
        </main>
    </div>
</body>
</html>
