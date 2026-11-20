# Memoria del Proyecto: GymSENA - Control de Acceso y Servicios

**Última actualización:** 2026-11-16
**Rol:** Ingeniero de Software Senior, Arquitecto de Soluciones y Analista de Sistemas.
**Objetivo General:** PWA para control de acceso, aforo, trazabilidad y gestión de servicios del gimnasio del SENA para aprendices, monitores y administradores (Bienestar al Aprendiz).

---

## 1. Estado de los Entregables

| # | Entregable | Estado | Notas |
|---|------------|--------|-------|
| 1 | Documento de requisitos (SRS) + Historias de Usuario | **COMPLETADO Y APROBADO** | Base formal del proyecto SENA en `docs/`. |
| 2 | Casos de uso principales | **COMPLETADO** | Especificaciones IEEE/UML completas. |
| 3 | Modelo de datos (ERD + Script SQL Multi-gimnasio) | **COMPLETADO** | En `database/schema.sql`, `seed.sql` y `docs/03_Modelo_Datos_ERD.md`. |
| 4 | Arquitectura del sistema y diseño de API REST | **COMPLETADO** | En `docs/04_Arquitectura_API.md` y estructura en capas. |
| 5 | Especificación y diseño de pantallas (HTML5/CSS3) | **COMPLETADO** | Mobile-First en `src/Views/` y `public/css/style.css`. |
| 6 | Código base MVP paso a paso (PHP + PWA) | **COMPLETADO** | Backend PHP 8.3 + Frontend PWA + Service Worker. |
| 7 | Plan de pruebas | **COMPLETADO (100% PASS)** | 19/19 pruebas superadas en `tests/run_tests.php` y `docs/07_Plan_de_Pruebas.md`. |
| 8 | Plan de implementación y despliegue en el SENA | **COMPLETADO** | Cartelería de QR, capacitación y piloto en `docs/08_Plan_Implementacion_SENA.md`. |
| 9 | Roadmap y evolución futura | **COMPLETADO** | App Android Kotlin, reservas y multi-gimnasio en `docs/09_Roadmap_Evolucion.md`. |

---

