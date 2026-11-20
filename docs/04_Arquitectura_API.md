# Entregable 4: Arquitectura del Sistema y Diseño de la API

## 1. Diagrama de Arquitectura de Software

```mermaid
flowchart TD
    subgraph Cliente [Capa de Presentación / Clientes]
        PWA["PWA Mobile-First (HTML5 + CSS3 + Service Worker)"]
        KOTLIN["Futura App Android Nativa (Kotlin + Retrofit)"]
    end

    subgraph Proxy [Capa de Entrada y Servidor Web]
        NGINX["Servidor Web (Nginx / Apache / PHP CLI)"]
        ROUTER["Front Controller (public/index.php) + Router"]
    end

    subgraph Backend [Capa de Lógica y Dominio (PHP 8.3)]
        AUTH["Auth & Sessions Middleware"]
        CTRL["Controladores (Access, Learner, Monitor, Admin)"]
        SERV["Capa de Servicios (AccessService, ReportService)"]
        MODELS["Modelos de Dominio (Gym, Learner, Attendance, User)"]
    end

    subgraph Persistencia [Capa de Datos y Almacenamiento]
        DB_DRIVER["Database Factory (PDO MySQL / Fallback Driver)"]
        MYSQL[("MySQL 8.0+ / MariaDB 10.4+")]
        FALLBACK[("Local Data Store JSON (Zero-Crash Mode)")]
    end

    PWA -->|HTTPS / JSON / Form| NGINX
    KOTLIN -->|REST API / JSON| NGINX
    NGINX --> ROUTER
    ROUTER --> AUTH
    AUTH --> CTRL
    CTRL --> SERV
    SERV --> MODELS
    MODELS --> DB_DRIVER
    DB_DRIVER -->|Producción| MYSQL
    DB_DRIVER -->|Resiliencia/Dev| FALLBACK
```

---

## 2. Especificación de Endpoints de la API REST

### 2.1 Consulta de Aforo en Vivo
* **Endpoint:** `GET /api/aforo?gym={gym_id}`
* **Descripción:** Devuelve el estado instantáneo de ocupación del gimnasio para alimentar widgets y pantallas en tiempo real.
* **Respuesta Exitosa (200 OK):**
```json
{
  "gimnasio_id": 1,
  "nombre": "Gimnasio Principal - Bienestar al Aprendiz",
  "capacidad_maxima": 30,
  "ocupacion_actual": 12,
  "cupos_disponibles": 18,
  "porcentaje_ocupacion": 40,
  "estado": "ABIERTO"
}
```

### 2.2 Procesar Acceso por QR (Check-in / Check-out)
* **Endpoint:** `POST /procesar-acceso`
* **Headers:** `Content-Type: application/json` o `application/x-www-form-urlencoded`
* **Payload:**
```json
{
  "gym_id": 1,
  "documento_o_token": "1020304050"
}
```
* **Respuesta Entrada (200 OK):**
```json
{
  "success": true,
  "type": "CHECKIN",
  "action": "ENTRADA",
  "learner": {
    "id": 1,
    "nombres": "Carlos Andrés",
    "ficha": "2694520"
  },
  "attendance": {
    "hora_entrada": "07:15:00"
  },
  "aforo": {
    "ocupacion_actual": 13,
    "cupos_disponibles": 17
  },
  "message": "¡Bienvenido a entrenar, Carlos Andrés! Entrada registrada con éxito."
}
```

---

## 3. Hoja de Ruta Hacia la App Android Nativa en Kotlin

El backend desarrollado desacopla totalmente la lógica de negocio mediante endpoints JSON. Para migrar a una aplicación Android nativa en **Kotlin**:
1. **Networking:** Uso de la librería **Retrofit 2** + **OkHttp** con interceptor para tokens de sesión.
2. **Arquitectura Android:** Patrón **MVVM (Model-View-ViewModel)** con Jetpack Compose y Coroutines / Flow.
3. **Escáner QR Nativo:** Integración de **Google ML Kit Barcode Scanning** o **CameraX** para lectura instantánea del QR sin abrir el navegador.
4. **Geolocalización:** Integración con **FusedLocationProviderClient** de Google Play Services para verificar coordenadas exactas frente a la sede SENA antes de enviar la petición de Check-in.

