<div class="flat-admin-wrap">

    <!-- Encabezado del Instructor Encargado -->
    <div class="flat-admin-header">
        <div>
            <span class="flat-chip-green">Líder / Instructor Encargado del Gimnasio</span>
            <h2 class="flat-admin-title">Panel de Control y Gestión Institucional</h2>
            <p class="text-muted small">Trazabilidad de aforo, analítica de participación y asignación de pasantes de sala</p>
        </div>

        <div class="flat-admin-actions">
            <a href="/admin/exportar-csv?inicio=<?= urlencode($startDate) ?>&fin=<?= urlencode($endDate) ?>&gym=<?= $gymId ?>" class="flat-btn-primary">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                <span>Descargar Reporte Excel (.CSV)</span>
            </a>
        </div>
    </div>

    <!-- SECCIÓN 1: MÉTRICAS Y HORAS PICO -->
    <div id="modulo-metricas">
        <!-- Filtro de Rango de Fechas -->
        <section class="flat-card flat-filter-box mt-3">
            <form method="GET" action="/admin" class="flat-filter-form">
                <div class="flat-filter-fields">
                    <div class="flat-filter-col">
                        <label for="inicio" class="flat-label">Fecha inicial</label>
                        <input type="date" id="inicio" name="inicio" class="flat-input" value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="flat-filter-col">
                        <label for="fin" class="flat-label">Fecha final</label>
                        <input type="date" id="fin" name="fin" class="flat-input" value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="flat-filter-col-btn">
                        <button type="submit" class="flat-btn-secondary w-100">
                            Filtrar Datos
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <!-- Métricas Clave de Rendimiento (KPIs Flat) -->
        <div class="flat-stats-grid mt-3">
            <div class="flat-stat-box">
                <span class="flat-stat-label">Visitas Totales</span>
                <div class="flat-stat-number"><?= $summary['total_visitas'] ?></div>
                <span class="flat-stat-sub">En el rango seleccionado</span>
            </div>

            <div class="flat-stat-box">
                <span class="flat-stat-label">Usuarios Únicos</span>
                <div class="flat-stat-number"><?= $summary['aprendices_unicos'] ?></div>
                <span class="flat-stat-sub">Asistentes individuales</span>
            </div>

            <div class="flat-stat-box">
                <span class="flat-stat-label">Tiempo Promedio</span>
                <div class="flat-stat-number"><?= $summary['promedio_minutos'] ?: '--' ?> <small class="stat-unit-sm">min</small></div>
                <span class="flat-stat-sub">Permanencia por sesión</span>
            </div>

            <div class="flat-stat-box">
                <span class="flat-stat-label">Aforo en Vivo</span>
                <div class="flat-stat-number text-green"><?= $aforo['ocupacion_actual'] ?> <small class="stat-max-sub">/ <?= $aforo['capacidad_maxima'] ?></small></div>
                <span class="flat-stat-sub"><?= $aforo['cupos_disponibles'] ?> cupos disponibles</span>
            </div>
        </div>

        <!-- Analítica Visual Flat: Horas Pico y Top Programas -->
        <div class="grid-2-cols mt-4">
            <!-- Gráfica de Horas Pico -->
            <section class="flat-card">
                <div class="flat-card-header-simple">
                    <div>
                        <h3>Curva de Horas Pico</h3>
                        <p class="text-muted small">Distribución de ingresos por hora del día</p>
                    </div>
                </div>

                <div class="flat-chart-bars">
                    <?php 
                    $maxHour = !empty($peakHours) ? max(max($peakHours), 1) : 1;
                    foreach ($peakHours as $hora => $cant): 
                        $pct = round(($cant / $maxHour) * 100);
                    ?>
                        <div class="flat-bar-column">
                            <div class="flat-bar-fill bar-height-<?= max(6, $pct) ?>" style="height: <?= max(6, $pct) ?>%;" title="<?= $hora ?>: <?= $cant ?> ingresos">
                                <?php if ($cant > 0): ?>
                                    <span class="flat-bar-number"><?= $cant ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="flat-bar-label"><?= substr($hora, 0, 2) ?>h</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flat-chart-legend">
                    <span>06:00 AM (Apertura)</span>
                    <span>20:00 PM (Cierre)</span>
                </div>
            </section>

            <!-- Top Programas de Formación -->
            <section class="flat-card">
                <div class="flat-card-header-simple">
                    <div>
                        <h3>Top Programas y Áreas</h3>
                        <p class="text-muted small">Mayor participación en sala</p>
                    </div>
                </div>

                <?php if (empty($topPrograms)): ?>
                    <div class="flat-empty-state">
                        <p>No se registran asistencias en este rango de fechas.</p>
                    </div>
                <?php else: ?>
                    <div class="flat-ranking-list">
                        <?php 
                        $maxProg = !empty($topPrograms) ? max(max($topPrograms), 1) : 1;
                        foreach ($topPrograms as $prog => $cant): 
                            $pct = round(($cant / $maxProg) * 100);
                        ?>
                            <div class="flat-ranking-item">
                                <div class="flat-ranking-header">
                                    <span class="flat-prog-title"><?= htmlspecialchars($prog) ?></span>
                                    <span class="flat-prog-count"><?= $cant ?> visitas</span>
                                </div>
                                <div class="flat-progress-track ranking-track">
                                    <div class="flat-progress-bar" style="width: <?= $pct ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- SECCIÓN 2: GESTIÓN DE PASANTES / ENTRENADORES EN ETAPA PRODUCTIVA -->
    <div id="modulo-pasantes" class="mt-4">
        <div class="flat-section-banner">
            <div>
                <span class="flat-chip-green">Equipo de Sala</span>
                <h3 class="section-banner-title">Pasantes / Entrenadores de Turno</h3>
                <p class="text-muted small">Crea y administra las cuentas de los pasantes en etapa práctica que atienden la sala del gimnasio.</p>
            </div>
        </div>

        <div class="grid-2-cols mt-3">
            <!-- Formulario de Registro de Pasante -->
            <section class="flat-card">
                <h3 class="mb-2">Asignar Nuevo Pasante</h3>
                <p class="text-muted small mb-3">Diligencia los datos del aprendiz en pasantía para habilitar su acceso al panel del entrenador:</p>

                <form action="/admin/crear-pasante" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= \GymSena\Core\Auth::csrfToken() ?>">
                    <div class="flat-form-group">
                        <label for="pasante_doc" class="flat-label">Documento de Identidad</label>
                        <input type="text" 
                               id="pasante_doc" 
                               name="documento" 
                               class="flat-input input-no-spin" 
                               placeholder="Número de cédula o tarjeta" 
                               required 
                               inputmode="numeric" 
                               pattern="[0-9]*"
                               autocomplete="off">
                    </div>

                    <div class="flat-form-group">
                        <label for="pasante_nombre" class="flat-label">Nombre Completo</label>
                        <input type="text" 
                               id="pasante_nombre" 
                               name="nombre_completo" 
                               class="flat-input" 
                               placeholder="Ej: Daniel Esteban Osorio" 
                               required>
                    </div>

                    <div class="flat-form-group">
                        <label for="pasante_email" class="flat-label">Correo Electrónico (Acceso)</label>
                        <input type="email" 
                               id="pasante_email" 
                               name="email" 
                               class="flat-input" 
                               placeholder="pasante@sena.edu.co" 
                               required>
                    </div>

                    <div class="flat-form-group">
                        <label for="pasante_pass" class="flat-label">Contraseña Inicial</label>
                        <input type="password" 
                               id="pasante_pass" 
                               name="password" 
                               class="flat-input" 
                               placeholder="Mínimo 6 caracteres" 
                               required>
                    </div>

                    <button type="submit" class="flat-btn-primary w-100 mt-2">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        <span>Habilitar Pasante como Entrenador</span>
                    </button>
                </form>
            </section>

            <!-- Lista de Pasantes Asignados -->
            <section class="flat-card">
                <div class="flat-card-header-simple">
                    <div>
                        <h3>Pasantes Registrados (<?= count($trainers ?? []) ?>)</h3>
                        <p class="text-muted small">Cuentas con acceso al panel de supervisión de sala</p>
                    </div>
                </div>

                <?php if (empty($trainers)): ?>
                    <div class="flat-empty-state">
                        <p>No hay pasantes registrados actualmente. Puedes crear uno usando el formulario.</p>
                    </div>
                <?php else: ?>
                    <div class="staff-list-stack">
                        <?php foreach ($trainers as $trainer): ?>
                            <?php $isActivo = ($trainer['estado'] ?? '') === 'ACTIVO'; ?>
                            <div class="staff-card-item">
                                <div class="staff-card-info">
                                    <div class="table-avatar-initial">
                                        <?= strtoupper(substr($trainer['nombre_completo'] ?? 'P', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($trainer['nombre_completo'] ?? '') ?></strong>
                                        <div class="staff-card-meta">
                                            <span>Doc: <?= htmlspecialchars($trainer['documento'] ?? '') ?></span>
                                            <span class="meta-separator">&bull;</span>
                                            <span><?= htmlspecialchars($trainer['email'] ?? '') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="staff-card-actions">
                                    <span class="badge-pill <?= $isActivo ? 'badge-pill-success' : 'badge-pill-info' ?>">
                                        <?= $isActivo ? 'Activo' : 'Pausado' ?>
                                    </span>

                                    <form action="/admin/toggle-pasante/<?= $trainer['id'] ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= \GymSena\Core\Auth::csrfToken() ?>">
                                        <button type="submit" class="btn-subtle-action" title="<?= $isActivo ? 'Pausar acceso' : 'Reactivar acceso' ?>">
                                            <?= $isActivo ? 'Pausar' : 'Activar' ?>
                                        </button>
                                    </form>

                                    <form action="/admin/eliminar-pasante/<?= $trainer['id'] ?>" method="POST" onsubmit="return confirm('¿Confirmas eliminar a este pasante del sistema?');" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= \GymSena\Core\Auth::csrfToken() ?>">
                                        <button type="submit" class="btn-subtle-danger" title="Eliminar cuenta">
                                            <svg class="ui-icon-xs" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- SECCIÓN 3: AJUSTES OPERATIVOS Y CARGA MASIVA -->
    <div id="modulo-ajustes" class="grid-2-cols mt-4">
        <section class="flat-card">
            <h3 class="mb-2">Configurar Capacidad Máxima (Aforo)</h3>
            <p class="text-muted small mb-3">Define el cupo máximo de personas simultáneas en el gimnasio:</p>

            <form action="/admin/actualizar-config" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \GymSena\Core\Auth::csrfToken() ?>">
                <input type="hidden" name="gym_id" value="<?= $gymId ?>">
                <div class="flat-form-group">
                    <label for="capacidad_maxima" class="flat-label">Capacidad de Sala</label>
                    <div class="input-btn-group">
                        <input type="number" 
                               id="capacidad_maxima" 
                               name="capacidad_maxima" 
                               class="flat-input input-no-spin" 
                               value="<?= $aforo['capacidad_maxima'] ?>" 
                               min="5" 
                               max="200" 
                               required>
                        <button type="submit" class="flat-btn-primary btn-nowrap">
                            Actualizar
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <section class="flat-card">
            <h3 class="mb-2">Carga Masiva de Usuarios (CSV)</h3>
            <p class="text-muted small mb-3">Importa usuarios autorizados para precargar fichas en el sistema:</p>
            
            <form action="/admin/carga-masiva" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= \GymSena\Core\Auth::csrfToken() ?>">
                <div class="flat-form-group">
                    <input type="file" name="archivo_excel" accept=".csv" class="flat-input" required>
                </div>
                <button type="submit" class="flat-btn-secondary w-100">
                    Subir e Importar Archivo
                </button>
            </form>
        </section>
    </div>

</div>
