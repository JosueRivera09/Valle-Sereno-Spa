# Documento de Especificación de Requisitos de Software (SRS)
## Sistema de Gestión Integral — Valle Sereno Spa & Wellness

| Campo | Valor |
| :--- | :--- |
| **Nombre del sistema** | Valle Sereno Spa & Wellness — Sistema de Gestión Integral |
| **Tipo de documento** | Especificación de Requisitos de Software (SRS) + Informe de Estado del Proyecto |
| **Versión del documento** | 1.0 |
| **Fecha de emisión** | 18 de octubre de 2026 |
| **Estándar de referencia** | IEEE 830 / ISO/IEC/IEEE 29148 (adaptado) |
| **Autor** | Equipo de desarrollo — Proyecto académico |
| **Estado del proyecto** | Funcional en entorno local (XAMPP) · No listo para producción sin ajustes de configuración y seguridad |
| **Repositorio / Rama** | `main` · HEAD `7cfd563` (sin empujar) + cambios locales sin confirmar |

---

## Resumen Ejecutivo

**Valle Sereno Spa & Wellness** es una aplicación web monolítica en **PHP 8** con **MySQL/MariaDB**, desarrollada bajo **arquitectura N-Capas** y el patrón **MVC**, con un *Front Controller* único y control de acceso basado en roles (RBAC). Cubre la operación diaria de un spa: agenda terapéutica, expedientes de clientes, catálogo de servicios, caja y cobros, reportes contables, gestión de usuarios y respaldos de base de datos.

**Estado general:** la funcionalidad de negocio está **completa y operativa** en entorno de desarrollo. Las pruebas unitarias (15 aserciones) y la prueba de humo E2E (24 verificaciones) pasan al 100 %. El rediseño visual "Editorial Wellness" del panel administrativo está implementado y verificado.

