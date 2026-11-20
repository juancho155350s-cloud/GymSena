/**
 * GymSENA - Lógica de Cliente y PWA
 */

// 1. Registro del Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => console.log('GymSENA ServiceWorker registrado:', reg.scope))
            .catch((err) => console.warn('Fallo registro ServiceWorker:', err));
    });
}

// 2. Gestión de Banner de Instalación PWA (A2HS)
let deferredPrompt;
const installBanner = document.getElementById('pwa-install-banner');
const installBtn = document.getElementById('btn-install-pwa');

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (installBanner) {
        installBanner.classList.remove('hidden');
    }
});

if (installBtn) {
    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            console.log('Aprendiz instaló la PWA');
        }
        deferredPrompt = null;
        if (installBanner) {
            installBanner.classList.add('hidden');
        }
    });
}

// 3. Polling en Vivo del Aforo (cada 20 segundos para no saturar)
function actualizarAforoEnVivo() {
    const aforoCountEl = document.querySelector('.aforo-count');
    const progressBarEl = document.querySelector('.progress-bar-fill');
    const aforoFooterSlots = document.querySelector('.aforo-footer strong');

    if (!aforoCountEl) return;

    fetch('/api/aforo?gym=1')
        .then(res => res.json())
        .then(data => {
            if (data && typeof data.ocupacion_actual !== 'undefined') {
                aforoCountEl.textContent = data.ocupacion_actual;
                if (progressBarEl) {
                    progressBarEl.style.width = Math.min(100, data.porcentaje_ocupacion) + '%';
                }
                if (aforoFooterSlots) {
                    aforoFooterSlots.textContent = data.cupos_disponibles;
                }
            }
        })
        .catch(err => console.debug('Aforo poller:', err));
}

setInterval(actualizarAforoEnVivo, 20000);

