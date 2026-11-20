# Entregable 9: Roadmap de Evolución Tecnológica (GymSENA)

**Visión:** Evolucionar GymSENA desde un control de acceso ágil hacia una plataforma integral de salud, acondicionamiento físico y bienestar para la comunidad del SENA a nivel nacional.

---

## 1. Cronograma de Evolución por Fases

```mermaid
flowchart TD
    MVP["Fase 1: MVP Operativo (ACTUAL)
    - Acceso QR en 1 toque
    - Aforo en vivo
    - Historial y carné digital
    - Reportes Excel"] --> FASE2["Fase 2: Reservas de Turnos
    - Reserva de franja horaria (1h)
    - Cancelación de cupo
    - Lista de espera"]
    
    FASE2 --> FASE3["Fase 3: Notificaciones Web Push
    - Alerta de cupos liberados
    - Recordatorio 15 min antes del turno
    - Notificaciones institucionales"]

    FASE3 --> FASE4["Fase 4: Salud y Rutinas Deportivas
    - Ficha antropométrica (IMC, peso)
    - Rutinas por instructor SENA
    - Registro de marcas y progreso"]

    FASE4 --> FASE5["Fase 5: App Nativa Android (Kotlin)
    - Escaneo con Google ML Kit / CameraX
    - Geocerca GPS perimetral nativa
    - Experiencia offline avanzada"]

    FASE5 --> FASE6["Fase 6: Multi-Sede Nacional
    - Conexión de todos los gimnasios SENA
    - Dashboard para Dirección General
    - Carné único nacional"]
```

---

## 2. Detalle de las Fases Futuras

### Fase 2: Sistema de Reserva Previa de Turnos
* **Problema que resuelve:** Evitar desplazamientos innecesarios de aprendices en horas pico cuando el gimnasio está lleno.
* **Funcionalidad:**
  * Franjas configurables de 60 minutos con cupo máximo independiente (ej. 25 cupos por franja).
  * Reserva permitida con hasta 24 horas de anticipación desde la PWA.
  * Regla de cancelación: Liberación automática del cupo si el aprendiz no escanea el QR en los primeros 10 minutos de la franja.

### Fase 3: Notificaciones Web Push
* Integración del estándar **Web Push API** con **VAPID keys** en el Service Worker existente:
  * Aviso cuando el aforo baje del 80% para aprendices interesados.
  * Notificación informativa ante cambios imprevistos de horario o mantenimiento de máquinas.

### Fase 4: Módulo de Ficha Antropométrica y Rutinas
* Permite a los instructores de Cultura Física registrar:
  * Mediciones periódicas: Peso, estatura, porcentaje de grasa, Índice de Masa Corporal (IMC).
  * Planes de entrenamiento semanales (Fuerza, Hipertrofia, Resistencia, Movilidad).
  * Consulta interactiva de rutinas con animaciones de ejercicios directamente desde la PWA.

### Fase 5: App Android Nativa en Kotlin
* **Arquitectura:** MVVM (Model-View-ViewModel) + Jetpack Compose + Coroutines.
* **Integración API:** Consumo directo de los endpoints REST existentes de GymSENA (`/api/aforo`, `/procesar-acceso`, `/registrar-aprendiz`).
* **Seguridad Avanzada:**
  * Uso de **Google ML Kit Barcode Scanning** para leer el QR a 60 FPS sin retraso.
  * Verificación de ubicación vía **FusedLocationProviderClient** con validación criptográfica para garantizar que el dispositivo está dentro del perímetro del SENA.

### Fase 6: Despliegue Multi-Sede Nacional
* Aprovechando la arquitectura de datos existente con `sedes` y `gimnasios`:
  * Tablero consolidado para la Dirección General del SENA con estadísticas comparativas entre regionales (Antioquia, Bogotá, Valle, Atlántico, Santander, etc.).
  * Carné digital universal válido en cualquier gimnasio SENA del país.