**Preparación para despliegue:** **PARCIAL.** La aplicación funciona, pero **no está lista para producción** sin antes: externalizar credenciales/configuración, desactivar el modo desarrollo (`display_errors`), habilitar HTTPS y cookies seguras, aislar los respaldos del control de versiones, y añadir observabilidad, paginación y pruebas de CI. Ver la sección [§14 Brechas y Preparación para Despliegue](#14-brechas-y-preparación-para-despliegue).

---

## Tabla de Contenido

1. [Introducción](#1-introducción)
2. [Descripción General](#2-descripción-general)
3. [Requisitos Funcionales](#3-requisitos-funcionales)
4. [Requisitos No Funcionales](#4-requisitos-no-funcionales)
5. [Modelo de Datos](#5-modelo-de-datos)
6. [Arquitectura y Diseño](#6-arquitectura-y-diseño)
7. [Matriz de Roles y Permisos](#7-matriz-de-roles-y-permisos)
8. [Reglas de Negocio](#8-reglas-de-negocio)
9. [Interfaces del Sistema](#9-interfaces-del-sistema)
10. [Estado del Proyecto (Progreso)](#10-estado-del-proyecto-progreso)
11. [Cobertura de Pruebas](#11-cobertura-de-pruebas)
12. [Gestión de Configuración](#12-gestión-de-configuración)
13. [Riesgos](#13-riesgos)
14. [Brechas y Preparación para Despliegue](#14-brechas-y-preparación-para-despliegue)
15. [Checklist de Despliegue](#15-checklist-de-despliegue)
16. [Roadmap / Próximos Pasos](#16-roadmap--próximos-pasos)
17. [Anexos](#17-anexos)

---

## 1. Introducción

### 1.1 Propósito
Este documento especifica los requisitos funcionales y no funcionales del Sistema de Gestión Integral de Valle Sereno Spa & Wellness, y a la vez actúa como **informe de estado del proyecto**. Sirve como base para el desarrollo, las pruebas, la aceptación del cliente y el despliegue.

### 1.2 Alcance
El sistema automatiza la operación administrativa y comercial de un centro de spa:

- Autenticación y autorización por roles.
- Agenda terapéutica con validación de disponibilidad y anti-solapamiento.
- Gestión de expedientes de clientes.
- Catálogo de categorías y servicios/tratamientos.
- Gestión de personal terapéutico y horarios de disponibilidad.
- Caja diaria: apertura, cobros, arqueo y cierre.
- Reportes contables con exportación CSV y PDF.
- Administración de usuarios y roles.
- Respaldos y mantenimiento de base de datos.

**Fuera de alcance (v1.0):** facturación fiscal electrónica, pasarela de pagos en línea, portal del cliente, notificaciones por correo/SMS, aplicación móvil nativa, multi-sucursal, e internacionalización.

### 1.3 Audiencia
Equipo de desarrollo, docentes evaluadores, cliente (administración del spa) y responsable de despliegue.

### 1.4 Definiciones, Acrónimos y Abreviaturas

| Término | Definición |
| :--- | :--- |
| **MVC** | Modelo–Vista–Controlador |
| **RBAC** | Control de acceso basado en roles |
| **CSRF** | Cross-Site Request Forgery |
| **XSS** | Cross-Site Scripting |
| **SQLi** | Inyección SQL |
| **PDO** | PHP Data Objects |
| **BCRYPT** | Algoritmo de hashing de contraseñas |
| **Caja** | Sesión de operación diaria de cobros |
| **Arqueo** | Conteo y cuadre del efectivo al cierre |
| **Terapeuta** | Empleado que atiende servicios |
| **C$ / NIO** | Córdoba nicaragüense (moneda del sistema) |
| **RBAC-rol** | Administrador, Recepcionista, Terapeuta |

### 1.5 Referencias
- IEEE 830 — *Recommended Practice for Software Requirements Specifications*.
- ISO/IEC/IEEE 29148 — *Systems and software engineering — Life cycle processes — Requirements engineering*.
- `README.md` del repositorio.
- `database/schema.sql`, `database/seed_data.sql`, `database/fix_integridad.sql`, `database/fix_tildes.sql`.

---

## 2. Descripción General

### 2.1 Perspectiva del Producto
Aplicación web independiente (greenfield) para uso interno del spa. No se integra con sistemas externos en v1.0. Consume recursos estáticos desde CDN y se ejecuta sobre Apache + MySQL (XAMPP en desarrollo).

### 2.2 Funciones del Producto (resumen)

| # | Módulo | Función principal |
| :--- | :--- | :--- |
| M1 | Autenticación | Login por correo/usuario, bloqueo por intentos, sesiones |
| M2 | Dashboard | Métricas e indicadores del negocio |
| M3 | Citas | Agenda, agendamiento atómico, máquina de estados |
| M4 | Clientes | CRUD de expedientes e historial |
| M5 | Servicios | Catálogo de categorías/servicios y especialidades |
| M6 | Personal | Empleados y horarios de disponibilidad |
| M7 | Caja & Pagos | Apertura, cobros, arqueo, cierre, ticket |
| M8 | Reportes | Ventas por rango, ranking, CSV/PDF |
| M9 | Usuarios | CRUD de usuarios, roles, bloqueos, contraseñas |
| M10 | Respaldos | Generar/descargar/restaurar/optimizar BD |
| M11 | Login público | Vitrina visual con carrusel de servicios más vendidos |

### 2.3 Características de los Usuarios (Perfiles)

| Rol | Descripción | Nivel técnico | Frecuencia de uso |
| :--- | :--- | :--- | :--- |
| **Administrador** | Control total: finanzas, reportes, usuarios, respaldos | Medio | Diaria |
| **Recepcionista** | Agenda, clientes, cobros, caja | Medio | Diaria (jornada completa) |
| **Terapeuta** | Consulta sus citas asignadas y gestiona sus especialidades | Básico | Diaria |

### 2.4 Restricciones

- **CR-1:** PHP 8.x, MySQL/MariaDB, Apache (verificado en XAMPP).
- **CR-2:** Sin gestor de dependencias (Composer/NPM no incluidos); las librerías front-end se cargan por CDN.
- **CR-3:** Zona horaria fija `America/Managua` y moneda `C$` (NIO).
- **CR-4:** Idioma único: español.
- **CR-5:** El panel de administración se sirve bajo un único punto de entrada `index.php`.

### 2.5 Suposiciones y Dependencias

- El servidor dispone de acceso a Internet para cargar Bootstrap 5.3.3, Bootstrap Icons 1.11.3, Chart.js 4.4.2 y Google Fonts desde CDN.
- La base de datos `spa_db` se crea con `utf8mb4`/`utf8mb4_unicode_ci`.
- El usuario MySQL de la aplicación tiene permisos de lectura/escritura; la restauración de respaldos requiere permisos DDL.
- Los navegadores objetivo son versiones modernas (Chrome, Edge, Firefox).

---

## 3. Requisitos Funcionales

Convención: **DEBE** = obligatorio, **DEBERÍA** = recomendado, **PUEDE** = opcional.
Prioridad: **Alta / Media / Baja**.

### 3.1 M1 — Autenticación y Sesión

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-1.1 | El sistema DEBE permitir iniciar sesión con **correo institucional** o con el **usuario (alias)** más contraseña. | Alta |
| RF-1.2 | El sistema DEBE validar la contraseña con `password_verify()` (hashes BCRYPT). | Alta |
| RF-1.3 | Ante credenciales inválidas, el sistema DEBE devolver un **mensaje genérico** ("Correo o contraseña incorrectos") sin revelar si la cuenta existe (anti-enumeración). | Alta |
| RF-1.4 | Tras **3 intentos fallidos**, el sistema DEBE **bloquear temporalmente** la cuenta por **15 minutos**, liberándola automáticamente al expirar. | Alta |
| RF-1.5 | El sistema DEBE permitir a un Administrador **desbloquear** manualmente una cuenta. | Media |
| RF-1.6 | El sistema DEBE **regenerar el ID de sesión** al autenticar (anti *session fixation*). | Alta |
| RF-1.7 | El sistema DEBERÍA ofrecer "Recordar este dispositivo", extendiendo la vigencia de la cookie a **30 días**. | Media |
| RF-1.8 | El sistema DEBE **expirar la sesión por inactividad** a los **30 minutos** (30 días si se marcó "Recordar"). | Alta |
| RF-1.9 | El sistema DEBE permitir cerrar sesión destruyendo la sesión y su cookie. | Alta |
| RF-1.10 | El sistema DEBE redirigir según rol: Terapeuta → Citas; Administrador/Recepcionista → Dashboard; evitando bucles de redirección. | Alta |
| RF-1.11 | El sistema DEBE registrar el **último acceso** del usuario. | Baja |
| RF-1.12 | La interfaz de login DEBE permitir **ver/ocultar** la contraseña, validar en cliente por AJAX y mostrar alertas de bloqueo/soporte. | Media |

### 3.2 M2 — Dashboard

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-2.1 | El sistema DEBE mostrar métricas: citas de hoy (total, confirmadas, pendientes, completadas), terapeutas activos, clientes registrados, transacciones del día y servicios activos. | Alta |
| RF-2.2 | El sistema DEBE mostrar la **agenda del día** (citas de hoy). | Alta |
| RF-2.3 | El sistema DEBE mostrar los **servicios más solicitados/populares**. | Media |
| RF-2.4 | El sistema DEBE mostrar el **resumen de pagos por método** (Efectivo, Tarjeta, Transferencia). | Media |
| RF-2.5 | El sistema DEBE graficar los **ingresos de los últimos 7 días** (Chart.js). | Media |
| RF-2.6 | El sistema DEBE graficar la distribución de **citas por categoría**. | Baja |
| RF-2.7 | El Dashboard DEBE ser accesible solo a Administrador y Recepcionista. | Alta |

### 3.3 M3 — Citas / Agenda

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-3.1 | El sistema DEBE listar las citas; el **Terapeuta** solo ve **sus** citas asignadas. | Alta |
| RF-3.2 | El sistema DEBE permitir **agendar** citas (Administrador/Recepcionista) con cliente, terapeuta, fecha, hora y servicio o monto manual. | Alta |
| RF-3.3 | El **total y la hora de fin** DEBEN calcularse en el **servidor** a partir del servicio del catálogo (el cliente no controla el monto). | Alta |
| RF-3.4 | El sistema DEBE **impedir el solapamiento** de horarios del mismo terapeuta (fórmula estricta `inicio_nuevo < fin_existente AND fin_nuevo > inicio_existente`, permitiendo citas consecutivas). | Alta |
| RF-3.5 | El agendamiento DEBE ser **atómico** (transacción + `FOR UPDATE`) para evitar carreras (TOCTOU). | Alta |
| RF-3.6 | El sistema DEBE validar que la cita caiga **dentro del turno registrado** del terapeuta. | Alta |
| RF-3.7 | El sistema DEBE **rechazar fechas pasadas** y horarios con formato inválido. | Alta |
| RF-3.8 | Para hoy, el sistema DEBE rechazar horas de inicio ya transcurridas. | Media |
| RF-3.9 | El sistema DEBE aplicar la **máquina de estados**: `Pendiente → {Confirmada, Cancelada}`, `Confirmada → {Completada, Cancelada, Pendiente}`, `Completada → ∅`, `Cancelada → {Pendiente}`. | Alta |
| RF-3.10 | El sistema DEBE impedir **cancelar o reabrir** una cita ya **cobrada**. | Alta |
| RF-3.11 | El **Terapeuta** solo PUEDE marcar como **Confirmada** o **Completada** sus propias citas. | Alta |
| RF-3.12 | El sistema DEBE ofrecer un **endpoint de disponibilidad** que devuelva los terapeutas sin cruce de horario y dentro de su turno (opcionalmente filtrados por servicio). | Alta |
| RF-3.13 | El sistema DEBE permitir **registro express de cliente** desde el modal de citas (Admin/Recepción). | Media |
| RF-3.14 | El sistema DEBE persistir el **detalle del servicio** contratado (`cita_detalles`) para alimentar el Dashboard y Reportes. | Media |

### 3.4 M4 — Clientes

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-4.1 | El sistema DEBE listar, crear y actualizar expedientes de clientes (Admin/Recepción). | Alta |
| RF-4.2 | El sistema DEBE exigir **nombre completo, fecha de nacimiento y teléfono**; el correo DEBERÍA validarse por formato si se ingresa. | Alta |
| RF-4.3 | El sistema DEBE permitir **activar/inactivar** clientes. | Media |
| RF-4.4 | El sistema DEBE exponer el **historial de citas** de un cliente. | Media |
| RF-4.5 | Las operaciones de escritura DEBEN validar el token CSRF. | Alta |

### 3.5 M5 — Servicios & Categorías

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-5.1 | El sistema DEBE listar categorías y servicios. | Alta |
| RF-5.2 | El sistema DEBE validar: categoría válida, nombre obligatorio, costo ≥ 0 y duración > 0. | Alta |
| RF-5.3 | El sistema DEBE permitir crear/editar servicios y cambiar su estado (Admin/Recepción). | Alta |
| RF-5.4 | El sistema DEBE permitir crear **categorías** (Admin/Recepción). | Media |
| RF-5.5 | El **Terapeuta** PUEDE consultar el catálogo y **activar/desactivar sus propias especialidades** (relación empleado–servicio). | Media |
| RF-5.6 | El sistema DEBERÍA exhibir en el login público el **carrusel de servicios más vendidos** con nombre y descripción reales. | Baja |

### 3.6 M6 — Personal / Empleados

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-6.1 | El sistema DEBE listar, crear y actualizar empleados (Admin/Recepción). | Alta |
| RF-6.2 | El sistema DEBE validar nombre, teléfono, correo (formato) y cargo; el **correo DEBE ser único**. | Alta |
| RF-6.3 | El sistema DEBE gestionar **horarios de disponibilidad** por día de la semana (1=Lunes…7=Domingo) exigiendo `hora_fin > hora_inicio`. | Alta |
| RF-6.4 | El sistema DEBE permitir agregar, editar y eliminar horarios de disponibilidad. | Media |

### 3.7 M7 — Caja & Pagos

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-7.1 | El sistema DEBE permitir **abrir caja** con monto inicial (Admin/Recepción), impidiendo **doble apertura**. | Alta |
| RF-7.2 | El sistema NO DEBE permitir registrar cobros si la caja **no está abierta**. | Alta |
| RF-7.3 | El sistema DEBE registrar pagos (Efectivo/Tarjeta/Transferencia) asociados a una cita. | Alta |
| RF-7.4 | El sistema DEBE **impedir cobros duplicados** y montos que **excedan el saldo pendiente** de la cita. | Alta |
| RF-7.5 | El pago DEBE registrarse de forma **atómica** (bloqueo `FOR UPDATE`) y, al completarse, marcar la cita como **Completada** solo si estaba Pendiente/Confirmada. | Alta |
| RF-7.6 | El sistema DEBE permitir el **arqueo y cierre** de caja calculando el efectivo esperado (inicial + efectivo cobrado) y la diferencia, con **tolerancia de medio centavo**. | Alta |
| RF-7.7 | El sistema DEBE impedir **doble cierre** de caja. | Alta |
| RF-7.8 | El sistema DEBE mostrar el consolidado de **citas con saldo pendiente**. | Media |
| RF-7.9 | El sistema DEBE emitir un **ticket/comprobante** de pago. | Media |

### 3.8 M8 — Reportes & Analítica (solo Administrador)

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-8.1 | El sistema DEBE generar un reporte por **rango de fechas** con datos de pago, cliente, terapeuta y método. | Alta |
| RF-8.2 | El sistema DEBE calcular **resumen contable**: total recaudado, efectivo, tarjeta, transferencia y número de transacciones. | Alta |
| RF-8.3 | El sistema DEBE **exportar CSV** con BOM UTF-8 (compatibilidad Excel) y **neutralizar inyección de fórmulas** (`=`, `+`, `-`, `@`). | Alta |
| RF-8.4 | El sistema DEBE **exportar PDF** del reporte. | Media |
| RF-8.5 | El sistema DEBE mostrar **ventas mensuales** y **ranking de terapeutas**. | Media |
| RF-8.6 | El sistema DEBE validar y normalizar las fechas recibidas (formato `Y-m-d`, fallback a mes actual/hoy). | Alta |

### 3.9 M9 — Usuarios & Seguridad (solo Administrador)

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-9.1 | El sistema DEBE listar usuarios y catálogos (roles, empleados). | Alta |
| RF-9.2 | El sistema DEBE crear usuarios validando usuario (3–50), correo (formato y **único**) y rol. | Alta |
| RF-9.3 | El sistema DEBE aplicar la **política de contraseña**: mínimo 8 caracteres, al menos una letra y un número. | Alta |
| RF-9.4 | El sistema DEBE permitir actualizar usuario, correo, rol y estado. | Alta |
| RF-9.5 | El sistema DEBE permitir **resetear la contraseña** aplicando la política. | Alta |
| RF-9.6 | El sistema DEBE permitir **activar/inactivar** usuarios, impidiendo que el Administrador **desactive su propia cuenta**. | Alta |
| RF-9.7 | El sistema DEBE permitir **desbloquear** cuentas, reiniciando sus intentos fallidos a 0. | Media |
| RF-9.8 | Los endpoints DEBEN validar CSRF y responder JSON cuando la petición sea AJAX. | Alta |

### 3.10 M10 — Respaldos & Mantenimiento (solo Administrador)

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-10.1 | El sistema DEBE mostrar el estado de las tablas (filas, tamaño) y la lista de respaldos. | Media |
| RF-10.2 | El sistema DEBE **generar y guardar** un respaldo SQL en el servidor. | Alta |
| RF-10.3 | El sistema DEBE permitir **descargar** un respaldo. | Alta |
| RF-10.4 | El sistema DEBE permitir **restaurar** la BD subiendo un archivo `.sql` (validando extensión y contenido). | Alta |
| RF-10.5 | El sistema DEBE permitir **optimizar** tablas y **eliminar** respaldos. | Media |
| RF-10.6 | El directorio `storage/backups` DEBE estar **bloqueado ante acceso directo** (`.htaccess`). | Alta |

### 3.11 M11 — Login Público (vitrina)

| ID | Requisito | Prioridad |
| :--- | :--- | :--- |
| RF-11.1 | La pantalla de login DEBE mostrar un diseño *glassmorphism* con la paleta de marca. | Baja |
| RF-11.2 | El carrusel DEBE mostrar los **servicios más vendidos** obtenidos de la base de datos. | Baja |
| RF-11.3 | El bloque del carrusel DEBE ocultarse si no hay servicios. | Baja |
| RF-11.4 | La pantalla NO DEBE generar scroll innecesario en escritorio, pero DEBE conservarlo cuando la altura lo exija. | Baja |

---

## 4. Requisitos No Funcionales

### 4.1 Seguridad
- **RNF-1.1:** Todas las entradas DEBEN tratarse con consultas preparadas (PDO) para prevenir SQLi.
- **RNF-1.2:** Todas las salidas dinámicas DEBEN escaparse con `htmlspecialchars()` (anti-XSS) o la función `esc()` en JS.
- **RNF-1.3:** TODAS las peticiones POST DEBEN validar el token CSRF (`X-CSRF-TOKEN` o `csrf_token`), con comparación `hash_equals()`.
- **RNF-1.4:** Las contraseñas DEBEN almacenarse con `password_hash(PASSWORD_BCRYPT)`.
- **RNF-1.5:** El Front Controller DEBE usar **lista blanca** de controladores y solo invocar acciones **públicas**.
- **RNF-1.6:** La autorización DEBE aplicarse **en el servidor** por módulo y por acción (la ocultación en UI no es suficiente).
- **RNF-1.7:** Las cookies de sesión DEBEN ser `HttpOnly` y `SameSite=Lax`; en producción además `Secure` sobre HTTPS.
- **RNF-1.8:** Los respaldos y datos sensibles (PII, hashes) NO DEBEN ser accesibles directamente por URL.

### 4.2 Rendimiento
- **RNF-2.1:** El sistema DEBERÍA responder vistas principales en < 2 s en entorno local con datos de prueba.
- **RNF-2.2:** Los índices (`idx_citas_terapeuta_fecha`, `idx_pagos_cita_fecha`, etc.) DEBEN mantenerse para consultas frecuentes.
- **RNF-2.3:** *(Brecha conocida)* Los listados DEBERÍAN implementar **paginación** para grandes volúmenes.

### 4.3 Usabilidad
- **RNF-3.1:** La interfaz DEBE ser responsiva (escritorio, tablet, móvil) con Bootstrap 5.
- **RNF-3.2:** Las operaciones DEBEN dar retroalimentación al usuario (alertas flash, mensajes AJAX, spinners).
- **RNF-3.3:** La navegación DEBE adaptarse por rol y ser accesible desde cualquier módulo.
- **RNF-3.4:** La interfaz DEBE ofrecer confirmaciones en acciones destructivas.

### 4.4 Compatibilidad y Portabilidad
- **RNF-4.1:** El sistema DEBE operar en PHP 8.x + MySQL/MariaDB sobre Apache (XAMPP).
- **RNF-4.2:** El front-end DEBE soportarse en Chrome, Edge y Firefox actuales.
- **RNF-4.3:** El sistema DEBERÍA poder migrarse a un hosting LAMP estándar con cambios mínimos de configuración.

### 4.5 Disponibilidad e Integridad
- **RNF-5.1:** Las operaciones críticas (agendar, cobrar, abrir/cerrar caja) DEBEN ser transaccionales.
- **RNF-5.2:** El sistema DEBE preservar la integridad referencial mediante claves foráneas y `CHECK` constraints.

### 4.6 Mantenibilidad
- **RNF-6.1:** El código DEBE seguir la separación N-Capas (controller → service → model → database).
- **RNF-6.2:** El sistema DEBE contar con pruebas ejecutables sin dependencias externas.
- **RNF-6.3:** El sistema DEBERÍA contar con pipeline de integración continua (no implementado).

---

## 5. Modelo de Datos

**Motor:** InnoDB · **Charset:** `utf8mb4` / `utf8mb4_unicode_ci`. Esquema en `database/schema.sql` (12 tablas).

| Tabla | Propósito | Columnas clave |
| :--- | :--- | :--- |
| `roles` | Roles del sistema | `id`, `nombre` (UNIQUE), `descripcion` |
| `empleados` | Personal | `id`, `nombre_completo`, `telefono`, `correo` (UNIQUE), `cargo`, `especialidades`, `estado` |
| `usuarios` | Cuentas de acceso | `id`, `id_rol→roles`, `id_empleado→empleados`, `usuario` (UNIQUE), `email` (UNIQUE), `password_hash`, `estado` (activo/inactivo/bloqueado), `intentos_fallidos`, `bloqueado_hasta`, `ultimo_acceso` |
| `disponibilidad_empleados` | Turnos por día | `id`, `id_empleado→empleados`, `dia_semana` (1–7), `hora_inicio`, `hora_fin`, `estado` |
| `clientes` | Expedientes | `id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en` |
| `categorias` | Categorías de servicios | `id`, `nombre` (UNIQUE), `descripcion`, `estado` |
| `servicios` | Catálogo | `id`, `id_categoria→categorias`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado` |
| `citas` | Agenda | `id`, `id_cliente→clientes`, `id_empleado→empleados`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado` (Pendiente/Confirmada/Completada/Cancelada), `observaciones` |
| `cita_detalles` | Servicios por cita | `id`, `id_cita→citas`, `id_servicio→servicios`, `precio_aplicado`, `duracion_aplicada` |
| `pagos` | Cobros | `id`, `id_cita→citas`, `tipo_pago` (Efectivo/Tarjeta/Transferencia), `fecha_pago`, `monto`, `estado` (Pagado/Pendiente) |
| `empleado_servicios` | Especialidades | `id`, `id_empleado`, `id_servicio` (UNIQUE compuesto) |
| `caja_sesiones` | Caja diaria | `id`, `id_usuario→usuarios`, `fecha_apertura`, `monto_inicial`, `fecha_cierre`, montos por método, `diferencia_efectivo`, `estado` (abierta/cerrada) |

**Relaciones principales:**
```
roles 1──N usuarios N──1 empleados (opcional)
empleados 1──N disponibilidad_empleados
empleados 1──N citas N──1 clientes
citas 1──N cita_detalles N──1 servicios N──1 categorias
citas 1──N pagos
empleados N──N servicios (empleado_servicios)
usuarios 1──N caja_sesiones
```

---

## 6. Arquitectura y Diseño

### 6.1 Patrón Arquitectónico
**N-Capas + MVC** con *Front Controller* único (`index.php`).

```
Petición HTTP
   │
   ▼
index.php (Front Controller: lista blanca de controladores, sesión endurecida)
   │
   ▼
Controller  ──  valida rol (AuthHelper) + CSRF, orquesta
   │
   ▼
Service     ──  reglas de negocio y validaciones
   │
   ▼
Model       ──  acceso a datos (PDO)
   │
   ▼
Database    ──  conexión PDO Singleton (utf8mb4)
   │
   ▼
View (+ layout) ──  presentación HTML/CSS/JS
```

### 6.2 Estructura de Directorios

```
Valle-Sereno-Spa/
├── app/
│   ├── controllers/   (10)  Enrutamiento, permisos y flujo
│   ├── models/        (13)  Acceso a datos y entidades
│   ├── services/      (10)  Lógica de negocio y validaciones
│   ├── helpers/        (1)  AuthHelper (sesión, roles, CSRF)
│   └── views/         (13)  Interfaz (layout + módulos)
├── config/            (1)   Configuración global
├── database/          (4)   Esquema, seeds y parches
├── docs/              (1)   Documentación (este SRS)
├── public/            (2)   Recursos estáticos (CSS)
├── storage/backups/         Respaldos SQL (bloqueados por .htaccess)
├── tests/             (5)   Pruebas unitarias
├── .gitignore
├── index.php                Front Controller
└── README.md
```

### 6.3 Flujo de una Petición (ejemplo: cobrar)
1. `POST index.php?c=pagos&a=cobrar` con `csrf_token`.
2. `PagosController::__construct()` valida rol (Admin/Recepción).
3. `AuthHelper::requireCsrf()` valida el token.
4. `CajaService` verifica que la caja esté abierta.
5. `PagoService::procesarPago()` valida datos y delega en `PagoModel`.
6. `PagoModel::registrarPago()` abre transacción, bloquea la cita, revalida y persiste.
7. Respuesta JSON → refresco de UI.

### 6.4 Componentes Transversales
- **AuthHelper:** sesión, timeout de inactividad, roles, CSRF, detección AJAX, `homeUrl()`.
- **Layout `main.php`:** masthead editorial, parche global de `fetch()` (inyecta CSRF + `X-Requested-With`), helper `esc()`, flash messages, JS de menú móvil e inactividad.
- **Tema `public/css/spa-theme.css`:** paleta de marca y sistema visual "Editorial Wellness".

### 6.5 Dirección de Arte UI (Rediseño 2026)
Panel administrativo bajo el concepto **"Editorial Wellness / Ritual"**:
- Masthead superior *sticky* con wordmark serif y navegación con subrayado dorado.
- Encabezados de página editoriales (kicker dorado + título Playfair + regla).
- Franjas de métricas tipo *ledger*; tablas con reglas finas; listas rankeadas.
- Banda *hero* del dashboard con gradiente de marca y gráficos Chart.js.
- Responsive con menú *hamburguesa* (izquierda) en móvil.
- Paleta conservada: `--spa-primary #1e3d34`, `--spa-secondary #c5a059`, `--spa-accent #6b8e23`, `--spa-bg-soft #f7f9f7`.

---

## 7. Matriz de Roles y Permisos

Leyenda: **F** = Full (crear/editar/eliminar), **R** = Solo lectura/consulta, **R*** = lectura + acciones propias, **—** = sin acceso.

| Módulo / Acción | Administrador | Recepcionista | Terapeuta |
| :--- | :---: | :---: | :---: |
| Dashboard | F | F | — |
| Citas — ver | F | F | R* (solo propias) |
| Citas — agendar | ✔ | ✔ | — |
| Citas — cambiar estado | ✔ (todos) | ✔ (todos) | R* (solo Confirmar/Completar propias) |
| Citas — cancelar | ✔ | ✔ | — |
| Citas — cliente express | ✔ | ✔ | — |
| Clientes | F | F | — |
| Servicios — ver | R | R | R |
| Servicios — gestionar catálogo | ✔ | ✔ | — |
| Servicios — mis especialidades | — | — | ✔ |
| Personal / Empleados | F | F | — |
| Disponibilidad | F | F | — |
| Caja & Pagos | F | F | — |
| Reportes | F | — | — |
| Usuarios | F | — | — |
| Respaldos | F | — | — |
| Login público | público | público | público |

---

## 8. Reglas de Negocio

### RN-01 — Autenticación y bloqueo
- Máx. **3** intentos fallidos → bloqueo **15 min** con auto-liberación.
- Mensaje de error genérico (no revela existencia de la cuenta).
- Contraseñas: mín. **8** caracteres con al menos **una letra y un número**.
- Sesión: inactividad **30 min** (o **30 días** con "Recordar").

### RN-02 — Citas
- No se agendan citas en **fechas pasadas**; ni horas ya transcurridas en el día actual.
- El horario debe estar dentro de la **disponibilidad activa** del terapeuta para ese día de la semana.
- **Anti-solapamiento estricto** por terapeuta (permite citas consecutivas).
- **Total y duración** calculados en el servidor desde el catálogo; si no hay servicio, se exige monto manual > 0.
- **Máquina de estados** y bloqueo optimista; una cita **cobrada** no se cancela ni reabre.

### RN-03 — Cobros
- Solo con **caja abierta**.
- Sin **doble cobro** ni **exceso** sobre el saldo pendiente (tolerancia ±0.01).
- Al pagar una cita Pendiente/Confirmada, pasa a **Completada** (nunca revive Canceladas).

### RN-04 — Caja
- Una sola sesión **abierta** a la vez (anti doble apertura).
- **Efectivo esperado** = monto inicial + efectivo cobrado.
- **Diferencia** = efectivo contado − esperado; cuadre perfecto si |diferencia| < 0.005.
- Anti doble cierre.

### RN-05 — Clientes / Servicios / Empleados
- Cliente: nombre, fecha de nacimiento y teléfono obligatorios; correo válido si se ingresa.
- Servicio: categoría válida, nombre, costo ≥ 0, duración > 0.
- Empleado: nombre, teléfono, correo (formato y **único**) y cargo.
- Disponibilidad: `dia_semana` ∈ [1,7] y `hora_fin > hora_inicio`.

### RN-06 — Usuarios
- `usuario` y `email` **únicos**; política de contraseña; un Administrador **no puede desactivarse a sí mismo**.
- Desbloquear reinicia `intentos_fallidos = 0`.

### RN-07 — Reportes
- `inicio ≤ fin`; formato de fecha validado.
- Exportación CSV con BOM UTF-8 y **sanitización anti-fórmula**.

### RN-08 — Respaldos
- Solo Administrador, vía POST + CSRF. `storage/backups` bloqueado a acceso directo. Restauración limitada a archivos `.sql`.

---

## 9. Interfaces del Sistema

### 9.1 Interfaz de Usuario
- Web responsiva (Bootstrap 5.3.3 + CSS propio).
- Tipografías Playfair Display & Plus Jakarta Sans (Google Fonts).
- Gráficos con Chart.js 4.4.2.
- Iconografía Bootstrap Icons 1.11.3.

### 9.2 Interfaz de Programación (endpoints internos)
Front Controller: `index.php?c={controlador}&a={acción}`.

| Controlador | Acciones | Método | Rol mínimo |
| :--- | :--- | :--- | :--- |
| `auth` | `login`, `logout` | GET | público / autenticado |
| `auth` | `authenticate` | POST | público |
| `dashboard` | `index` | GET | Admin/Recepción |
| `citas` | `index` | GET | Todos |
| `citas` | `consultarDisponibilidad` | GET (JSON) | Todos |
| `citas` | `crearClienteExpress` | POST (JSON) | Admin/Recepción |
| `citas` | `guardar` | POST | Admin/Recepción |
| `citas` | `cambiarEstado` | POST (JSON) | Todos (regla por rol) |
| `clientes` | `index`, `historial` | GET | Admin/Recepción |
| `clientes` | `guardar`, `cambiarEstado` | POST (JSON) | Admin/Recepción |
| `servicios` | `index` | GET | Todos |
| `servicios` | `toggleEspecialidad` | POST (JSON) | Terapeuta |
| `servicios` | `guardar`, `guardarCategoria`, `cambiarEstado` | POST (JSON) | Admin/Recepción |
| `empleados` | `index` | GET | Admin/Recepción |
| `empleados` | `guardar`, `guardarHorario`, `eliminarHorario` | POST (JSON) | Admin/Recepción |
| `empleados` | `disponibilidad` | GET (JSON) | Admin/Recepción |
| `pagos` | `index`, `ticket` | GET | Admin/Recepción |
| `pagos` | `cobrar`, `abrirCaja`, `cerrarCaja` | POST (JSON) | Admin/Recepción |
| `reportes` | `index`, `exportarExcel`, `exportarPdf` | GET | Administrador |
| `reportes` | `filtrar` | GET (JSON) | Administrador |
| `usuarios` | `index`, `get` | GET | Administrador |
| `usuarios` | `guardar`, `actualizar`, `cambiarPassword`, `toggleEstado`, `desbloquear` | POST | Administrador |
| `respaldos` | `index`, `descargar` | GET | Administrador |
| `respaldos` | `generarLocal`, `optimizar`, `restaurar`, `eliminar` | POST | Administrador |

### 9.3 Interfaz de Base de Datos
- MySQL/MariaDB vía PDO con consultas preparadas.
- Configuración en `config/config.php` (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET=utf8mb4`).

### 9.4 Interfaz de Archivos
- **Entrada:** archivo `.sql` para restauración (multipart).
- **Salida:** dumps SQL de respaldo, exportaciones CSV, PDF de reportes y tickets.

---

## 10. Estado del Proyecto (Progreso)

### 10.1 Completitud por Módulo

| Módulo | Backend | Frontend | Pruebas | Estado |
| :--- | :---: | :---: | :---: | :--- |
| M1 Autenticación | ✅ | ✅ | ⚠️ manual | **Completo** |
| M2 Dashboard | ✅ | ✅ | — | **Completo** |
| M3 Citas | ✅ | ✅ | ⚠️ humo | **Completo** |
| M4 Clientes | ✅ | ✅ | ✅ unit | **Completo** |
| M5 Servicios | ✅ | ✅ | ✅ unit | **Completo** |
| M6 Personal | ✅ | ✅ | — | **Completo** |
| M7 Caja & Pagos | ✅ | ✅ | ✅ unit + humo | **Completo** |
| M8 Reportes | ✅ | ✅ | ✅ unit | **Completo** |
| M9 Usuarios | ✅ | ✅ | — | **Completo** |
| M10 Respaldos | ✅ | ✅ | — | **Completo** |
| M11 Login público | ✅ | ✅ | — | **Completo** |
| Rediseño UI "Editorial Wellness" | — | ✅ | — | **Completo** |
| Seguridad (Sprint 7) | ✅ | — | ⚠️ humo | **Completo** |

### 10.2 Métricas del Código

| Capa | Archivos | Líneas aprox. |
| :--- | :---: | ---: |
| Controllers | 10 | 879 |
| Models | 13 | 1 601 |
| Services | 10 | 1 040 |
| Views | 13 | 3 964 |
| Helpers | 1 | 152 |
| Config | 1 | 23 |
| Database (SQL) | 4 | 502 |
| Tests | 5 | 279 |
| **Total PHP** | **~53** | **~7 990** |

### 10.3 Historia de Desarrollo (Sprints)
Sprint 1 Servicios → Sprint 2 Clientes → Sprint 3 Caja/Pagos → Sprint 4 Reportes → Sprint 5 Permisos por rol/Terapeuta → Sprint 6 Usuarios/Respaldos/Ticket → **Sprint 7 Hardening** (CSRF global, máquina de estados, anti cobro doble, agendamiento transaccional, validación de turnos, seeds idempotentes, hashes válidos).

### 10.4 Trabajo Reciente (sin confirmar en Git)
- **Hardening de login:** mensaje genérico, bloqueo 15 min, "recordar dispositivo", política de contraseña, columna `usuarios.email` (nullable + UNIQUE).
- **Rediseño visual completo** del panel administrativo (masthead editorial, timeline de agenda, franjas de métricas, tablas finas).
- **Carrusel dinámico** de servicios más vendidos en el login.
- **Códigos de rol/permiso** verificados; menú izquierdo en móvil.
- **Corrección de codificación UTF-8** en datos (`database/fix_tildes.sql`).

---

## 11. Cobertura de Pruebas

### 11.1 Pruebas Unitarias (sin dependencias, XAMPP puro)
Ejecutables con `php tests/run_*.php`; devuelven código de salida ≠ 0 si fallan (compatibles con CI).

| Runner | Módulo | Aserciones | Resultado |
| :--- | :--- | :---: | :---: |
| `run_cliente_test.php` | Validación/registro de clientes | 4 | ✅ |
| `run_pago_test.php` | Validación de cobros | 4 | ✅ |
| `run_reporte_test.php` | Reportes por rango | 3 | ✅ |
| `run_servicio_test.php` | Catálogo de servicios | 4 | ✅ |
| **Total** | | **15** | **✅ 15/15** |

> `tests/ClienteTest.php` es una plantilla PHPUnit heredada (requiere Composer; no incluida).

### 11.2 Prueba de Humo E2E (externa al repositorio)
Script de extremo a extremo que verifica **24 comprobaciones** sobre el sistema en ejecución (login, dashboard, citas, disponibilidad, agendamiento, caja, cobros, reportes y permisos por rol). Resultado actual: **24 PASS / 0 FAIL**.

> Recomendación: incorporar este script a `tests/` del repositorio para trazabilidad.

### 11.3 Verificación de UI
Comprobación en navegador de las 9 páginas del panel + login: render del sistema editorial, navegación activa por rol, **0 errores de consola**, gráficos Chart.js inicializados y ausencia de desbordamiento horizontal en escritorio/tablet/móvil.

### 11.4 Brechas de Pruebas
- Sin pruebas unitarias para: Citas, Autenticación, Usuarios, Empleados, Respaldos.
- Sin pruebas automatizadas de UI (E2E de navegador) ni de regresión visual.
- Sin PHPUnit/CI configurados.

---

## 12. Gestión de Configuración

- **Control de versiones:** Git, rama `main` (más ramas `feature/*`).
- **Commits:** convencionales (`feat:`, `fix:`, `style:`, `chore:`, `docs:`, `test:`).
- **Estado:** HEAD `7cfd563` con **commits locales sin empujar** y **cambios sin confirmar** (rediseño, hardening de login, fix de tildes, ajustes de UI).
- **Assets externos (CDN):** Bootstrap 5.3.3, Bootstrap Icons 1.11.3, Chart.js 4.4.2, Google Fonts.
- **Versionado de caché CSS:** `spa-theme.css?v=2.5`.

---

## 13. Riesgos

| ID | Riesgo | Impacto | Prob. | Mitigación |
| :--- | :--- | :--- | :--- | :--- |
| R-1 | Credenciales de BD en código (`root`/sin clave) | Alto | Alta | Externalizar a `.env`/variables de entorno |
| R-2 | `display_errors = 1` en producción | Alto | Alta | Desactivar y registrar en log |
| R-3 | Dependencia de CDN (sin Internet → sin estilos/gráficos) | Medio | Media | Servir assets localmente |
| R-4 | Restauración de respaldos ejecuta SQL arbitrario | Alto | Baja | Solo Admin+CSRF; validar/limitar en producción |
| R-5 | Sin paginación en listados | Medio | Media | Implementar paginación server-side |
| R-6 | Sin auditoría de acciones | Medio | Media | Tabla de log de auditoría |
| R-7 | Sin pipeline CI | Medio | Media | Integrar runners en CI |
| R-8 | Respaldos versionados en Git | Medio | Media | Añadir `storage/backups/*.sql` a `.gitignore` |
| R-9 | Sin HTTPS ni cabeceras de seguridad | Alto | Alta | Forzar HTTPS, HSTS, CSP |
| R-10 | Cobertura de pruebas parcial | Medio | Media | Ampliar pruebas unitarias/E2E |

---

## 14. Brechas y Preparación para Despliegue

### 14.1 Veredicto
> **No listo para producción tal cual.** Es **desplegable en un entorno controlado/LAN o de demostración** tras ejecutar la configuración mínima. Requiere los ajustes de la sección 14.2 para un entorno público real.

### 14.2 Bloqueantes para Producción (obligatorios)

1. **Configuración externalizada:** mover credenciales de BD, `BASE_URL` y banderas de entorno a un archivo `.env` (o variables de entorno), fuera del control de versiones.
2. **Modo producción:** `display_errors = 0`, `log_errors = 1`, error_reporting adecuado.
3. **HTTPS + cookies seguras:** forzar TLS, activar `Secure` en la cookie de sesión, añadir HSTS y cabeceras de seguridad (CSP, X-Frame-Options, X-Content-Type-Options).
4. **Usuario de BD de mínimo privilegio** (no `root`) y contraseña robusta.
5. **Assets locales** o plan de contingencia para CDN.
6. **Respaldos fuera del repositorio** y con política de retención.

### 14.3 Mejoras Recomendadas (no bloqueantes)

7. Paginación y búsqueda server-side en listados.
8. Auditoría (log de acciones por usuario).
9. Ampliar pruebas (citas, auth, usuarios) y añadir CI.
10. Notificaciones (correo/recordatorios) y flujo de recuperación de contraseña self-service.
11. Números fiscales parametrizables (RUC, razón social) en lugar de fijos.
12. Rate limiting en login y rotación de token CSRF.
13. Índices/consultas revisados con `EXPLAIN` para volúmenes reales.

### 14.4 Fortalezas Actuales
- RBAC consistente y verificado en servidor por acción.
- Transacciones con bloqueo pesimista en las rutas críticas (citas, pagos, caja).
- CSRF global y salidas escapadas.
- Buen desempeño y UI coherente y responsiva.
- Seeds idempotentes, hashes válidos, datos íntegros.

---

## 15. Checklist de Despliegue

### Base de datos
- [ ] Crear BD `spa_db` con `utf8mb4_unicode_ci`.
- [ ] Importar `database/schema.sql` con `--default-character-set=utf8mb4`.
- [ ] Importar `database/seed_data.sql` (solo demo) con `--default-character-set=utf8mb4`.
- [ ] Aplicar `database/fix_integridad.sql` (BD heredadas) y `database/fix_tildes.sql` (si hubo corrupción de acentos).
- [ ] Crear usuario BD de mínimo privilegio.

### Aplicación
- [ ] Configurar `.env` (host, puerto, nombre, usuario, contraseña, `BASE_URL`).
- [ ] Desactivar `display_errors`; activar logging.
- [ ] Servir assets localmente (o validar CDN).
- [ ] Verificar `storage/backups/.htaccess` activo.
- [ ] Confirmar `mod_rewrite`/config de Apache según hosting.

### Seguridad
- [ ] Forzar HTTPS y cookies `Secure`.
- [ ] Cabeceras de seguridad (HSTS, CSP, X-Frame-Options).
- [ ] Rotar credenciales de prueba (`admin`/`Admin123*`).
- [ ] Revisar permisos de carpetas (`storage/`).
- [ ] Excluir respaldos del repositorio.

### Verificación
- [ ] Ejecutar `php tests/run_*.php` → 15/15.
- [ ] Ejecutar prueba de humo E2E → 24/24.
- [ ] Recorrer los módulos por los 3 roles.
- [ ] Probar en Chrome/Edge/Firefox (escritorio/tablet/móvil).

### Post-despliegue
- [ ] Configurar respaldos programados.
- [ ] Monitorizar logs de PHP/MySQL.
- [ ] Documentar procedimiento de respaldo/restauración.

---

## 16. Roadmap / Próximos Pasos

| Prioridad | Tarea | Módulo |
| :--- | :--- | :--- |
| Alta | Externalizar configuración a `.env` | Infra |
| Alta | Modo producción + HTTPS/cookies seguras | Infra |
| Alta | Ignorar respaldos en Git | Infra |
| Alta | Commitear y empujar trabajo pendiente | Gestión |
| Media | Paginación y búsqueda en listados | UI/Backend |
| Media | Auditoría de acciones | Seguridad |
| Media | Pruebas unitarias de Citas/Auth/Usuarios | QA |
| Media | Pipeline CI | DevOps |
| Media | Incorporar la prueba de humo al repo | QA |
| Baja | Recuperación de contraseña self-service | Auth |
| Baja | Notificaciones/recordatorios por correo | Comunicación |
| Baja | Parametrizar datos fiscales | Reportes |

---

## 17. Anexos

### Anexo A — Credenciales de Prueba (demo)
> ⚠️ Cambiar en producción.

| Rol | Usuario (alias) | Correo | Contraseña |
| :--- | :--- | :--- | :--- |
| Administrador | `admin` | `admin@vallesereno.com` | `Admin123*` |
| Recepcionista | `recepcion` | `recepcion@vallesereno.com` | `Admin123*` |
| Terapeuta | `terapeuta` | `terapeuta@vallesereno.com` | `Admin123*` |

### Anexo B — Estados del Sistema

**Cita:** `Pendiente`, `Confirmada`, `Completada`, `Cancelada`.
**Pago:** `Pagado`, `Pendiente`.
**Caja:** `abierta`, `cerrada`.
**Usuario:** `activo`, `inactivo`, `bloqueado`.
**Cliente/Servicio/Empleado/Categoría:** `activo`, `inactivo`.
**Tipo de pago:** `Efectivo`, `Tarjeta`, `Transferencia`.

### Anexo C — Glosario
- **Expediente de cliente:** registro con datos personales e historial de citas.
- **Especialidad:** relación empleado–servicio que un terapeuta puede ofrecer.
- **Arqueo:** conteo del efectivo físico al cierre de caja.
- **Ticket:** comprobante de un pago registrado.

### Anexo D — Convenciones de Requisitos
- **RF-xx** Requisito Funcional · **RNF-x.x** Requisito No Funcional · **RN-xx** Regla de Negocio.

---

*Fin del documento. Versión 1.0 — sujeta a revisión con el cliente y el equipo.*
