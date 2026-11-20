<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartel Oficial de Entrada - GymSENA</title>
    <link rel="icon" type="image/svg+xml" href="/icons/logo-sena-oficial.svg">
    <link rel="stylesheet" href="/css/style.css?v=4">
    <link rel="stylesheet" href="/css/poster.css?v=2">
</head>
<body class="poster-body">

    <!-- Barra de Control (Oculta al imprimir) -->
    <nav class="poster-controls" aria-label="Controles del cartel">
        <a href="/monitor" class="poster-back-btn">
            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            <span>Volver al Panel</span>
        </a>
        <button type="button" class="poster-print-btn" onclick="window.print()">
            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            <span>Imprimir Cartel (A4 / Carta)</span>
        </button>
    </nav>

    <!-- Hoja de Cartel Institucional Físico -->
    <main class="poster-sheet">
        
        <!-- Encabezado Institucional SENA -->
        <header class="poster-header">
            <img src="/icons/logo-sena-oficial.svg" alt="Logotipo Oficial SENA" class="poster-logo">
            <div class="poster-header-text">
                <p class="poster-inst-title">Servicio Nacional de Aprendizaje &bull; SENA</p>
                <h1 class="poster-brand-title">Gym<span>SENA</span></h1>
                <span class="poster-venue-badge"><?= htmlspecialchars($gym['nombre'] ?? 'Gimnasio Institucional') ?></span>
            </div>
        </header>

        <!-- Llamado a la Acción Principal -->
        <div class="poster-main-action">
            <h2 class="poster-cta-title">REGISTRO DE ENTRADA Y SALIDA</h2>
            <p class="poster-cta-subtitle">Escanea con la cámara de tu teléfono móvil para marcar tu asistencia</p>
        </div>

        <!-- Código QR Único Central -->
        <div class="poster-qr-frame">
            <img src="/images/qr-entrada-oficial.svg" alt="Código QR Institucional de Entrada" class="poster-qr-img">
        </div>

        <div>
            <span class="poster-url-pill">
                <svg class="svg-icon" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                <?= htmlspecialchars($accessUrl) ?>
            </span>
        </div>

        <!-- Público Habilitado (Inclusivo y Profesional con Iconos SVG) -->
        <section class="poster-audience-strip" aria-label="Comunidad habilitada">
            <div class="poster-audience-item">
                <svg class="poster-audience-svg" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                <span>Aprendices</span>
            </div>
            <div class="poster-audience-item">
                <svg class="poster-audience-svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                <span>Instructores</span>
            </div>
            <div class="poster-audience-item">
                <svg class="poster-audience-svg" viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                <span>Funcionarios</span>
            </div>
            <div class="poster-audience-item">
                <svg class="poster-audience-svg" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                <span>Invitados</span>
            </div>
        </section>

        <!-- 3 Pasos Ergonómicos con Iconos SVG -->
        <section class="poster-steps-grid" aria-label="Pasos de registro">
            <div class="poster-step">
                <svg class="poster-step-icon" viewBox="0 0 24 24"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>
                <div class="poster-step-num">1</div>
                <div class="poster-step-text">Apunta con la cámara de tu celular o WhatsApp</div>
            </div>
            <div class="poster-step">
                <svg class="poster-step-icon" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                <div class="poster-step-num">2</div>
                <div class="poster-step-text">Ingresa tu documento o confirma en 1 toque</div>
            </div>
            <div class="poster-step">
                <svg class="poster-step-icon" viewBox="0 0 24 24"><path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.07 4.74 14.12 4 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
                <div class="poster-step-num">3</div>
                <div class="poster-step-text">Entrada o salida registrada en menos de 2s</div>
            </div>
        </section>

        <!-- Pie Institucional y Normativa -->
        <footer class="poster-footer">
            <strong>Bienestar al Aprendiz &bull; Actividad Física y Salud Integral</strong><br>
            Tratamiento de datos personales amparado por la Ley Estatutaria 1581 de 2012 (Habeas Data Colombia).<br>
            <em>En caso de fallas de cámara o batería, solicita tu registro asistido al monitor o entrenador de turno.</em>
        </footer>

    </main>

</body>
</html>
