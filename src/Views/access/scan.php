<div class="flat-access-wrap">

    <!-- Medidor de Aforo Flat (Limpio, Ergonómico y de Alto Contraste) -->
    <section class="flat-card flat-aforo-card">
        <div class="flat-aforo-header">
            <div>
                <span class="flat-badge-venue"><?= htmlspecialchars($aforo['nombre']) ?></span>
                <h2 class="flat-aforo-title">Aforo en Sala</h2>
            </div>
            <div class="flat-aforo-counter">
                <span class="flat-count-current aforo-count"><?= $aforo['ocupacion_actual'] ?></span>
                <span class="flat-count-max">/ <?= $aforo['capacidad_maxima'] ?></span>
            </div>
        </div>

        <div class="flat-progress-track">
            <div class="flat-progress-bar" style="width: <?= min(100, $aforo['porcentaje_ocupacion']) ?>%;"></div>
        </div>

        <div class="flat-aforo-footer">
            <span class="flat-slots-badge <?= $aforo['cupos_disponibles'] > 0 ? 'badge-available' : 'badge-full' ?>">
                <?php if ($aforo['cupos_disponibles'] > 0): ?>
                    <?= $aforo['cupos_disponibles'] ?> cupos disponibles
                <?php else: ?>
                    Aforo Completo
                <?php endif; ?>
            </span>
            <span class="flat-live-tag">Tiempo real</span>
        </div>
    </section>

    <?php if (!$isOpen): ?>
        <!-- Aviso Fuera de Horario -->
        <section class="flat-card flat-card-warning text-center">
            <div class="flat-icon-box">
                <svg class="ui-icon-lg" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7-.8 1.3z"/></svg>
            </div>
            <h3>Gimnasio Cerrado</h3>
            <p class="text-muted"><?= htmlspecialchars($closedReason) ?></p>
            <p class="flat-schedule-hint">Horario institucional: Lunes a Viernes &bull; 06:00 a 20:00</p>
        </section>
    <?php else: ?>

        <?php if ($learner): ?>
            <?php 
            $tipoUsuario = $learner['tipo_usuario'] ?? 'APRENDIZ';
            $catBadge = match($tipoUsuario) {
                'INSTRUCTOR' => 'Instructor SENA',
                'FUNCIONARIO' => 'Funcionario / Colaborador',
                'INVITADO' => 'Invitado / Visitante',
                default => 'Aprendiz SENA'
            };
            $isInside = !empty($activeVisit);
            ?>
            <!-- Usuario Reconocido: Acceso Express de 1 Solo Toque (Cero Fricción) -->
            <section class="flat-card flat-recognized-box">
                <div class="flat-user-row">
                    <div class="flat-user-avatar <?= $isInside ? 'avatar-active-pulse' : '' ?>">
                        <?= strtoupper(substr($learner['nombre'] ?? $learner['nombres'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="flat-user-meta">
                        <span class="flat-greeting"><?= $catBadge ?></span>
                        <h3><?= htmlspecialchars($learner['nombre'] ?? ($learner['nombres'] . ' ' . $learner['apellidos'])) ?></h3>
                        <p class="text-muted">
                            <?php if ($tipoUsuario === 'APRENDIZ'): ?>
                                Ficha <?= htmlspecialchars($learner['ficha'] ?? '') ?> &bull; <?= htmlspecialchars($learner['programa'] ?? $learner['programa_formacion'] ?? '') ?>
                            <?php else: ?>
                                <?= htmlspecialchars($learner['programa'] ?? $learner['programa_formacion'] ?? 'Comunidad SENA') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- Estado Actual en Sala (Heurística Nielsen #1: Visibilidad del Estado) -->
                <div class="current-status-strip <?= $isInside ? 'status-inside' : 'status-outside' ?>">
                    <?php if ($isInside): ?>
                        <span class="status-indicator-dot dot-green"></span>
                        <span><strong>Actualmente en sala</strong> &bull; Ingresaste a las <?= htmlspecialchars(substr($activeVisit['hora_entrada'], 0, 5)) ?></span>
                    <?php else: ?>
                        <span class="status-indicator-dot dot-gray"></span>
                        <span><strong>Fuera del gimnasio</strong> &bull; Listo para ingresar</span>
                    <?php endif; ?>
                </div>

                <form action="/procesar-acceso" method="POST" class="mt-3">
                    <input type="hidden" name="gym_id" value="<?= $gymId ?>">
                    <input type="hidden" name="documento_o_token" value="<?= htmlspecialchars($learner['documento']) ?>">
                    
                    <?php if ($isInside): ?>
                        <button type="submit" class="flat-btn-action btn-checkout-action">
                            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                            <span>Registrar Mi Salida</span>
                        </button>
                    <?php else: ?>
                        <div class="confirm-entry-prompt">
                            <p class="confirm-prompt-title">¿Deseas ingresar al gimnasio?</p>
                            <p class="confirm-prompt-sub">Confirma tu acceso para registrar tu entrada y actualizar el aforo.</p>
                        </div>
                        <button type="submit" class="flat-btn-action btn-checkin-action">
                            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            <span>Sí, Registrar Mi Entrada</span>
                        </button>
                    <?php endif; ?>
                </form>

                <!-- Accesos directos a Carné e Historial -->
                <div class="user-quick-links mt-3">
                    <a href="/carne" class="btn-subtle-pill">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                        <span>Mi Carné Digital</span>
                    </a>
                    <a href="/historial" class="btn-subtle-pill">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        <span>Mi Historial</span>
                    </a>
                </div>

                <div class="text-center mt-3">
                    <a href="/logout-aprendiz" class="flat-link-subtle" onclick="try{localStorage.removeItem('gymsena_doc');}catch(e){}">
                        ¿No eres <?= htmlspecialchars(explode(' ', $learner['nombre'] ?? $learner['nombres'])[0]) ?>? Cambiar documento
                    </a>
                </div>
            </section>
            <script>
                try {
                    localStorage.setItem('gymsena_doc', <?= json_encode($learner['documento']) ?>);
                } catch(e) {}
            </script>
        <?php else: ?>

            <!-- Contenedor Dinámico para Usuario Reconocido desde Almacenamiento Local (Evita re-digitar cédula) -->
            <section class="flat-card flat-recognized-box hidden" id="storageRecognizedBox">
                <div class="flat-user-row">
                    <div class="flat-user-avatar" id="storageUserAvatar">U</div>
                    <div class="flat-user-meta">
                        <span class="flat-greeting" id="storageUserCategory">Aprendiz SENA</span>
                        <h3 id="storageUserName">Usuario</h3>
                        <p class="text-muted" id="storageUserMeta"></p>
                    </div>
                </div>

                <!-- Estado Actual en Sala -->
                <div class="current-status-strip" id="storageStatusStrip">
                    <span class="status-indicator-dot" id="storageStatusDot"></span>
                    <span id="storageStatusText"></span>
                </div>

                <form action="/procesar-acceso" method="POST" class="mt-3" id="storageAccessForm">
                    <input type="hidden" name="gym_id" value="<?= $gymId ?>">
                    <input type="hidden" name="documento_o_token" id="storageDocInput" value="">
                    
                    <div id="storageActionContainer">
                        <!-- Inyectado por JS: botón de Salida o prompt de entrada -->
                    </div>
                </form>

                <div class="user-quick-links mt-3">
                    <a href="/carne" class="btn-subtle-pill">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2zm-9 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 10H5v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1z"/></svg>
                        <span>Mi Carné Digital</span>
                    </a>
                    <a href="/historial" class="btn-subtle-pill">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        <span>Mi Historial</span>
                    </a>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn-text-link flat-link-subtle" onclick="forgetStoredUser()">
                        ¿No eres tú? Cambiar documento
                    </button>
                </div>
            </section>

            <div id="anonymousTabsContainer">

            <?php if (!empty($isNewUserDetected) || !empty($showRegistration)): ?>
                <!-- Notificación Automática: Usuario Nuevo Detectado -->
                <div class="auto-detect-alert">
                    <div class="auto-detect-icon">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div>
                        <strong>¡Bienvenido a GymSENA!</strong>
                        <p>No encontramos un registro previo con el documento <strong><?= htmlspecialchars($suggestedDoc) ?></strong>. Completa este breve formulario una sola vez para habilitar tu carné e ingreso.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pestañas Flat Sin Distracciones -->
            <div class="flat-tabs-row" role="tablist">
                <button type="button" 
                        class="flat-tab-item <?= empty($showRegistration) ? 'active' : '' ?>" 
                        id="tab-btn-ingreso"
                        onclick="switchFlatTab('tab-ingreso', this)" 
                        role="tab" 
                        aria-selected="<?= empty($showRegistration) ? 'true' : 'false' ?>">
                    <svg class="tab-svg-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Ingreso con Documento</span>
                </button>
                <button type="button" 
                        class="flat-tab-item <?= !empty($showRegistration) ? 'active' : '' ?>" 
                        id="tab-btn-registro"
                        onclick="switchFlatTab('tab-registro', this)" 
                        role="tab" 
                        aria-selected="<?= !empty($showRegistration) ? 'true' : 'false' ?>">
                    <svg class="tab-svg-icon" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    <span>Registro Rápido (Nuevo)</span>
                </button>
            </div>

            <!-- Pestaña 1: Ingreso Rápido con Auto-Detección Inteligente -->
            <div id="tab-ingreso" class="flat-tab-pane <?= empty($showRegistration) ? 'active' : '' ?>" role="tabpanel">
                <section class="flat-card">
                    <form action="/procesar-acceso" method="POST" id="formIngreso" onsubmit="return handleSmartAccessSubmit(event)">
                        <input type="hidden" name="gym_id" value="<?= $gymId ?>">

                        <div class="flat-form-group">
                            <label for="documento_o_token" class="flat-label">Número de Identificación</label>
                            <div class="smart-input-wrap">
                                <input type="text" 
                                       id="documento_o_token" 
                                       name="documento_o_token" 
                                       class="flat-input flat-input-lg input-no-spin" 
                                       placeholder="Digita tu número de documento" 
                                       value="<?= htmlspecialchars($suggestedDoc) ?>"
                                       required 
                                       autofocus 
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       autocomplete="off">
                                <div class="smart-detect-indicator" id="detectIndicator">
                                    <svg class="ui-icon icon-search-pulse" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                                </div>
                            </div>
                            
                            <!-- Caja de Feedback de Detección Dinámica -->
                            <div id="detectionFeedback" class="detection-feedback-box hidden">
                                <span id="feedbackText"></span>
                            </div>

                            <span class="field-hint form-hint-block">
                                El sistema detectará automáticamente si ya estás inscrito o si eres usuario nuevo.
                            </span>
                        </div>

                        <button type="submit" class="flat-btn-action mt-2" id="btnMarcarAcceso">
                            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                            <span id="btnMarcarTexto">Continuar / Marcar Acceso</span>
                        </button>
                    </form>

                    <div class="first-time-prompt mt-3 text-center">
                        <button type="button" class="btn-text-link" onclick="openRegistrationWithCurrentDoc()">
                            <svg class="ui-icon-sm" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            <span>¿Es tu primera visita al gimnasio? Regístrate aquí</span>
                        </button>
                    </div>
                </section>
            </div>

            <!-- Pestaña 2: Formulario de Auto-Inscripción Inclusivo (Aprendiz, Instructor, Funcionario, Invitado) -->
            <div id="tab-registro" class="flat-tab-pane <?= !empty($showRegistration) ? 'active' : '' ?>" role="tabpanel">
                <section class="flat-card">
                    <h3 class="flat-section-title">Inscripción Única a GymSENA</h3>
                    <p class="text-muted small mb-3">Diligencia tus datos una sola vez. Válido para toda la comunidad institucional.</p>

                    <!-- Selector de Vinculación Institucional con SVGs -->
                    <div class="user-category-selector" role="group" aria-label="Tipo de vinculación">
                        <button type="button" class="category-pill active" onclick="selectUserCategory('APRENDIZ', this)">
                            <svg class="cat-svg-icon" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                            <span>Aprendiz</span>
                        </button>
                        <button type="button" class="category-pill" onclick="selectUserCategory('INSTRUCTOR', this)">
                            <svg class="cat-svg-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                            <span>Instructor</span>
                        </button>
                        <button type="button" class="category-pill" onclick="selectUserCategory('FUNCIONARIO', this)">
                            <svg class="cat-svg-icon" viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.1 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                            <span>Funcionario</span>
                        </button>
                        <button type="button" class="category-pill" onclick="selectUserCategory('INVITADO', this)">
                            <svg class="cat-svg-icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            <span>Invitado</span>
                        </button>
                    </div>

                    <form action="/registrar-aprendiz" method="POST" id="formRegistro">
                        <input type="hidden" name="gym_id" value="<?= $gymId ?>">
                        <input type="hidden" name="tipo_usuario" id="input_tipo_usuario" value="APRENDIZ">

                        <!-- Fila Documento -->
                        <div class="flat-grid-2">
                            <div class="flat-form-group">
                                <label for="tipo_documento" class="flat-label">Tipo Doc.</label>
                                <select name="tipo_documento" id="tipo_documento" class="flat-input" required>
                                    <option value="CC">CC - Cédula de Ciudadanía</option>
                                    <option value="TI">TI - Tarjeta de Identidad</option>
                                    <option value="CE">CE - Cédula de Extranjería</option>
                                    <option value="PPT">PPT - Permiso Temporal</option>
                                </select>
                            </div>
                            <div class="flat-form-group">
                                <label for="documento" class="flat-label">Número de Documento</label>
                                <input type="text" 
                                       name="documento" 
                                       id="documento" 
                                       class="flat-input input-no-spin" 
                                       value="<?= htmlspecialchars($suggestedDoc) ?>" 
                                       required 
                                       placeholder="Sin puntos ni espacios" 
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       autocomplete="off">
                            </div>
                        </div>

                        <!-- Fila Nombres -->
                        <div class="flat-grid-2">
                            <div class="flat-form-group">
                                <label for="nombres" class="flat-label">Nombres</label>
                                <input type="text" name="nombres" id="nombres" class="flat-input" required placeholder="Tus nombres">
                            </div>
                            <div class="flat-form-group">
                                <label for="apellidos" class="flat-label">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos" class="flat-input" required placeholder="Tus apellidos">
                            </div>
                        </div>

                        <!-- Fila Dinámica según Rol -->
                        <div class="flat-grid-2" id="row-dynamic-fields">
                            <div class="flat-form-group" id="group-ficha">
                                <label for="ficha" id="label-ficha" class="flat-label">Nº de Ficha</label>
                                <input type="text" name="ficha" id="ficha" class="flat-input" required placeholder="Ej: 2694520" inputmode="numeric">
                            </div>
                            <div class="flat-form-group" id="group-programa">
                                <label for="programa_formacion" id="label-programa" class="flat-label">Programa de Formación</label>
                                <input type="text" name="programa_formacion" id="programa_formacion" class="flat-input" required placeholder="Ej: ADSO, Gestión...">
                            </div>
                        </div>

                        <!-- Fila Contacto -->
                        <div class="flat-grid-2">
                            <div class="flat-form-group">
                                <label for="correo" class="flat-label">Correo Institucional / Personal</label>
                                <input type="email" name="correo" id="correo" class="flat-input" placeholder="usuario@sena.edu.co">
                            </div>
                            <div class="flat-form-group">
                                <label for="telefono" class="flat-label">Celular / Teléfono</label>
                                <input type="tel" name="telefono" id="telefono" class="flat-input" placeholder="Ej: 3001234567" inputmode="tel">
                            </div>
                        </div>

                        <!-- Contacto de Emergencia (Obligatorio en Gimnasio) -->
                        <div class="flat-grid-2">
                            <div class="flat-form-group">
                                <label for="contacto_emergencia_nombre" class="flat-label">Contacto de Emergencia</label>
                                <input type="text" name="contacto_emergencia_nombre" id="contacto_emergencia_nombre" class="flat-input" required placeholder="Familiar o acudiente">
                            </div>
                            <div class="flat-form-group">
                                <label for="contacto_emergencia_telefono" class="flat-label">Teléfono de Emergencia</label>
                                <input type="tel" name="contacto_emergencia_telefono" id="contacto_emergencia_telefono" class="flat-input" required placeholder="Teléfono de contacto" inputmode="tel">
                            </div>
                        </div>

                        <!-- Consentimiento Ley 1581 de 2012 -->
                        <div class="flat-consent-box">
                            <label class="flat-checkbox-label">
                                <input type="checkbox" name="acepto_politica" value="1" required id="checkPolitica" checked>
                                <span>
                                    Autorizo el tratamiento de mis datos personales para fines de control de acceso y seguridad conforme a la <strong>Ley 1581 de 2012</strong>.
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="flat-btn-action mt-3">
                            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span>Completar Inscripción e Ingresar</span>
                        </button>
                    </form>
                </section>
            </div>
            </div> <!-- Fin #anonymousTabsContainer -->

        <?php endif; ?>

    <?php endif; ?>

</div>

<script>
function switchFlatTab(tabId, btn) {
    document.querySelectorAll('.flat-tab-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.flat-tab-item').forEach(el => {
        el.classList.remove('active');
        el.setAttribute('aria-selected', 'false');
    });

    const target = document.getElementById(tabId);
    if (target) {
        target.classList.add('active');
    }
    if (btn) {
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');
    }
}

function openRegistrationWithCurrentDoc() {
    const docInput = document.getElementById('documento_o_token');
    const regDocInput = document.getElementById('documento');
    if (docInput && regDocInput && docInput.value.trim()) {
        regDocInput.value = docInput.value.trim();
    }
    const regTabBtn = document.getElementById('tab-btn-registro');
    switchFlatTab('tab-registro', regTabBtn);
    const nombresInput = document.getElementById('nombres');
    if (nombresInput) {
        nombresInput.focus();
    }
}

function selectUserCategory(category, buttonEl) {
    document.querySelectorAll('.category-pill').forEach(el => el.classList.remove('active'));
    buttonEl.classList.add('active');

    const inputTipo = document.getElementById('input_tipo_usuario');
    if (inputTipo) {
        inputTipo.value = category;
    }

    const fichaInput = document.getElementById('ficha');
    const programaInput = document.getElementById('programa_formacion');
    const labelFicha = document.getElementById('label-ficha');
    const labelPrograma = document.getElementById('label-programa');
    const groupFicha = document.getElementById('group-ficha');

    if (category === 'APRENDIZ') {
        if (groupFicha) groupFicha.style.display = 'block';
        if (labelFicha) labelFicha.textContent = 'Nº de Ficha';
        if (fichaInput) {
            fichaInput.required = true;
            fichaInput.placeholder = 'Ej: 2694520';
            fichaInput.value = '';
        }
        if (labelPrograma) labelPrograma.textContent = 'Programa de Formación';
        if (programaInput) {
            programaInput.placeholder = 'Ej: ADSO, Gestión Empresarial...';
            programaInput.required = true;
        }
    } else if (category === 'INSTRUCTOR') {
        if (groupFicha) groupFicha.style.display = 'none';
        if (fichaInput) {
            fichaInput.required = false;
            fichaInput.value = 'INSTRUCTOR';
        }
        if (labelPrograma) labelPrograma.textContent = 'Área o Red de Conocimiento';
        if (programaInput) {
            programaInput.placeholder = 'Ej: Teleinformática, Salud, Bilingüismo...';
            programaInput.required = true;
        }
    } else if (category === 'FUNCIONARIO') {
        if (groupFicha) groupFicha.style.display = 'none';
        if (fichaInput) {
            fichaInput.required = false;
            fichaInput.value = 'FUNCIONARIO';
        }
        if (labelPrograma) labelPrograma.textContent = 'Dependencia / Coordinación';
        if (programaInput) {
            programaInput.placeholder = 'Ej: Coordinación Académica, Administrativa...';
            programaInput.required = true;
        }
    } else if (category === 'INVITADO') {
        if (groupFicha) groupFicha.style.display = 'none';
        if (fichaInput) {
            fichaInput.required = false;
            fichaInput.value = 'INVITADO';
        }
        if (labelPrograma) labelPrograma.textContent = 'Entidad / Motivo de Visita';
        if (programaInput) {
            programaInput.placeholder = 'Ej: Convenio Universidad, Proveedor...';
            programaInput.required = true;
        }
    }
}

// Auto-Detección Inteligente al Escanear o Digitar el Documento
let checkUserTimeout = null;
const docInput = document.getElementById('documento_o_token');
const feedbackBox = document.getElementById('detectionFeedback');
const feedbackText = document.getElementById('feedbackText');
const btnMarcar = document.getElementById('btnMarcarAcceso');
const btnMarcarTexto = document.getElementById('btnMarcarTexto');

if (docInput) {
    docInput.addEventListener('input', function() {
        const val = this.value.trim();
        clearTimeout(checkUserTimeout);
        if (val.length < 5) {
            if (feedbackBox) feedbackBox.classList.add('hidden');
            if (btnMarcarTexto) btnMarcarTexto.textContent = 'Continuar / Marcar Acceso';
            return;
        }

        checkUserTimeout = setTimeout(() => {
            fetch(`/api/check-user?doc=${encodeURIComponent(val)}&gym=<?= $gymId ?>`)
                .then(r => r.json())
                .then(data => {
                    if (!feedbackBox || !feedbackText) return;
                    feedbackBox.classList.remove('hidden', 'feedback-registered', 'feedback-new');

                    if (data.exists) {
                        feedbackBox.classList.add('feedback-registered');
                        const nombreCorto = data.nombres.split(' ')[0];
                        const accion = data.is_inside ? 'Registrar Salida' : 'Registrar Entrada';
                        feedbackText.innerHTML = `<strong>¡Hola, ${nombreCorto}!</strong> Registrado como ${data.tipo_usuario}. Listo para ${accion}.`;
                        if (btnMarcarTexto) btnMarcarTexto.textContent = `${accion} Inmediata`;
                    } else {
                        feedbackBox.classList.add('feedback-new');
                        feedbackText.innerHTML = `<strong>Usuario nuevo detectado:</strong> Completa tu registro rápido a continuación.`;
                        if (btnMarcarTexto) btnMarcarTexto.textContent = 'Continuar al Registro';
                    }
                })
                .catch(() => {});
        }, 350);
    });
}

function handleSmartAccessSubmit(e) {
    const docVal = docInput ? docInput.value.trim() : '';
    if (!docVal) return true;

    // Verificar si ya sabemos que es nuevo o podemos comprobar antes de saltar
    if (feedbackBox && feedbackBox.classList.contains('feedback-new')) {
        e.preventDefault();
        openRegistrationWithCurrentDoc();
        return false;
    }
    return true;
}

function forgetStoredUser() {
    try {
        localStorage.removeItem('gymsena_doc');
        localStorage.removeItem('gymsena_nombre');
    } catch(e) {}
    const box = document.getElementById('storageRecognizedBox');
    if (box) box.classList.add('hidden');
    const tabs = document.getElementById('anonymousTabsContainer');
    if (tabs) tabs.classList.remove('hidden');
    const tabIngreso = document.getElementById('tab-btn-ingreso');
    if (tabIngreso) switchFlatTab('tab-ingreso', tabIngreso);
}

function showStorageRecognizedUser(data, doc) {
    const box = document.getElementById('storageRecognizedBox');
    const tabs = document.getElementById('anonymousTabsContainer');
    if (!box) return;

    if (tabs) tabs.classList.add('hidden');
    box.classList.remove('hidden');

    const avatar = document.getElementById('storageUserAvatar');
    const nameEl = document.getElementById('storageUserName');
    const catEl = document.getElementById('storageUserCategory');
    const metaEl = document.getElementById('storageUserMeta');
    const docInputEl = document.getElementById('storageDocInput');
    const strip = document.getElementById('storageStatusStrip');
    const dot = document.getElementById('storageStatusDot');
    const statusText = document.getElementById('storageStatusText');
    const actionContainer = document.getElementById('storageActionContainer');

    if (avatar) avatar.textContent = (data.nombres || 'U').charAt(0).toUpperCase();
    if (nameEl) nameEl.textContent = `${data.nombres} ${data.apellidos}`;
    if (docInputEl) docInputEl.value = doc;

    let catBadge = 'Aprendiz SENA';
    if (data.tipo_usuario === 'INSTRUCTOR') catBadge = 'Instructor SENA';
    else if (data.tipo_usuario === 'FUNCIONARIO') catBadge = 'Funcionario / Colaborador';
    else if (data.tipo_usuario === 'INVITADO') catBadge = 'Invitado / Visitante';
    if (catEl) catEl.textContent = catBadge;

    if (metaEl) {
        if (data.tipo_usuario === 'APRENDIZ') {
            metaEl.textContent = `Ficha ${data.ficha || ''} • ${data.programa || ''}`;
        } else {
            metaEl.textContent = data.programa || 'Comunidad SENA';
        }
    }

    if (data.is_inside) {
        if (strip) strip.className = 'current-status-strip status-inside';
        if (dot) dot.className = 'status-indicator-dot dot-green';
        if (statusText) statusText.innerHTML = `<strong>Actualmente en sala</strong> • Ingresaste a las ${data.hora_entrada || ''}`;
        if (actionContainer) {
            actionContainer.innerHTML = `
                <button type="submit" class="flat-btn-action btn-checkout-action">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                    <span>Registrar Mi Salida</span>
                </button>
            `;
        }
    } else {
        if (strip) strip.className = 'current-status-strip status-outside';
        if (dot) dot.className = 'status-indicator-dot dot-gray';
        if (statusText) statusText.innerHTML = `<strong>Fuera del gimnasio</strong> • Listo para ingresar`;
        if (actionContainer) {
            actionContainer.innerHTML = `
                <div class="confirm-entry-prompt">
                    <p class="confirm-prompt-title">¿Deseas ingresar al gimnasio?</p>
                    <p class="confirm-prompt-sub">Confirma tu acceso para registrar tu entrada y actualizar el aforo.</p>
                </div>
                <button type="submit" class="flat-btn-action btn-checkin-action">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    <span>Sí, Registrar Mi Entrada</span>
                </button>
            `;
        }
    }
}

// Inicialización inteligente al cargar la pantalla de acceso
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('clear_storage=1')) {
        try {
            localStorage.removeItem('gymsena_doc');
            localStorage.removeItem('gymsena_nombre');
        } catch(e) {}
    }

    <?php if (!$learner): ?>
    let storedDoc = null;
    try {
        storedDoc = localStorage.getItem('gymsena_doc');
    } catch(e) {}

    if (storedDoc) {
        fetch(`/api/check-user?doc=${encodeURIComponent(storedDoc)}&gym=<?= $gymId ?>`)
            .then(r => r.json())
            .then(data => {
                if (data.exists) {
                    showStorageRecognizedUser(data, storedDoc);
                } else {
                    // Documento no encontrado en BD: limpiar y sugerir registro
                    try { localStorage.removeItem('gymsena_doc'); } catch(e) {}
                    const regTab = document.getElementById('tab-btn-registro');
                    if (regTab) switchFlatTab('tab-registro', regTab);
                }
            })
            .catch(() => {});
    } else {
        // Usuario nuevo en este dispositivo móvil: abrir directamente la pestaña de registro
        <?php if (empty($suggestedDoc) && empty($showRegistration)): ?>
        const regTab = document.getElementById('tab-btn-registro');
        if (regTab) {
            switchFlatTab('tab-registro', regTab);
        }
        <?php endif; ?>
    }
    <?php endif; ?>
});
</script>
