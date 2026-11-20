<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#00324D">
    <title><?= htmlspecialchars($title ?? 'GymSENA - Gestión Profesional') ?></title>
    
    <link rel="icon" type="image/svg+xml" href="/images/logo-sena-oficial.svg">
    <link rel="stylesheet" href="/css/style.css?v=3">
</head>
<body class="dashboard-body">

    <!-- 1. BARRA LATERAL (ASIDE BAR) PROFESIONAL -->
    <aside class="sidebar" id="sidebar">
        <!-- Cabecera de la Barra Lateral con Logo Oficial SENA -->
        <div class="sidebar-header">
            <div class="sidebar-logo-box">
                <img src="/images/logo-sena-verde.svg" alt="SENA Logo Oficial" class="sidebar-sena-logo">
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-title">GymSENA</span>
                <span class="sidebar-brand-tag">Gestión Institucional</span>
            </div>
            <!-- Botón cerrar en móvil -->
            <button type="button" class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="Cerrar Menú">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>

        <!-- Perfil Breve del Entrenador / Funcionario -->
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar">
                <?= strtoupper(substr($user['nombre'] ?? $user['nombre_completo'] ?? 'E', 0, 1)) ?>
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?= htmlspecialchars($user['nombre'] ?? $user['nombre_completo'] ?? 'Funcionario') ?></span>
                <span class="sidebar-user-role"><?= ($user['rol'] ?? '') === 'ADMINISTRADOR' ? 'Instructor Encargado' : 'Pasante de Turno' ?></span>
            </div>
        </div>

        <!-- Módulos y Opciones del Menú (Navegación Ergonómica) -->
        <nav class="sidebar-nav">
            <span class="sidebar-section-title">Módulos de Operación</span>

            <?php if (($user['rol'] ?? '') === 'MONITOR'): ?>
                <a href="#modulo-aforo" class="sidebar-link active" onclick="activateSection('modulo-aforo', this)">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                    <span>Control de Sala en Vivo</span>
                </a>
                <a href="#modulo-qr" class="sidebar-link" onclick="activateSection('modulo-qr', this)">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 3h3v-3h-3v3zm3 3h3v-3h-3v3zm-3 0v-3h-3v3h3zm3-6h3V11h-3v3zm-6 0h3v-3h-3v3z"/></svg>
                    <span>Cartel QR de Entrada</span>
                </a>
            <?php else: ?>
                <a href="#modulo-metricas" class="sidebar-link active" onclick="activateSection('modulo-metricas', this)">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                    <span>Métricas y Horas Pico</span>
                </a>
                <a href="#modulo-pasantes" class="sidebar-link" onclick="activateSection('modulo-pasantes', this)">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    <span>Pasantes / Entrenadores</span>
                </a>
                <a href="#modulo-ajustes" class="sidebar-link" onclick="activateSection('modulo-ajustes', this)">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                    <span>Aforo y Carga Masiva</span>
                </a>
                <a href="/admin/exportar-csv" class="sidebar-link">
                    <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                    <span>Exportar a Excel</span>
                </a>
            <?php endif; ?>

            <span class="sidebar-section-title">Accesos Rápidos</span>
            <a href="/cartel-qr" target="_blank" class="sidebar-link">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                <span>Imprimir Cartel QR Entrada</span>
            </a>
            <a href="/acceso" target="_blank" class="sidebar-link">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
                <span>Vista Usuario (PWA)</span>
            </a>
        </nav>

        <!-- Pie de la Barra Lateral -->
        <div class="sidebar-footer">
            <a href="/logout" class="sidebar-logout-btn">
                <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <!-- Overlay oscuro en móvil -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- 2. ÁREA DE TRABAJO PRINCIPAL (MAIN WRAPPER) -->
    <div class="dashboard-main">
        <!-- Barra Superior del Dashboard -->
        <header class="dashboard-topbar">
            <div class="topbar-left">
                <button type="button" class="topbar-toggle-btn" onclick="toggleSidebar()" aria-label="Abrir Menú">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
                </button>
                <div class="topbar-title-block">
                    <span class="topbar-pretitle">Centro de Formación &bull; Bienestar al Aprendiz</span>
                    <h1 class="topbar-title"><?= htmlspecialchars($title ?? 'Panel de Control') ?></h1>
                </div>
            </div>

            <div class="topbar-right">
                <div class="live-clock-pill">
                    <span class="live-clock-time" id="dashboard-clock"><?= date('H:i') ?></span>
                    <span class="live-clock-zone">COL (GMT-5)</span>
                </div>
                <div class="topbar-aforo-indicator">
                    <span class="indicator-dot"></span>
                    <span>Aforo: <strong><?= $aforo['ocupacion_actual'] ?? 0 ?></strong> / <?= $aforo['capacidad_maxima'] ?? 30 ?></span>
                </div>
            </div>
        </header>

        <!-- Mensajes Flash / Alertas -->
        <div class="dashboard-content-area">
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
        </div>
    </div>

    <!-- Scripts de Interacción del Dashboard -->
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }
    }

    function activateSection(sectionId, linkEl) {
        document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
        if (linkEl) linkEl.classList.add('active');

        // Desplazamiento suave al módulo
        const target = document.getElementById(sectionId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Si está en móvil, cerrar sidebar al seleccionar
        if (window.innerWidth < 992) {
            toggleSidebar();
        }
    }

    // Reloj digital en vivo
    function updateClock() {
        const clockEl = document.getElementById('dashboard-clock');
        if (clockEl) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
    </script>
</body>
</html>

