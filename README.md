# GymSENA - PWA de Control de Acceso, Aforo y Servicios

Sistema Web Progresivo (PWA) de alto rendimiento para el control de acceso, aforo en tiempo real y trazabilidad del gimnasio institucional del **SENA (Servicio Nacional de Aprendizaje)**.

Desarrollado bajo los lineamientos metodológicos de proyectos formativos SENA (Línea ADSO / Tecnologías de la Información) y en estricto cumplimiento de la **Ley 1581 de 2012 (Protección de Datos Personales en Colombia)**.

---

## 1. Características Principales

* **Acceso Express por QR y Auto-Detección Inteligente:**
  * Si el usuario ya está registrado: auto-reconocimiento en su celular y registro inmediato de **Entrada** (*Check-in*) o **Salida** (*Check-out*) con 1 solo toque.
  * Si es nuevo en el sistema: el sistema lo detecta de forma instantánea y lo guía a la inscripción rápida con su documento pre-cargado.
* **Aforo Dinámico en Tiempo Real:** Contador visual de personas dentro vs. capacidad máxima permitida con cálculo de cupos disponibles y actualización en vivo.
* **Panel del Entrenador con Timbre de Entrada Sonoro:**
  * Alerta acústica institucional sintetizada vía **Web Audio API** (100% offline, sin archivos de audio externos) cada vez que una persona ingresa al gimnasio.
  * Botón para activar o silenciar el sonido y notificación flotante en pantalla.
  * Asistencia manual asistida para aprendices sin celular o sin batería (sin flechas en el campo de cédula).
  * Listado en vivo de aprendices en sala con opción de registrar salida inmediata.
* **Carné Digital Institucional (ISO ID-1):** Carné virtual con código QR vectorial SVG individual, chip de seguridad holográfico y compatibilidad con impresión física (`@media print`).
* **Cartel Oficial de Entrada Imprimible (`/cartel-qr`):** Plantilla oficial en formato A4 / Carta con código QR vectorial de alta definición para fijar en torniquetes y accesos.
* **Dashboard Administrativo (Bienestar al Aprendiz):**
  * Curva horaria de horas pico.
  * Ranking de programas de formación y dependencias con mayor afluencia.
  * Exportación de reportes a **Excel (.CSV con codificación BOM UTF-8)** por rango de fechas.
  * Carga masiva de usuarios desde archivo CSV.
* **PWA Offline-First:** Service Worker con precarga de assets, caché Stale-While-Revalidate y manifiesto PWA para instalación en celulares y computadores.
* **Cierre Automático Nocturno (Cron):** Script para crontab que auto-cierra visitas no registradas al final del horario oficial.

---

## 2. Arquitectura de Software

El sistema implementa una **Arquitectura en Capas Limpia (Clean Layered Architecture)** sobre PHP 8.1+ y Frontend Vanilla (HTML5 semántico, CSS3 modular y JavaScript nativo), sin dependencias externas:

```
GymSena/
├── config/                  # Configuración general, operativa y de BD
│   ├── app.php
│   └── database.php
├── cron/                    # Tareas programadas del sistema operativo
│   └── daily_close.php      # Daemon de cierre nocturno automático
├── database/                # Scripts de base de datos relacional
│   ├── schema.sql           # DDL: Modelo multi-gimnasio en 3FN
│   └── seed.sql             # DML: Datos semilla para pruebas
├── docs/                    # Documentación técnica y sustentación
├── public/                  # Raíz pública del servidor web
│   ├── .htaccess            # Configuración de rewrite y seguridad Apache
│   ├── css/                 # Hojas de estilo desacopladas (Cero CSS en PHP)
│   │   ├── style.css        # Estilos globales y PWA
│   │   └── poster.css       # Hoja de estilo para cartel imprimible
│   ├── icons/               # Iconografía vectorial SVG institucional
│   ├── images/              # Fondos y códigos QR vectoriales
│   ├── js/app.js            # PWA, Service Worker y polling
│   ├── manifest.json        # Web App Manifest PWA
│   ├── sw.js                # Service Worker para funcionamiento offline
│   └── index.php            # Front Controller único y enrutador
├── scripts/                 # Utilidades CLI
│   └── install.php          # Instalador automatizado para producción
├── src/                     # Código fuente de la aplicación
│   ├── Core/                # Núcleo (Router, Database, Request, Response, Auth)
│   ├── Controllers/         # Controladores HTTP
│   ├── Models/              # Modelos de datos y repositorios
│   ├── Services/            # Lógica de negocio y reglas operativas
│   └── Views/               # Vistas HTML5 por módulos y layouts
└── tests/                   # Suite de pruebas automatizadas
    └── run_tests.php        # 19 pruebas de integración y negocio
```

---

## 3. Requisitos y Despliegue en Producción

### Requisitos del Servidor:
* **PHP 8.1 o superior** (extensiones: `pdo`, `pdo_mysql`, `mbstring`, `json`, `session`).
* **Servidor Web:** Apache (con `mod_rewrite` habilitado), Nginx o servidor integrado PHP.
* **Base de Datos:** MySQL 8.0+ / MariaDB 10.4+ (incluye modo Fallback JSON si MySQL no está disponible).

### Instalación Rápida:

1. Clonar el repositorio y copiar las variables de entorno:
   ```bash
   cp .env.example .env
   ```
2. Ejecutar el instalador automatizado:
   ```bash
   php scripts/install.php
   ```
3. Ejecutar la suite de pruebas:
   ```bash
   php tests/run_tests.php
   # Resultado esperado: 19 de 19 pruebas superadas con éxito (100%)
   ```

### Servidor Web Local (Pruebas):
```bash
php -S 0.0.0.0:8080 -t public
```

### Configuración del Cierre Automático (Crontab):
Para cerrar automáticamente las visitas abiertas al finalizar la jornada (Lunes a Viernes a las 23:59):
```bash
59 23 * * 1-5 /usr/bin/php /ruta/al/proyecto/cron/daily_close.php >> /var/log/gymsena_cron.log 2>&1
```

---

## 4. Credenciales de Acceso

| Rol | Correo / Identificación | Contraseña | Funciones |
|---|---|---|---|
| **Administrador (Bienestar)** | `admin@sena.edu.co` | `admin123` | Reportes, analítica, descarga Excel, aforo |
| **Monitor / Entrenador** | `monitor@sena.edu.co` | `monitor123` | Control en vivo con timbre sonoro, cartel QR |
| **Aprendiz de Muestra** | Documento: `1020304050` | *Sin clave* | Acceso QR, carné digital, historial |

---

## 5. Marco Legal (Ley 1581 de 2012)
El sistema cumple estrictamente con la **Ley Estatutaria 1581 de 2012** de la República de Colombia. Cada registro almacena:
* Confirmación explícita del consentimiento.
* Dirección IP y User Agent del dispositivo.
* Marca de tiempo exacta (*timestamp*).
* Versión de la política institucional aceptada.
