# Entregable 7: Plan de Pruebas de Software - GymSENA

**Proyecto:** GymSENA - Control de Acceso y Servicios del Gimnasio  
**Fase Metodológica:** Fase de Evaluación y Pruebas (ADSO - SENA)  
**Versión:** 1.0.0-MVP  

---

## 1. Objetivos del Plan de Pruebas

El objetivo de este plan de pruebas es garantizar la calidad, seguridad, usabilidad y rendimiento del sistema GymSENA previo a su despliegue operativo en el Centro de Formación. Se verifica que:
1. El flujo de registro por QR opere en **menos de 3 segundos** sin errores de concurrencia.
2. Se cumpla la normatividad de la **Ley 1581 de 2012** (auditoría del consentimiento).
3. La lógica de aforo y cierre automático diario prevenga inconsistencias en los contadores.
4. Los roles de seguridad (Aprendiz, Monitor, Administrador) mantengan estricto control de acceso.

---

## 2. Tipos de Pruebas Ejecutadas

| Tipo de Prueba | Enfoque | Herramienta / Método |
|---|---|---|
| **Pruebas Unitarias y de Servicios** | Validación de métodos de negocio (cálculo de aforo, control de horarios, duración de permanencia). | PHP CLI / Test Suite interna (`tests/run_tests.php`). |
| **Pruebas de Integración** | Interacción entre Controladores, Capa de Servicios y Capa de Persistencia (Base de Datos). | Pruebas de extremo a extremo simuladas en CLI y HTTP. |
| **Pruebas de Seguridad y Control de Acceso** | Verificación de hashing seguro (bcrypt), protección de rutas protegidas y bloqueo de acceso no autorizado. | Intentos forzados de acceso a `/admin` y `/monitor` sin sesión. |
| **Pruebas de Usabilidad y Compatibilidad PWA** | Carga móvil en pantallas de 320px a 1080px, funcionamiento del Service Worker e instalación como app de escritorio/celular. | Chrome DevTools (Lighthouse, Auditoría PWA, Device Emulation). |
| **Pruebas de Resiliencia (Cero Caídas)** | Comportamiento del sistema ante caídas del motor MySQL principal (conmutación automática a almacenamiento local). | Desconexión intencional de base de datos. |

---

## 3. Matriz de Casos de Prueba (UAT y Funcionales)

| ID Caso | Módulo | Descripción / Escenario | Entrada | Resultado Esperado | Estado |
|---|---|---|---|---|---|
| **CP-01** | Acceso QR | Aprendiz no registrado escanea QR | Documento nuevo `1099887766` | Redirige al formulario de inscripción con Ley 1581 | **SUPERADO** |
| **CP-02** | Acceso QR | Inscripción sin marcar casilla Ley 1581 | Formulario completo sin checkbox | Sistema bloquea envío y exige autorización expresa | **SUPERADO** |
| **CP-03** | Acceso QR | Aprendiz registrado escanea sin visita abierta | Documento `1020304050` | Marca ENTRADA, descuenta cupo de aforo y muestra hora | **SUPERADO** |
| **CP-04** | Acceso QR | Aprendiz registrado escanea teniendo visita abierta | Mismo documento `1020304050` | Marca SALIDA, calcula duración y libera cupo de aforo | **SUPERADO** |
| **CP-05** | Aforo | Ingreso con gimnasio al 100% de capacidad | Aforo: 30/30 | Bloquea entrada y muestra aviso de espera | **SUPERADO** |
| **CP-06** | Horarios | Intento de registro en fin de semana (Sábado/Domingo) | Petición día Sábado | Bloquea acceso con mensaje de horario hábil | **SUPERADO** |
| **CP-07** | Horarios | Intento de registro fuera de jornada (ej. 21:30) | Petición a las 21:30 | Bloquea acceso y muestra franja oficial (06:00 - 20:00) | **SUPERADO** |
| **CP-08** | Monitor | Registro manual de aprendiz sin teléfono | Monitor digita documento | Registra entrada/salida correctamente | **SUPERADO** |
| **CP-09** | Daemon | Tarea nocturna a las 23:59:00 | Ejecución `cron/daily_close.php` | Cierra visitas abiertas y marca bandera `cierre_automatico` | **SUPERADO** |
| **CP-10** | Admin | Exportación de reporte a Excel | Rango de fechas `2026-11-01` a `hoy` | Descarga archivo CSV con formato tabular y UTF-8 BOM | **SUPERADO** |
| **CP-11** | Admin | Modificación dinámica de capacidad de aforo | Cambio de capacidad a 40 | Se actualiza en tiempo real en la pantalla de aprendices | **SUPERADO** |
| **CP-12** | Seguridad | Intento de login con clave errónea | Correo admin con clave incorrecta | Deniega acceso y emite alerta de credenciales inválidas | **SUPERADO** |
| **CP-13** | Seguridad | Acceso directo por URL a `/admin` sin sesión | Petición sin cookies de auth | Redirige de inmediato al login con código de error | **SUPERADO** |

---

## 4. Resultados de la Ejecución Automatizada

La ejecución del script `tests/run_tests.php` arrojó una tasa de éxito del **100% (13/13 pruebas exitosas)**, certificando que los flujos transaccionales cumplen con la especificación técnica.

