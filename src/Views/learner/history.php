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

$nombreCompleto = trim(($learner['nombres'] ?? $learner['nombre'] ?? 'Aprendiz') . ' ' . ($learner['apellidos'] ?? ''));
$totalSesiones = $stats['total_sesiones'] ?? count($history);
$horasTotales = $stats['horas_totales'] ?? 0;
$promedioMinutos = $stats['promedio_minutos'] ?? 0;
$nivelDisciplina = $stats['nivel_disciplina'] ?? 'Nuevo Ingreso';
?>

<div class="learner-history-wrap">

    <!-- Encabezado de Perfil Ejecutivo del Usuario -->
    <section class="flat-card history-profile-card">
        <div class="history-profile-main">
            <div class="history-avatar-box <?= $roleThemeClass ?>">
                <?= strtoupper(substr($learner['nombres'] ?? $learner['nombre'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="history-profile-info">
                <div class="history-badge-row">
                    <span class="card-category-badge <?= $roleThemeClass ?>">
                        <?= htmlspecialchars($catBadge) ?>
                    </span>
                    <span class="history-status-active">
                        <span class="status-indicator-dot dot-green"></span>
                        Usuario Activo
                    </span>
                </div>
                <h2 class="history-user-title"><?= htmlspecialchars($nombreCompleto) ?></h2>
                <p class="history-user-meta">
                    <span>Documento: <strong><?= htmlspecialchars($learner['documento'] ?? '') ?></strong></span>
                    <?php if ($tipoUsuario === 'APRENDIZ' && !empty($learner['ficha'])): ?>
                        <span class="meta-separator">&bull;</span>
                        <span>Ficha: <strong><?= htmlspecialchars($learner['ficha']) ?></strong></span>
                    <?php endif; ?>
                    <span class="meta-separator">&bull;</span>
                    <span><?= htmlspecialchars($learner['programa_formacion'] ?? $learner['programa'] ?? 'Comunidad SENA') ?></span>
                </p>
            </div>
        </div>

        <div class="history-profile-actions">
            <a href="/carne" class="btn-action-primary">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                <span>Ver Mi Carné Digital</span>
            </a>
            <a href="/acceso" class="btn-action-outline">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 3h3v-3h-3v3zm3 3h3v-3h-3v3zm-3 0v-3h-3v3h3zm3-6h3V11h-3v3zm-6 0h3v-3h-3v3z"/></svg>
                <span>Punto de Acceso</span>
            </a>
        </div>
    </section>

    <!-- Rejilla de Indicadores de Rendimiento (KPIs) -->
    <section class="kpi-metrics-grid">
        <div class="kpi-metric-card border-accent-blue">
            <div class="kpi-icon-wrap icon-blue">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Sesiones Totales</span>
                <span class="kpi-value text-blue"><?= $totalSesiones ?></span>
                <span class="kpi-hint">Visitas registradas</span>
            </div>
        </div>

        <div class="kpi-metric-card border-accent-green">
            <div class="kpi-icon-wrap icon-green">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.07 4.74 14.12 4 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Horas Acumuladas</span>
                <span class="kpi-value text-green"><?= $horasTotales ?> <small class="kpi-unit">hrs</small></span>
                <span class="kpi-hint">Tiempo en sala</span>
            </div>
        </div>

        <div class="kpi-metric-card border-accent-orange">
            <div class="kpi-icon-wrap icon-orange">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Promedio Sesión</span>
                <span class="kpi-value text-orange"><?= $promedioMinutos ?> <small class="kpi-unit">min</small></span>
                <span class="kpi-hint">Por cada visita</span>
            </div>
        </div>

        <div class="kpi-metric-card border-accent-teal">
            <div class="kpi-icon-wrap icon-teal">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Constancia</span>
                <span class="kpi-value text-teal"><?= htmlspecialchars($nivelDisciplina) ?></span>
                <span class="kpi-hint">Bienestar SENA</span>
            </div>
        </div>
    </section>

    <!-- Lista Cronológica Detallada de Visitas -->
    <section class="flat-card history-table-card">
        <div class="history-section-header">
            <div>
                <h3 class="history-table-title">Historial Detallado de Visitas</h3>
                <p class="history-table-subtitle">Control de asistencia institucional y constancia deportiva</p>
            </div>
            <span class="history-count-pill">
                <?= $totalSesiones ?> <?= $totalSesiones === 1 ? 'registro' : 'registros' ?>
            </span>
        </div>

        <?php if (empty($history)): ?>
            <div class="history-empty-state">
                <div class="empty-state-icon-box">
                    <svg class="empty-state-svg" viewBox="0 0 24 24"><path d="M20.57 14.86L22 13.43 20.57 12 17 15.57 8.43 7 12 3.43 10.57 2 9.14 3.43 7.71 2 5.57 4.14 4.14 2.71 2.71 4.14l1.43 1.43L2 7.71l1.43 1.43L2 10.57 3.43 12 7 8.43 15.57 17 12 20.57 13.43 22l1.43-1.43L16.29 22l2.14-2.14 1.43 1.43 1.43-1.43-1.43-1.43L22 16.29z"/></svg>
                </div>
                <h4>¡Tu historial deportivo está listo para comenzar!</h4>
                <p>Aún no has registrado ingresos en el gimnasio institucional. Escanea el código QR en la entrada para marcar tu primer entrenamiento.</p>
                <a href="/acceso" class="btn-action-primary mt-3">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    <span>Marcar Mi Primer Acceso</span>
                </a>
            </div>
        <?php else: ?>
            <div class="history-table-responsive">
                <table class="history-data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Duración</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $item): ?>
                            <?php 
                            $esEnSala = empty($item['hora_salida']);
                            $duracion = !empty($item['duracion_minutos']) ? (int)$item['duracion_minutos'] : 0;
                            $porcentajeDuracion = min(100, round(($duracion / 90) * 100));
                            ?>
                            <tr class="history-table-row <?= $esEnSala ? 'row-currently-active' : '' ?>">
                                <td class="cell-date">
                                    <div class="date-badge-box">
                                        <svg class="ui-icon-xs text-muted" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                                        <strong><?= htmlspecialchars($item['fecha']) ?></strong>
                                    </div>
                                </td>
                                <td class="cell-time">
                                    <span class="time-stamp-val stamp-in">
                                        <svg class="ui-icon-xs text-green" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                        <?= htmlspecialchars(substr($item['hora_entrada'], 0, 5)) ?>
                                    </span>
                                </td>
                                <td class="cell-time">
                                    <?php if ($esEnSala): ?>
                                        <span class="status-live-pulse-pill">
                                            <span class="status-indicator-dot dot-green"></span>
                                            En Sala
                                        </span>
                                    <?php else: ?>
                                        <span class="time-stamp-val stamp-out">
                                            <svg class="ui-icon-xs text-muted" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                                            <?= htmlspecialchars(substr($item['hora_salida'], 0, 5)) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="cell-duration">
                                    <?php if ($esEnSala): ?>
                                        <span class="text-muted font-italic">En progreso</span>
                                    <?php else: ?>
                                        <div class="duration-meter-wrap">
                                            <span class="duration-text"><?= $duracion ?> min</span>
                                            <div class="mini-progress-track">
                                                <div class="mini-progress-fill" style="width: <?= $porcentajeDuracion ?>%;"></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="cell-status">
                                    <?php if ($esEnSala): ?>
                                        <span class="badge-pill-warning">Entrenando</span>
                                    <?php elseif (!empty($item['cierre_automatico'])): ?>
                                        <span class="badge-pill-info" title="Cierre nocturno automático">Auto-cerrado</span>
                                    <?php else: ?>
                                        <span class="badge-pill-success">Completado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</div>
