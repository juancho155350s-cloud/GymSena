<div class="result-wrapper">
    <?php 
    $isSuccess = !empty($result['success']);
    $type = $result['type'] ?? 'INFO';
    $action = $result['action'] ?? '';
    ?>

    <?php if ($isSuccess && ($action === 'ENTRADA' || $type === 'CHECKIN' || $type === 'REGISTERED_AND_CHECKIN')): ?>
        <?php 
        $cat = $result['learner']['tipo_usuario'] ?? 'APRENDIZ';
        $catTitle = match($cat) {
            'INSTRUCTOR' => 'Instructor SENA',
            'FUNCIONARIO' => 'Funcionario / Colaborador',
            'INVITADO' => 'Invitado / Visitante',
            default => 'Aprendiz Ficha ' . htmlspecialchars($result['learner']['ficha'] ?? '')
        };
        ?>
        <!-- Tarjeta de Entrada Exitosa (Heurística 1: Visibilidad y Confirmación Inmediata) -->
        <div class="result-card-celebration">
            <div class="result-celebration-badge badge-in-check">
                <svg class="result-badge-svg" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
            </div>
            <span class="result-kpi-pill pill-status-in">ENTRADA REGISTRADA</span>
            <h2 class="result-celebration-title">
                ¡A entrenar, <?= htmlspecialchars(explode(' ', $result['learner']['nombres'])[0]) ?>!
            </h2>
            <p class="result-celebration-subtitle">
                <strong><?= $catTitle ?></strong> &bull; <?= htmlspecialchars($result['learner']['programa_formacion']) ?>
            </p>

            <div class="result-details-box">
                <div class="result-detail-col">
                    <span class="result-detail-label">Hora Ingreso</span>
                    <span class="result-detail-value"><?= htmlspecialchars(substr($result['attendance']['hora_entrada'], 0, 5)) ?></span>
                </div>
                <div class="result-detail-col">
                    <span class="result-detail-label">Aforo en Sala</span>
                    <span class="result-detail-value"><?= $result['aforo']['ocupacion_actual'] ?> <small class="result-detail-max">/ <?= $result['aforo']['capacidad_maxima'] ?></small></span>
                </div>
            </div>

            <p class="result-message-text">
                <?= htmlspecialchars($result['message']) ?>
            </p>

            <div class="result-actions-stack">
                <a href="/carne" class="btn-primary-action btn-full-width">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                    <span>Ver Mi Carné Digital</span>
                </a>
                <a href="/historial" class="btn-outline-action btn-full-width">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    <span>Mi Historial de Asistencias</span>
                </a>
                <a href="/acceso" class="flat-link-subtle mt-2">
                    <span>Volver al Punto de Acceso</span>
                </a>
            </div>
        </div>

    <?php elseif ($isSuccess && ($action === 'SALIDA' || $type === 'CHECKOUT')): ?>
        <!-- Tarjeta de Salida Exitosa -->
        <div class="result-card-celebration">
            <div class="result-celebration-badge badge-out-check">
                <svg class="result-badge-svg" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
            </div>
            <span class="result-kpi-pill pill-status-out">SALIDA CONFIRMADA</span>
            <h2 class="result-celebration-title">
                ¡Excelente Entrenamiento!
            </h2>
            <p class="result-celebration-subtitle">
                <?= htmlspecialchars($result['learner']['nombres']) ?> <?= htmlspecialchars($result['learner']['apellidos']) ?>
            </p>

            <div class="result-details-box">
                <div class="result-detail-col">
                    <span class="result-detail-label">Hora Salida</span>
                    <span class="result-detail-value"><?= htmlspecialchars(substr($result['attendance']['hora_salida'], 0, 5)) ?></span>
                </div>
                <div class="result-detail-col">
                    <span class="result-detail-label">Tiempo Total</span>
                    <span class="result-detail-value"><?= $result['attendance']['duracion_minutos'] ?? '?' ?> <small class="result-detail-unit">min</small></span>
                </div>
            </div>

            <p class="result-message-text">
                <?= htmlspecialchars($result['message']) ?>
            </p>

            <div class="result-actions-stack">
                <a href="/historial" class="btn-primary-action btn-full-width">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    <span>Ver mis Constancias de Asistencia</span>
                </a>
                <a href="/carne" class="btn-outline-action btn-full-width">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                    <span>Ver Mi Carné Digital</span>
                </a>
                <a href="/acceso" class="flat-link-subtle mt-2">
                    <span>Listo / Volver</span>
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- Tarjeta de Advertencia / Aforo Lleno / Bloqueo (Heurística 9: Mensajes Constructivos) -->
        <div class="result-card-celebration">
            <div class="result-celebration-badge badge-err-check">
                <svg class="result-badge-svg" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
            </div>
            <span class="result-kpi-pill pill-status-err">AVISO DE SALA</span>
            <h2 class="result-celebration-title-error">
                No pudimos procesar tu ingreso
            </h2>

            <div class="alert alert-danger result-alert-box">
                <?= htmlspecialchars($result['message']) ?>
            </div>

            <div class="result-actions-stack">
                <?php if (!empty($result['type']) && $result['type'] === 'NEEDS_REGISTRATION'): ?>
                    <a href="/acceso?registro=1" class="btn-primary-action btn-full-width">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        <span>Diligenciar Inscripción Rápida</span>
                    </a>
                <?php endif; ?>
                <a href="/acceso" class="btn-outline-action btn-full-width">
                    <span>Volver al Punto de Acceso</span>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($result['learner']['documento'])): ?>
    <script>
        try {
            localStorage.setItem('gymsena_doc', <?= json_encode($result['learner']['documento']) ?>);
            localStorage.setItem('gymsena_nombre', <?= json_encode($result['learner']['nombres']) ?>);
        } catch(e) {}
    </script>
    <?php endif; ?>
</div>