## 2. Stack Tecnológico y Principios UI/UX
- **Backend**: PHP 8.3 (Arquitectura limpia en capas, MVC sin frameworks externos pesados).
- **Base de Datos**: MySQL / MariaDB con PDO (`pdo_mysql`) + adaptador fallback para desarrollo.
- **Frontend UI/UX**:
  - **Identidad Oficial SENA**: Integración de los vectores oficiales del SENA en 3 variantes (`logo-sena-oficial.svg`, `logo-sena-verde.svg`, `logo-sena-blanco.svg`) en cabeceras, carné digital, barra lateral y pantallas de autenticación.
  - **UI Limpia sin Flechas en Inputs de Documento**:
    - Se eliminaron por completo las flechas/spinners nativos de aumento y disminución (`::-webkit-inner-spin-button` y `appearance: textfield`) en todos los campos de cédula tanto en `/acceso` como en `/monitor`, configurando teclado numérico móvil limpio (`inputmode="numeric"`, `pattern="[0-9]*"`).
  - **Timbre Sonoro de Entrada en el Panel del Entrenador (`/monitor`)**:
    - Síntesis de sonido armónico institucional con **Web Audio API** (Do5 a Sol5), 100% offline sin necesidad de descargar archivos de audio externos.
    - Suena automáticamente cuando un aprendiz o usuario registra su entrada a sala.
    - Incluye botón de control para silenciar o activar el sonido según preferencia del monitor, y notificación tipo toast en pantalla.
  - **Arquitectura Offline-First con Service Worker (`sw.js`)**:
    - Estrategia *Stale-While-Revalidate* para assets estáticos y *Network-First* con fallback en caché para navegación.
    - El carné digital y las pantallas críticas quedan disponibles aún si el Wi-Fi institucional o los datos móviles fallan.
  - **Auto-Detección Inteligente al Escanear el QR de Entrada**:
    - Si el usuario ya está registrado en el sistema: la app lo reconoce de inmediato ("¡Hola, Carlos!"), valida su estado de presencia (`🟢 En Sala` / `⚪ Fuera de Sala`) y permite el registro de entrada/salida inmediato con 1 solo toque, recordando su celular mediante cookie/token seguro.
    - Si es usuario nuevo: el sistema detecta que el documento no existe y lo guía de manera fluida y automática al formulario de auto-registro rápido, pre-llenando su número de documento para que no deba digitarlo dos veces. Al completar sus datos, queda registrado e ingresa de inmediato.
    - Endpoint API reactivo `/api/check-user` para validación instantánea y feedback en tiempo real.
  - **Carné Digital de Alta Seguridad (ISO ID-1 / CR80)**:
    - Rediseñado en formato de tarjeta física institucional elegante con colores oficiales SENA Verde (`#39A900`) y SENA Azul (`#00324D`).
    - Chip holográfico vectorial, avatar con anillo metálico degradado, badge según rol (Aprendiz, Instructor, Funcionario, Invitado) y contacto de emergencia.
    - Código QR vectorial SVG generado 100% localmente y offline (`/images/qr_learner_{doc}.svg`) sin dependencias de CDNs externas.
    - Reglas de impresión `@media print` integradas para plastificación o expedición física.
  - **Mi Historial Deportivo Ejecutivo (`/historial`)**:
    - Encabezado con perfil de usuario y estado activo.
    - 4 tarjetas KPI con acentos cromáticos: *Sesiones Totales*, *Horas Acumuladas*, *Promedio por Sesión* y *Nivel de Constancia SENA*.
    - Tabla cronológica detallada con fecha, sellos de entrada/salida, micro-barras de progreso de duración en minutos y badges de estado.
    - Estado vacío ilustrado y motivacional si el usuario aún no tiene asistencias.
  - **Separación Estricta de CSS y Plantillas PHP (Cero CSS en PHP)**:
    - Cero etiquetas `<style>` dentro de las plantillas en `src/Views/`. Todos los estilos consolidados en `public/css/style.css` y `public/css/poster.css`.
  - **Cero Emoticones / Reemplazo Total por Iconografía Vectorial SVG**:
    - Cero emojis en todas las vistas; iconografía vectorial profesional de alta definición en [icons/](file:///home/anonimous/Escritorio/GymSena/public/icons/).
  - **Fondos Dinámicos y Atmosféricos con Alta Transparencia**:
    - Login: Hero inmersivo con `bg-gym-dark.jpg` y degradado institucional suave (`linear-gradient(145deg, rgba(0, 50, 77, 0.90), rgba(57, 169, 0, 0.65))`).
    - Vista Usuario PWA (`/acceso`, `/historial`, `/carne`): Fondo texturizado `bg-gym-training.jpg` con velo translúcido tenue (`rgba(248, 250, 252, 0.965)` a `0.975`) tipo marca de agua limpia.
    - Panel del Entrenador (`/monitor`): Fondo de sala moderna `bg-gym-modern.jpg` con tarjetas elevadas, logo oficial verde en aside bar y legibilidad de alto contraste.
    - Panel Administrador (`/admin`): Textura ejecutiva y gráfico plano de barras para horas pico.
    - Transición suave por CSS (`transition: background 0.4s ease`) entre vistas.
  - **Eliminación Total de Cuellos de Botella en el Acceso**:
    - **Cartel QR Único para Portería (`/cartel-qr`)**: Plantilla oficial imprimible en tamaño Carta/A4 con vector QR SVG 100% offline (sin CDN externa), instrucciones en 3 pasos y soporte multi-rol.
    - **Acceso Inclusivo Multi-Rol**: Soporte nativo para Aprendices, Instructores, Funcionarios/Trabajadores e Invitados. Los instructores y administrativos ya no son bloqueados pidiendo "Ficha".
    - **Visibilidad Inmediata del Estado**: La app detecta si el usuario está dentro (`🟢 En Sala` -> Botón "Registrar Salida") o fuera (`⚪ Fuera de Sala` -> Botón "Registrar Entrada") para marcar asistencia en un solo toque (< 2s).
  - **Login Split-Screen Profesional & Creativo**:
    - **Lado Izquierdo (Hero Institucional)**: Mensaje de bienvenida *"Entrena tu mejor versión. Gestiona con excelencia"*, logotipo oficial blanco, 3 pilares de alto valor (Acceso <3s, Aforo en vivo, Ley 1581) y punto de pulso en vivo.
    - **Lado Derecho (Formulario Flat)**: Selector de roles ergonómico (`Monitor` vs `Administrador`), iconos SVG institucionales, alternador de visibilidad de contraseña, botones de prueba rápida (*quick chips*) y retorno a vista de aprendiz.
  - **Panel del Entrenador con Barra Lateral Fija (Aside Bar)**: Logo oficial blanco, perfil de funcionario, reloj digital en tiempo real y selector modular.
  - **Métricas y Reportes (`/admin`) Rediseñado al 100% en Flat Design 2.0:** Tarjetas planas con bordes de 1px, filtros de fecha en línea, 4 cajas de KPIs limpias, gráfico plano de barras para horas pico y lista de ranking de programas con barras de progreso verdes planas.
  - Cumplimiento de las 10 Heurísticas de Nielsen y Leyes de Fitts/Hick:
    1. *Visibilidad del estado:* Medidor de aforo dinámico en vivo con indicador de pulso y porcentajes.
    2. *Coincidencia con el mundo real:* Metáfora de carné digital institucional, vocabulario deportivo y académico.
    3. *Control y libertad:* Opciones claras para alternar aprendices, cerrar sesión o corregir ingresos.
    4. *Consistencia:* Componentes unificados, botones institucionales consistentes.
    5. *Prevención de errores:* Validación en cliente, teclados numéricos (`inputmode="numeric"`), alertas proactivas.
    6. *Reconocimiento antes que recuerdo:* Auto-identificación del aprendiz por token, datos precargados.
    7. *Flexibilidad y eficiencia:* Acceso express de 1 toque + ingreso asistido para el entrenador.
    8. *Diseño estético y minimalista:* Jerarquía tipográfica con *Plus Jakarta Sans*, eliminación de sobrecarga cognitiva.
    9. *Diagnóstico y recuperación:* Mensajes de error cordiales y propositivos (ej. horarios y aforo lleno).
    10. *Ayuda y documentación:* Microcopias claras y guía rápida de bolsillo para el monitor.
  - **Ley de Fitts:** Botón gigante de acción principal (*CTA*) en la zona ergonómica del pulgar + Barra inferior móvil.
  - **Ley de Hick:** Reducción de opciones en pantalla mediante pestañas limpias de navegación.
- **PWA**: Web App Manifest (`manifest.json`) + Service Worker (`sw.js`) con soporte offline e instalación nativa.
4. **Mecanismo Antifraude para MVP**: Validación de Horario Operativo + Geofencing perimetral liviano por GPS del navegador (con radio de tolerancia, ej. 150m) + Token diario/rotativo o secreto institucional para evitar compartir enlaces directos por WhatsApp.
5. **Cumplimiento Legal**: Ley 1581 de 2012 (Habeas Data Colombia) mediante checkbox explícito y registro de timestamp + versión de política.

---

## 3. Registro de Preguntas Clave al Usuario
- [ ] P1: Infraestructura de despliegue disponible en el Centro SENA.
- [ ] P2: Método antifraude preferido para el MVP (QR estático con geolocalización vs QR dinámico en pantalla).
- [ ] P3: Validación de estado del aprendiz (¿integración con SofiaPlus/Zajuna o lista local/Excel?).
- [ ] P4: Franjas horarias y aforo estimado por turno.
- [ ] P5: Identificación y persistencia en dispositivos compartidos.

