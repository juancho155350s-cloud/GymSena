# Entregable 3: Modelo de Datos - GymSENA

## 1. Diagrama Entidad-Relación (ERD) Multi-Gimnasio

```mermaid
erDiagram
    SEDES ||--o{ GIMNASIOS : "alberga"
    SEDES ||--o{ USUARIOS_SISTEMA : "pertenece"
    SEDES ||--o{ APRENDICES : "matricula"
    
    GIMNASIOS ||--o{ HORARIOS_OPERACION : "establece"
    GIMNASIOS ||--o{ ASISTENCIAS : "registra"
    GIMNASIOS ||--o{ NOVEDADES_SANCIONES : "ocurre_en"
    GIMNASIOS ||--o{ USUARIOS_SISTEMA : "asigna_monitor"

    APRENDICES ||--o{ ASISTENCIAS : "realiza"
    APRENDICES ||--o{ CONSENTIMIENTOS_LEY1581 : "otorga"
    APRENDICES ||--o{ NOVEDADES_SANCIONES : "recibe"

    USUARIOS_SISTEMA ||--o{ NOVEDADES_SANCIONES : "reporta"

    SEDES {
        int id PK
        string codigo_centro UK
        string nombre
        string regional
        string direccion
        string estado
    }

    GIMNASIOS {
        int id PK
        int sede_id FK
        string nombre
        int capacidad_maxima
        decimal latitud
        decimal longitud
        int radio_metros_tolerancia
        string pin_seguridad_diario
        string estado
    }

    HORARIOS_OPERACION {
        int id PK
        int gimnasio_id FK
        string dia_semana
        time hora_apertura
        time hora_cierre
        boolean activo
    }

    USUARIOS_SISTEMA {
        int id PK
        int sede_id FK
        int gimnasio_id FK
        string documento UK
        string nombre_completo
        string email UK
        string password_hash
        string rol
        string estado
    }

    APRENDICES {
        int id PK
        int sede_id FK
        string tipo_documento
        string documento UK
        string nombres
        string apellidos
        string ficha
        string programa_formacion
        string correo
        string telefono
        string contacto_emergencia_nombre
        string contacto_emergencia_telefono
        string estado_formacion
        string token_acceso
        boolean activo
    }

    CONSENTIMIENTOS_LEY1581 {
        int id PK
        int aprendiz_id FK
        boolean acepto_politica
        string ip_registro
        string version_politica
        timestamp fecha_hora_consentimiento
    }

    ASISTENCIAS {
        int id PK
        int gimnasio_id FK
        int aprendiz_id FK
        date fecha
        time hora_entrada
        time hora_salida
        int duracion_minutos
        string metodo_ingreso
        boolean cierre_automatico
        string observaciones
    }

    NOVEDADES_SANCIONES {
        int id PK
        int aprendiz_id FK
        int gimnasio_id FK
        int usuario_sistema_id FK
        string tipo
        text descripcion
        boolean bloquea_ingreso
        date fecha_inicio
        date fecha_fin
        boolean resuelto
    }
```

## 2. Diccionario de Datos Sintético

1. **`sedes`**: Permite escalar el sistema a cualquier Centro de Formación del SENA a nivel nacional (soporte multi-tenant).
2. **`gimnasios`**: Configuración de salas y recintos deportivos por centro, con control de capacidad máxima y coordenadas GPS antifraude.
3. **`horarios_operacion`**: Restricción de franjas horarias de Lunes a Viernes para bloqueo preventivo de registros fuera de jornada.
4. **`aprendices`**: Información sociodemográfica, académica y de emergencia del aprendiz. Almacena `token_acceso` para persistencia en el dispositivo móvil.
5. **`consentimientos_ley1581`**: Auditoría estricta de la autorización expresa para el tratamiento de datos personales en Colombia.
6. **`asistencias`**: Núcleo transaccional de ingresos y salidas con cálculo automático de permanencia y bandera de cierre automático diario.

