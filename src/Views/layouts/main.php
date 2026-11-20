<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="theme-color" content="#39A900">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GymSENA">
    <meta name="description" content="Sistema PWA de Control de Acceso y Servicios para Aprendices del Gimnasio SENA">
    
    <title><?= htmlspecialchars($title ?? 'GymSENA - Gimnasio Institucional') ?></title>
    
    <!-- Manifest PWA -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/images/logo-sena-oficial.svg">
    <link rel="apple-touch-icon" href="/images/logo-sena-oficial.svg">

    <!-- Estilos Mobile-First & Human-Centered UX SENA -->
    <link rel="stylesheet" href="/css/style.css?v=3">
</head>
<body>
    <!-- 1. Header Institucional (Nielsen Heuristic 4: Consistencia y Estándares) -->
    <header class="navbar">
        <div class="nav-container">
            <a href="/acceso" class="nav-brand" title="GymSENA Inicio">
                <div class="sena-logo-box">
                    <img src="/images/logo-sena-oficial.svg" alt="SENA Logo Oficial" class="sena-logo-img">
                </div>
                <div class="brand-text">
                    <span class="brand-title">
                        <span class="brand-title-gym">Gym</span><span class="brand-title-sena">SENA</span>
                    </span>
                    <span class="brand-sub">Bienestar al Aprendiz</span>
                </div>
            </a>

            <!-- Navegación de Escritorio -->
            <nav class="desktop-nav">
                <?php 
                $currUser = \GymSena\Core\Auth::getUser();
                $currLearner = \GymSena\Core\Auth::getLearner();
                $currentUri = $_SERVER['REQUEST_URI'] ?? '';
                ?>

                <a href="/acceso" class="nav-link-btn <?= str_starts_with($currentUri, '/acceso') ? 'active' : '' ?>">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 3h3v-3h-3v3zm3 3h3v-3h-3v3zm-3 0v-3h-3v3h3zm3-6h3V11h-3v3zm-6 0h3v-3h-3v3z"/></svg>
                    <span>Acceso QR</span>
                </a>

                <?php if ($currLearner): ?>
                    <a href="/historial" class="nav-link-btn <?= str_starts_with($currentUri, '/historial') ? 'active' : '' ?>">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        <span>Mi Historial</span>
                    </a>
                    <a href="/carne" class="nav-link-btn <?= str_starts_with($currentUri, '/carne') ? 'active' : '' ?>">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                        <span>Carné Digital</span>
                    </a>
                    <a href="/logout-aprendiz" class="nav-link-btn" title="Cerrar sesión de aprendiz">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                        <span>Salir (<?= htmlspecialchars(explode(' ', $currLearner['nombre'])[0]) ?>)</span>
                    </a>
                <?php endif; ?>

                <?php if ($currUser): ?>
                    <?php if ($currUser['rol'] === 'ADMINISTRADOR'): ?>
                        <a href="/admin" class="nav-link-btn active">
                            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                            <span>Admin Bienestar</span>
                        </a>
                    <?php else: ?>
                        <a href="/monitor" class="nav-link-btn active">
                            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <span>Panel Entrenador</span>
                        </a>
                    <?php endif; ?>
                    <a href="/logout" class="nav-link-btn nav-logout-link">Cerrar Sesión</a>
                <?php elseif (!$currLearner): ?>
                    <a href="/login" class="nav-link-btn">
                        <span class="nav-role-badge">Soy Entrenador / Admin</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- 2. Contenido Principal -->
    <main class="main-content">
        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-info">
                <svg class="ui-icon alert-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <span><?= htmlspecialchars($_GET['msg']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger">
                <svg class="ui-icon alert-icon" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <!-- 3. Barra Ergonómica Inferior para Celulares (Ley de Fitts: Zona Cómoda del Pulgar) -->
    <nav class="mobile-bottom-nav">
        <a href="/acceso" class="mob-nav-item <?= str_starts_with($currentUri, '/acceso') || $currentUri === '/' ? 'active' : '' ?>">
            <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 3h3v-3h-3v3zm3 3h3v-3h-3v3zm-3 0v-3h-3v3h3zm3-6h3V11h-3v3zm-6 0h3v-3h-3v3z"/></svg>
            <span>Acceso QR</span>
        </a>

        <?php if ($currLearner): ?>
            <a href="/historial" class="mob-nav-item <?= str_starts_with($currentUri, '/historial') ? 'active' : '' ?>">
                <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                <span>Historial</span>
            </a>
            <a href="/carne" class="mob-nav-item <?= str_starts_with($currentUri, '/carne') ? 'active' : '' ?>">
                <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                <span>Carné</span>
            </a>
            <a href="/logout-aprendiz" class="mob-nav-item">
                <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                <span>Salir</span>
            </a>
        <?php elseif ($currUser): ?>
            <?php if ($currUser['rol'] === 'ADMINISTRADOR'): ?>
                <a href="/admin" class="mob-nav-item active">
                    <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                    <span>Admin</span>
                </a>
            <?php else: ?>
                <a href="/monitor" class="mob-nav-item active">
                    <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    <span>Monitor</span>
                </a>
            <?php endif; ?>
            <a href="/logout" class="mob-nav-item">
                <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                <span>Salir</span>
            </a>
        <?php else: ?>
            <a href="/login" class="mob-nav-item <?= str_starts_with($currentUri, '/login') ? 'active' : '' ?>">
                <svg class="mob-nav-svg" viewBox="0 0 24 24"><path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
                <span>Gestión</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- 4. Footer Institucional -->
    <footer class="footer-wrap">
        <div class="footer-container">
            <p class="footer-brand">GymSENA &bull; Sistema de Control y Bienestar Físico</p>
            <p>Servicio Nacional de Aprendizaje - SENA &bull; Regional y Centro de Formación</p>
            <p class="footer-legal-text">
                Cumplimiento normativo Ley Estatutaria 1581 de 2012 (Habeas Data Colombia)
            </p>
            <div id="pwa-install-banner" class="hidden">
                <button id="btn-install-pwa" class="pwa-badge-btn">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/></svg>
                    <span>Instalar GymSENA en mi celular</span>
                </button>
            </div>
        </div>
    </footer>

    <!-- Scripts PWA y Live Polling -->
    <script src="/js/app.js"></script>
</body>
</html>
