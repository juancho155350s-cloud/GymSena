<div class="split-auth-layout">
    
    <!-- COLUMNA IZQUIERDA: BIENVENIDA INSTITUCIONAL & BRANDING SENA -->
    <aside class="split-auth-hero">
        <div class="split-hero-overlay"></div>
        <div class="split-hero-content">
            
            <!-- Barra Superior Institucional -->
            <div class="split-hero-brand">
                <img src="/icons/logo-sena-blanco.svg" alt="SENA Colombia" class="split-hero-logo">
                <div class="split-hero-brand-text">
                    <span class="split-hero-title">GymSENA</span>
                    <span class="split-hero-badge">Bienestar al Aprendiz</span>
                </div>
            </div>

            <!-- Bloque Central de Motivación y Bienvenida -->
            <div class="split-hero-welcome">
                <span class="split-hero-tag">SISTEMA INTEGRAL DE ACTIVIDAD FÍSICA</span>
                <h1 class="split-hero-headline">
                    Entrena tu mejor versión.<br>
                    <span>Gestiona con excelencia.</span>
                </h1>
                <p class="split-hero-description">
                    Bienvenido al portal institucional del gimnasio SENA. Una plataforma diseñada para salvaguardar tu bienestar, optimizar el aforo y promover hábitos de vida saludable en la comunidad educativa.
                </p>

                <!-- 3 Pilares Institucionales de Alto Impacto (Con Iconos SVG) -->
                <div class="split-hero-features">
                    <div class="split-feature-item">
                        <div class="split-feature-icon">
                            <svg class="feature-svg" viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                        </div>
                        <div class="split-feature-text">
                            <strong>Acceso Inmediato &bull; Menos de 3 Segundos</strong>
                            <span>Validación fluida mediante carné digital o código QR institucional.</span>
                        </div>
                    </div>
                    <div class="split-feature-item">
                        <div class="split-feature-icon">
                            <svg class="feature-svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                        </div>
                        <div class="split-feature-text">
                            <strong>Aforo en Tiempo Real</strong>
                            <span>Monitoreo continuo de capacidad para seguridad y ergonomía en sala.</span>
                        </div>
                    </div>
                    <div class="split-feature-item">
                        <div class="split-feature-icon">
                            <svg class="feature-svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                        </div>
                        <div class="split-feature-text">
                            <strong>Habeas Data & Ley 1581 de 2012</strong>
                            <span>Consentimiento informado y protección rigurosa de datos personales.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de la Columna Izquierda con Estado Operativo -->
            <div class="split-hero-footer">
                <div class="split-status-indicator">
                    <span class="status-pulse-dot"></span>
                    <span>Sistema Operativo &bull; Centro de Formación SENA</span>
                </div>
                <div class="split-version-badge">v2.4 LTS</div>
            </div>

        </div>
    </aside>

    <!-- COLUMNA DERECHA: FORMULARIO DE ACCESO PROFESIONAL (FLAT DESIGN) -->
    <main class="split-auth-form-side">
        <div class="split-form-container">
            
            <!-- Encabezado para pantallas móviles o compactas -->
            <div class="split-mobile-header">
                <img src="/icons/logo-sena-verde.svg" alt="SENA" class="split-mobile-logo">
                <h2>GymSENA</h2>
            </div>

            <div class="split-form-heading">
                <h2 class="split-form-title">Portal de Funcionarios</h2>
                <p class="split-form-subtitle">Selecciona tu perfil e ingresa tus credenciales autorizadas.</p>
            </div>

            <!-- Selector de Roles Ergonómico (Nielsen #4 & #7 con Iconos SVG) -->
            <div class="role-selector-pills" role="tablist">
                <button type="button" 
                        class="role-pill active" 
                        id="role-monitor-btn"
                        onclick="selectRole('monitor')"
                        role="tab"
                        aria-selected="true">
                    <svg class="role-pill-svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    <span class="role-pill-label">Pasante / Entrenador</span>
                </button>
                <button type="button" 
                        class="role-pill" 
                        id="role-admin-btn"
                        onclick="selectRole('admin')"
                        role="tab"
                        aria-selected="false">
                    <svg class="role-pill-svg" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                    <span class="role-pill-label">Instructor / Admin</span>
                </button>
            </div>

            <?php if (!empty($error)): ?>
                <div class="flat-alert-error" role="alert">
                    <svg class="ui-icon alert-svg-icon" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                    <span>Credenciales no válidas o acceso no autorizado para esta sede.</span>
                </div>
            <?php endif; ?>

            <!-- Formulario Principal -->
            <form action="/login" method="POST" class="auth-clean-form" id="loginForm">
                
                <div class="form-field-group">
                    <label for="email" class="field-label">Correo Electrónico Institucional</label>
                    <div class="field-input-wrapper">
                        <svg class="field-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="field-input" 
                               placeholder="usuario@sena.edu.co" 
                               value="monitor@sena.edu.co"
                               autocomplete="username"
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="password" class="field-label">Contraseña Institucional</label>
                        <span class="field-hint">Clave única asignada</span>
                    </div>
                    <div class="field-input-wrapper">
                        <svg class="field-icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="field-input" 
                               placeholder="••••••••" 
                               value="monitor123"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="field-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Mostrar u ocultar contraseña">
                            <span id="eyeIcon">
                                <svg class="field-eye-svg" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-sena-btn" id="submitBtn">
                    <span>Iniciar Sesión en el Panel</span>
                    <svg viewBox="0 0 24 24" class="btn-arrow"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                </button>
            </form>

            <!-- Acceso Rápido para Demostración / Pruebas (Flat Chips con SVG) -->
            <div class="quick-demo-section">
                <span class="quick-demo-title">Relleno rápido de prueba:</span>
                <div class="quick-demo-chips">
                    <button type="button" class="demo-chip-btn" onclick="selectRole('monitor')">
                        <svg class="demo-chip-svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        <span class="chip-label">Monitor Demo</span>
                    </button>
                    <button type="button" class="demo-chip-btn" onclick="selectRole('admin')">
                        <svg class="demo-chip-svg" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                        <span class="chip-label">Admin Demo</span>
                    </button>
                </div>
            </div>

            <!-- Pie de Navegación hacia Aprendices -->
            <div class="split-form-footer">
                <a href="/acceso" class="return-access-link">
                    <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                    <span>Ir al escaneo de entrada y carné digital</span>
                </a>
            </div>

        </div>
    </main>

</div>

<script>
function selectRole(role) {
    const monitorBtn = document.getElementById('role-monitor-btn');
    const adminBtn = document.getElementById('role-admin-btn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (role === 'admin') {
        monitorBtn.classList.remove('active');
        monitorBtn.setAttribute('aria-selected', 'false');
        adminBtn.classList.add('active');
        adminBtn.setAttribute('aria-selected', 'true');
        
        emailInput.value = 'admin@sena.edu.co';
        passwordInput.value = 'admin123';
    } else {
        adminBtn.classList.remove('active');
        adminBtn.setAttribute('aria-selected', 'false');
        monitorBtn.classList.add('active');
        monitorBtn.setAttribute('aria-selected', 'true');
        
        emailInput.value = 'monitor@sena.edu.co';
        passwordInput.value = 'monitor123';
    }
    emailInput.focus();
}

const svgEyeOpen = '<svg class="field-eye-svg" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>';
const svgEyeOff = '<svg class="field-eye-svg" viewBox="0 0 24 24"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.44-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>';

function togglePasswordVisibility() {
    const passInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passInput.type === 'password') {
        passInput.type = 'text';
        eyeIcon.innerHTML = svgEyeOff;
    } else {
        passInput.type = 'password';
        eyeIcon.innerHTML = svgEyeOpen;
    }
}
</script>
