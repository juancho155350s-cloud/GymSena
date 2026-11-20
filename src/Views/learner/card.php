<?php
$tipoUsuario = $learner['tipo_usuario'] ?? 'APRENDIZ';
$catBadge = match($tipoUsuario) {
    'INSTRUCTOR' => 'Instructor SENA',
    'FUNCIONARIO' => 'Funcionario SENA',
    'INVITADO' => 'Invitado / Visitante',
    default => 'Aprendiz SENA'
};

$roleThemeClass = match($tipoUsuario) {
    'INSTRUCTOR' => 'role-theme-instructor',
    'FUNCIONARIO' => 'role-theme-funcionario',
    'INVITADO' => 'role-theme-invitado',
    default => 'role-theme-aprendiz'
};

$nombreCompleto = trim(($learner['nombres'] ?? '') . ' ' . ($learner['apellidos'] ?? ''));
$fechaExpedicion = date('d/m/Y');
?>

<div class="carnet-view-wrapper">

    <!-- Acciones Superiores de Control -->
    <div class="carnet-top-actions no-print">
        <a href="/historial" class="btn-subtle-back">
            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            <span>Mi Historial</span>
        </a>
        <div class="carnet-action-buttons">
            <button type="button" class="btn-action-print" onclick="window.print()">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                <span>Imprimir Carné</span>
            </button>
            <a href="/acceso" class="btn-action-access">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 3h3v-3h-3v3zm3 3h3v-3h-3v3zm-3 0v-3h-3v3h3zm3-6h3V11h-3v3zm-6 0h3v-3h-3v3z"/></svg>
                <span>Punto de Acceso</span>
            </a>
        </div>
    </div>

    <!-- Contenedor del Carné Virtual de Alta Seguridad ISO ID-1 -->
    <div class="sena-id-card-frame">
        <div class="sena-id-card <?= $roleThemeClass ?>" id="printableSenaCard">
            
            <!-- Marca de Agua Geométrica Institucional de Fondo -->
            <div class="card-security-pattern"></div>

            <!-- Cabecera Institucional Oficial -->
            <div class="card-header-institutional">
                <div class="card-header-logo-row">
                    <img src="/images/logo-sena-blanco.svg" alt="SENA" class="card-header-logo">
                    <div class="card-header-text">
                        <span class="card-country-label">REPÚBLICA DE COLOMBIA</span>
                        <h4 class="card-sena-entity-title">SERVICIO NACIONAL DE APRENDIZAJE</h4>
                        <span class="card-gym-facility-label">BIENESTAR AL APRENDIZ &bull; GIMNASIO INSTITUCIONAL</span>
                    </div>
                </div>

                <!-- Chip Holográfico de Verificación -->
                <div class="card-security-chip-box" title="Acreditación Digital Segura">
                    <svg class="security-chip-svg" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="3" fill="#ffffff" fill-opacity="0.15" stroke="#ffffff" stroke-width="1.2"/>
                        <circle cx="12" cy="12" r="3.5" fill="none" stroke="#ffffff" stroke-width="1.2"/>
                        <path d="M2 9h4M2 15h4M18 9h4M18 15h4M8 4v4M16 4v4M8 16v4M16 16v4" stroke="#ffffff" stroke-width="1.2"/>
                    </svg>
                    <span class="chip-text-label">OFICIAL</span>
                </div>
            </div>

            <!-- Línea de Acento Metálica -->
            <div class="card-metallic-accent-line"></div>

            <!-- Cuerpo Principal del Carné -->
            <div class="card-body-content">

                <!-- Columna Izquierda: Perfil, Avatar y Metadatos -->
                <div class="card-profile-section">
                    <div class="card-avatar-ring">
                        <div class="card-avatar-initials">
                            <?= strtoupper(substr($learner['nombres'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="card-status-pulsar-dot" title="Usuario Habilitado"></span>
                    </div>

                    <div class="card-user-identification">
                        <span class="card-category-badge <?= $roleThemeClass ?>">
                            <?= htmlspecialchars($catBadge) ?>
                        </span>
                        <h3 class="card-user-full-name">
                            <?= htmlspecialchars($nombreCompleto) ?>
                        </h3>
                        <div class="card-document-number">
                            <span class="doc-type-pill"><?= htmlspecialchars($learner['tipo_documento'] ?? 'CC') ?></span>
                            <span class="doc-num-val"><?= htmlspecialchars($learner['documento'] ?? '') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Rejilla de Información Académica e Institucional -->
                <div class="card-meta-details-grid">
                    <?php if ($tipoUsuario === 'APRENDIZ'): ?>
                        <div class="card-meta-field">
                            <span class="meta-field-label">Ficha de Caracterización</span>
                            <span class="meta-field-value text-accent-green"><?= htmlspecialchars($learner['ficha'] ?? 'N/A') ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="card-meta-field">
                        <span class="meta-field-label"><?= $tipoUsuario === 'APRENDIZ' ? 'Programa de Formación' : ($tipoUsuario === 'INSTRUCTOR' ? 'Área de Conocimiento' : 'Dependencia / Entidad') ?></span>
                        <span class="meta-field-value"><?= htmlspecialchars($learner['programa_formacion'] ?? 'SENA') ?></span>
                    </div>

                    <div class="card-meta-field">
                        <span class="meta-field-label">Estado en Sistema</span>
                        <span class="meta-field-value card-status-approved">
                            <svg class="ui-icon-xs" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            AUTORIZADO PARA ENTRENAMIENTO
                        </span>
                    </div>

                    <?php if (!empty($learner['contacto_emergencia_nombre'])): ?>
                        <div class="card-meta-field card-emergency-field">
                            <span class="meta-field-label">Contacto en Caso de Emergencia</span>
                            <span class="meta-field-value"><?= htmlspecialchars($learner['contacto_emergencia_nombre']) ?> &bull; <?= htmlspecialchars($learner['contacto_emergencia_telefono']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Módulo QR de Validación Presencial 100% Vectorial Offline -->
                <div class="card-qr-validation-module">
                    <div class="card-qr-frame">
                        <img src="<?= htmlspecialchars($qrUrl) ?>" alt="Código QR Institucional" class="card-qr-vector-img">
                    </div>
                    <div class="card-qr-meta">
                        <span class="qr-verify-caption">CÓDIGO DIGITAL ÚNICO</span>
                        <code class="qr-token-hash"><?= htmlspecialchars(substr($learner['token_acceso'] ?? md5($learner['documento']), 0, 14)) ?></code>
                        <span class="qr-instructions">Escanear para verificar aforo o control manual</span>
                    </div>
                </div>

            </div>

            <!-- Pie del Carné de Acceso -->
            <div class="card-footer-strip">
                <div class="footer-strip-left">
                    <span>GymSENA &bull; Sistema Integrado de Control Deportivo</span>
                </div>
                <div class="footer-strip-right">
                    <span>Expedición: <?= $fechaExpedicion ?> &bull; Ley 1581 de 2012</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Tarjeta Informativa de Uso Responsable -->
    <div class="carnet-guide-card no-print">
        <div class="guide-card-icon">
            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>
        <div class="guide-card-body">
            <h4>Uso de tu Carné Digital</h4>
            <p>Este carné es personal e intransferible. Al ingresar al gimnasio puedes presentar este código QR en pantalla o simplemente registrar tu entrada con 1 solo toque desde tu celular al escanear el cartel de la entrada.</p>
        </div>
    </div>

</div>
