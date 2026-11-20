<div class="trainer-flat-container">

    <!-- Tarjetas de Métricas Flat (Sin adornos artificiales) -->
    <div class="flat-stats-grid mb-4">
        <div class="flat-stat-box">
            <span class="flat-stat-label">En Sala Ahora</span>
            <div class="flat-stat-number" id="monitor-count-now"><?= $aforo['ocupacion_actual'] ?></div>
            <span class="flat-stat-sub">de <?= $aforo['capacidad_maxima'] ?> cupos</span>
        </div>

        <div class="flat-stat-box">
            <span class="flat-stat-label">Cupos Disponibles</span>
            <div class="flat-stat-number text-green" id="monitor-slots-now"><?= $aforo['cupos_disponibles'] ?></div>
            <span class="flat-stat-sub" id="monitor-status-sub"><?= $aforo['cupos_disponibles'] > 0 ? 'Entrada habilitada' : 'Aforo completo' ?></span>
        </div>

        <div class="flat-stat-box">
            <span class="flat-stat-label">Horario de Operación</span>
            <div class="flat-stat-number stat-highlight-time">06:00 - 20:00</div>
            <span class="flat-stat-sub">Lunes a Viernes</span>
        </div>
    </div>

    <!-- Barra de Búsqueda, Registro Rápido y Control de Sonido de Entrada -->
    <div class="flat-action-toolbar mb-3">
        <div class="flat-search-wrap">
            <input type="text" 
                   id="trainee-search" 
                   class="flat-input" 
                   placeholder="Buscar aprendiz por nombre, ficha o documento..." 
                   onkeyup="filterTable()">
        </div>

        <!-- Botón de Control de Sonido de Ingreso -->
        <button type="button" 
                class="btn-sound-toggle" 
                id="btnSoundToggle" 
                onclick="toggleChimeSound()" 
                title="Activar o silenciar el sonido cuando alguien ingresa">
            <svg class="ui-icon" id="soundIconOn" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
            <svg class="ui-icon hidden" id="soundIconOff" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
            <span id="soundToggleLabel">Sonido de Entrada: Activo</span>
        </button>

        <!-- Registro Manual Rápido sin Flechas de Stepper -->
        <form action="/monitor/asistencia-manual" method="POST" class="flat-quick-access-form">
            <input type="hidden" name="gym_id" value="<?= $gymId ?>">
            <input type="text" 
                   name="documento" 
                   class="flat-input input-no-spin" 
                   placeholder="Documento para registro..." 
                   required 
                   inputmode="numeric"
                   pattern="[0-9]*">
            <button type="submit" class="flat-btn-primary btn-nowrap">
                Registrar
            </button>
        </form>
    </div>

    <!-- Toast de Notificación de Nuevo Ingreso -->
    <div id="entryToast" class="entry-toast hidden">
        <div class="toast-indicator-pulse"></div>
        <div class="toast-text-box">
            <strong id="toastTitle">Nuevo ingreso</strong>
            <span id="toastDetails">Carlos Gómez acaba de ingresar a sala.</span>
        </div>
    </div>

    <!-- Lista Activa de Sala (Tabla Flat y Espaciosa) -->
    <section class="flat-card mb-4" id="modulo-aforo">
        <div class="flat-card-header-simple">
            <div>
                <h3>Comunidad en Sala Activa (<span id="inside-count-badge"><?= count($inside) ?></span>)</h3>
                <span class="text-muted small">Monitoreo en tiempo real del aforo del gimnasio</span>
            </div>
            <div class="live-pulse-badge">
                <span class="status-indicator-dot dot-green"></span>
                <span>En vivo</span>
            </div>
        </div>

        <?php if (empty($inside)): ?>
            <div class="flat-empty-state" id="empty-state-box">
                <div class="empty-state-icon-box">
                    <svg class="ui-icon-lg text-muted" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </div>
                <h4>No hay personas en sala en este momento</h4>
                <p class="text-muted">Los ingresos que se registren mediante el cartel QR o registro manual aparecerán aquí automáticamente.</p>
            </div>
        <?php else: ?>
            <div class="flat-table-responsive" id="table-wrap-box">
                <table class="flat-table" id="trainee-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Documento</th>
                            <th>Rol / Ficha</th>
                            <th>Programa / Dependencia</th>
                            <th>Hora Ingreso</th>
                            <th class="cell-align-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="trainee-table-body">
                        <?php foreach ($inside as $person): ?>
                            <tr id="row-visit-<?= $person['id'] ?>">
                                <td>
                                    <div class="table-user-cell">
                                        <div class="table-avatar-initial">
                                            <?= strtoupper(substr($person['nombres'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($person['nombres']) ?> <?= htmlspecialchars($person['apellidos']) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($person['documento']) ?></td>
                                <td>
                                    <span class="flat-chip-green"><?= htmlspecialchars($person['ficha']) ?></span>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($person['programa_formacion']) ?></td>
                                <td>
                                    <span class="time-pill-in">
                                        <?= htmlspecialchars(substr($person['hora_entrada'], 0, 5)) ?>
                                    </span>
                                </td>
                                <td class="cell-align-right">
                                    <form action="/monitor/marcar-salida/<?= $person['id'] ?>" method="POST" onsubmit="return confirm('¿Confirmas marcar la salida de <?= htmlspecialchars(addslashes($person['nombres'])) ?>?');" class="d-inline">
                                        <button type="submit" class="flat-btn-danger-sm">
                                            Marcar Salida
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <!-- Cartel QR Único para Portería y Entrada -->
    <section class="flat-card text-center" id="modulo-qr">
        <div class="flat-card-header-simple text-center">
            <h3>Cartel QR Único Institucional para la Entrada</h3>
            <p class="text-muted small">Pega este cartel impreso en la puerta y torniquete del gimnasio. Funciona para <strong>Aprendices, Instructores, Trabajadores e Invitados</strong>.</p>
        </div>

        <div class="qr-preview-wrapper">
            <div class="flat-qr-frame">
                <img src="/images/qr-entrada-oficial.svg" alt="QR Único de Entrada" class="qr-preview-img">
            </div>
            <div class="qr-preview-url">
                <span class="flat-badge-venue">Punto de Acceso Oficial GymSENA</span>
            </div>
        </div>

        <div class="qr-actions-row">
            <a href="/cartel-qr" target="_blank" class="flat-btn-primary qr-btn-link">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                <span>Ver e Imprimir Cartel Oficial (A4 / Carta)</span>
            </a>
            <a href="/acceso" target="_blank" class="flat-btn-secondary qr-btn-link">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>
                <span>Probar Vista Móvil</span>
            </a>
        </div>
    </section>

</div>

<script>
// Filtrado rápido de tabla
function filterTable() {
    const input = document.getElementById('trainee-search');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('trainee-table');
    if (!table) return;
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const text = rows[i].textContent || rows[i].innerText;
        rows[i].style.display = text.toLowerCase().includes(filter) ? '' : 'none';
    }
}

// ========================================================
// SISTEMA DE TIMBRE DE ENTRADA CON WEB AUDIO API (100% OFFLINE)
// ========================================================
let audioCtx = null;
let soundEnabled = true;
let lastKnownAttendanceId = <?= !empty($inside[0]['id']) ? (int)$inside[0]['id'] : 0 ?>;

function initAudioContext() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
}

// Activar audio con cualquier interacción del usuario
document.addEventListener('click', initAudioContext, { once: true });
document.addEventListener('keydown', initAudioContext, { once: true });

function toggleChimeSound() {
    initAudioContext();
    soundEnabled = !soundEnabled;
    const iconOn = document.getElementById('soundIconOn');
    const iconOff = document.getElementById('soundIconOff');
    const label = document.getElementById('soundToggleLabel');

    if (soundEnabled) {
        if (iconOn) iconOn.classList.remove('hidden');
        if (iconOff) iconOff.classList.add('hidden');
        if (label) label.textContent = 'Sonido de Entrada: Activo';
        playEntryChime(); // Toque de prueba para confirmar
    } else {
        if (iconOn) iconOn.classList.add('hidden');
        if (iconOff) iconOff.classList.remove('hidden');
        if (label) label.textContent = 'Sonido de Entrada: Silenciado';
    }
}

function playEntryChime() {
    if (!soundEnabled) return;
    try {
        initAudioContext();
        if (!audioCtx) return;

        const now = audioCtx.currentTime;

        // Campana armónica institucional: Do5 (523.25 Hz) -> Sol5 (783.99 Hz)
        const osc1 = audioCtx.createOscillator();
        const gain1 = audioCtx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(523.25, now);
        gain1.gain.setValueAtTime(0.25, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc1.connect(gain1);
        gain1.connect(audioCtx.destination);
        osc1.start(now);
        osc1.stop(now + 0.35);

        const osc2 = audioCtx.createOscillator();
        const gain2 = audioCtx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(783.99, now + 0.12);
        gain2.gain.setValueAtTime(0.3, now + 0.12);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.65);
        osc2.connect(gain2);
        gain2.connect(audioCtx.destination);
        osc2.start(now + 0.12);
        osc2.stop(now + 0.65);
    } catch (err) {
        console.warn('Audio chime error:', err);
    }
}

function showEntryToast(nombre, hora) {
    const toast = document.getElementById('entryToast');
    const title = document.getElementById('toastTitle');
    const details = document.getElementById('toastDetails');

    if (!toast) return;

    if (title) title.textContent = '¡Nuevo ingreso a sala!';
    if (details) details.textContent = `${nombre} ingresó a las ${hora}.`;

    toast.classList.remove('hidden');
    toast.classList.add('toast-visible');

    setTimeout(() => {
        toast.classList.remove('toast-visible');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 4500);
}

// Polling inteligente cada 5 segundos para detectar ingresos y tocar timbre
function checkLiveMonitorEntries() {
    fetch(`/api/monitor/live-check?last_id=${lastKnownAttendanceId}`)
        .then(r => r.json())
        .then(data => {
            if (!data) return;

            // Actualizar contadores
            const countNowEl = document.getElementById('monitor-count-now');
            const slotsNowEl = document.getElementById('monitor-slots-now');
            const insideBadgeEl = document.getElementById('inside-count-badge');
            const statusSubEl = document.getElementById('monitor-status-sub');

            if (countNowEl && data.aforo) countNowEl.textContent = data.aforo.ocupacion_actual;
            if (slotsNowEl && data.aforo) slotsNowEl.textContent = data.aforo.cupos_disponibles;
            if (insideBadgeEl && typeof data.inside_count !== 'undefined') insideBadgeEl.textContent = data.inside_count;
            if (statusSubEl && data.aforo) {
                statusSubEl.textContent = data.aforo.cupos_disponibles > 0 ? 'Entrada habilitada' : 'Aforo completo';
            }

            // Si hay un nuevo ingreso detectado
            if (data.new_entry) {
                playEntryChime();
                showEntryToast(data.new_entry.nombre, data.new_entry.hora);
                lastKnownAttendanceId = data.new_entry.id;

                // Si la página tenía empty state y ahora hay registros, recargar vista suavemente
                const emptyBox = document.getElementById('empty-state-box');
                if (emptyBox) {
                    location.reload();
                }
            } else if (data.latest_id > lastKnownAttendanceId) {
                lastKnownAttendanceId = data.latest_id;
            }
        })
        .catch(() => {});
}

setInterval(checkLiveMonitorEntries, 5000);
</script>
