# 🌿 Valle Sereno - Spa & Wellness

Sistema integral para la gestión administrativa, agenda terapéutica, control de cabinas, clientes y facturación de un centro de Spa y Bienestar. Proyecto desarrollado con fines académicos implementando **Arquitectura N-Capas**, el patrón **MVC (Modelo - Vista - Controlador)** y la metodología ágil **Scrum**.

---

## 🚀 Tecnologías Utilizadas

- **Lenguaje:** PHP 8.x
- **Base de Datos:** MySQL / MariaDB (Entorno XAMPP)
- **Frontend & UI:** HTML5, CSS3 personalizado (*paleta orgánica y relajante*), Bootstrap 5.3 y Bootstrap Icons
- **Tipografías:** *Playfair Display* & *Plus Jakarta Sans* (Google Fonts)
- **Conectividad:** PDO (PHP Data Objects) con consultas preparadas y contraseñas hasheadas con `BCRYPT`
- **Peticiones Asíncronas:** AJAX / Fetch API para respuesta fluida sin recargas bruscas

---

## 🏛️ Arquitectura del Sistema (N-Capas & MVC)

```text
Valle-Sereno-Spa/
├── app/
│   ├── controllers/         # [Capa Controlador]: Enrutamiento, permisos por rol y flujo de peticiones
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── CitasController.php
│   │   ├── ClientesController.php
│   │   ├── ServiciosController.php
│   │   ├── EmpleadosController.php
│   │   ├── PagosController.php
│   │   ├── ReportesController.php
│   │   ├── UsuariosController.php
│   │   └── RespaldosController.php
│   ├── models/              # [Capa de Datos]: Acceso a base de datos y entidades
│   │   ├── Database.php             # Conexión PDO Singleton
│   │   ├── Usuario.php              # Usuarios, bloqueo por intentos y roles
│   │   ├── Cliente.php              # Expedientes de clientes
│   │   ├── Citas.php                # Citas, agendamiento atómico y máquina de estados
│   │   ├── CitaDetalle.php          # Servicios contratados por cita
│   │   ├── Empleado.php             # Personal terapéutico
│   │   ├── DisponibilidadEmpleado.php # Turnos y días de atención
│   │   ├── ServicioModel.php        # Catálogo de servicios y especialidades
│   │   ├── PagoModel.php            # Pagos, validación de cobro y control de duplicados
│   │   ├── CajaModel.php            # Sesiones de caja, arqueos y cierres
│   │   ├── ReporteModel.php         # Consultas contables y fiscales
│   │   ├── Dashboard.php            # Métricas e indicadores del panel
│   │   └── RespaldoModel.php        # Generación/restauración de dumps SQL
│   ├── services/            # [Capa de Negocio]: Reglas de negocio, validaciones y lógica
│   │   ├── AuthService.php          # Login, lockout y sesiones
│   │   ├── CitaServicio.php         # Disponibilidad, cruces de horario y turnos
│   │   ├── ClienteService.php
│   │   ├── EmpleadoService.php
│   │   ├── ServicioService.php
│   │   ├── PagoService.php          # Reglas de cobro (sin duplicados ni excesos)
│   │   ├── CajaService.php          # Apertura, arqueo y cierre de turno
│   │   ├── ReporteService.php
│   │   └── RespaldoService.php
│   ├── helpers/
│   │   └── AuthHelper.php   # Sesiones, roles, CSRF global y timeout de inactividad
│   └── views/               # [Capa de Presentación]: Interfaces de usuario
│       ├── layouts/main.php         # Plantilla base con sidebar + parche global de fetch (CSRF/AJAX)
│       ├── auth/login.php           # Login con carrusel de servicios spa
│       ├── dashboard/index.php      # Panel central con métricas e indicadores
│       ├── citas/                   # Gestión de citas y agenda terapéutica
│       ├── clientes/                # Directorio y expedientes de clientes
│       ├── servicios/               # Catálogo de terapias, masajes y faciales
│       ├── empleados/               # Personal terapéutico y disponibilidad
│       ├── pagos/                   # Caja, cobros y ticket de pago
│       ├── reportes/                # Analítica, CSV y PDF para administrador
│       ├── respaldos/               # Copias de seguridad y mantenimiento
│       └── usuarios/                # Control de accesos y roles del sistema
├── config/                  # Configuraciones globales, zona horaria y credenciales
│   └── config.php
├── database/                # Scripts de base de datos
│   ├── schema.sql           # Tablas, relaciones, empleados y usuarios con Bcrypt
│   ├── seed_data.sql        # Datos de prueba coherentes (re-ejecutable)
│   └── fix_integridad.sql   # Parche para BD existentes (hashes y estados canónicos)
├── public/                  # Recursos estáticos
│   └── css/
│       └── spa-theme.css    # Hoja de estilos con diseño armónico
├── storage/
│   └── backups/             # Respaldos del servidor (bloqueados ante acceso directo por .htaccess)
├── tests/                   # Pruebas unitarias sin dependencias (15 aserciones)
├── .gitignore
├── index.php                # Front Controller (Punto único de entrada con lista blanca de controladores)
└── README.md
```

---

## 📋 Metodología Ágil: Scrum

El proyecto se desarrolló aplicando **Scrum** (los roles no se detallan por decisión del equipo):

### Artefactos

| Artefacto | Contenido |
| :--- | :--- |
| **Product Backlog** | Módulos priorizados: Autenticación y roles → Catálogo de Servicios → Clientes → Citas/Agenda → Caja y Pagos → Reportes → Usuarios → Respaldos → Mejoras de seguridad e integridad |
| **Sprint Backlog** | Historias de usuario y tareas técnicas desglosadas por módulo (ver iteraciones) |
| **Incremento** | Cada Sprint entregó un módulo funcional, probado y fusionado a `main` mediante ramas `feature/*` |

### Iteraciones (Sprints)

| Sprint | Historia de usuario principal | Entregables |
| :--- | :--- | :--- |
| **Sprint 1** | *“Como administrador, quiero gestionar el catálogo de terapias y categorías”* | Módulo de Servicios y Categorías con tests |
| **Sprint 2** | *“Como recepcionista, quiero registrar y consultar expedientes de clientes”* | Módulo de Clientes con historial de citas y tests |
| **Sprint 3** | *“Como cajero, quiero abrir caja, cobrar citas y emitir recibos”* | Módulo de Caja y Pagos con tests |
| **Sprint 4** | *“Como administrador, quiero reportes contables exportables”* | Módulo de Reportes (filtros, CSV/Excel, PDF) con tests |
| **Sprint 5** | *“Como terapeuta, quiero ver solo mis citas y avanzar su estado”* | Permisos por rol, disponibilidad en tiempo real, rediseño del modal de citas |
| **Sprint 6** | *“Como administrador, quiero gestionar usuarios y respaldos seguros”* | Usuarios con bloqueo por intentos, módulo de Respaldos, ticket PDF y cierre de caja (Fase 1) |
| **Sprint 7 (Hardening)** | *“Como usuario, quiero un sistema íntegro y a prueba de errores”* | CSRF global, máquina de estados de citas, anti cobros duplicados, agendamiento transaccional, validación de turnos, seeds idempotentes y corrección de hashes |

### Prácticas Scrum aplicadas

- **Versionado por ramas:** módulos desarrollados en ramas `feature/*` e integrados a `main` con *merge commits* (integración continua simple).
- **Commits convencionales:** prefijos `feat:`, `fix:`, `style:`, `chore:` para trazabilidad de cada incremento.
- **Revisión de incremento:** cada módulo se validó funcionalmente en el entorno XAMPP antes de considerarse terminado.
- **Definition of Done (mínima):** funcionalidad correcta + validaciones de negocio en la capa de servicios + roles verificados en el servidor + vista operativa.
- **Retrospectiva:** aplicada en el Sprint 7 corrigiendo los hallazgos de la auditoría de código (seguridad, integridad de datos y UX).

---

## ⚙️ Instalación y Puesta en Marcha

### 1. Requisitos Previos
- [XAMPP](https://www.apachefriends.org/) instalado con **Apache** y **MySQL** activos.
- Navegador web moderno (Chrome, Edge, Firefox).

### 2. Clonar el Repositorio
Ubica la terminal dentro de tu carpeta `htdocs` de XAMPP:
```bash
cd c:/xampp/htdocs
git clone <TU_URL_DE_GITHUB> Valle-Sereno-Spa
```

### 3. Configurar la Base de Datos
1. Abre **phpMyAdmin** en [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Crea una base de datos llamada:
   ```sql
   CREATE DATABASE spa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Importa el archivo [`database/schema.sql`](database/schema.sql) y luego [`database/seed_data.sql`](database/seed_data.sql) (datos de prueba), o ejecútalos desde la consola de MySQL:
   ```bash
   c:\xampp\mysql\bin\mysql.exe -u root spa_db < c:\xampp\htdocs\Valle-Sereno-Spa\database\schema.sql
   c:\xampp\mysql\bin\mysql.exe -u root spa_db < c:\xampp\htdocs\Valle-Sereno-Spa\database\seed_data.sql
   ```
4. *(Solo si tu BD fue creada antes de la última corrección)* aplica el parche de integridad:
   ```bash
   c:\xampp\mysql\bin\mysql.exe -u root spa_db < c:\xampp\htdocs\Valle-Sereno-Spa\database\fix_integridad.sql
   ```

### 4. Abrir la Aplicación
Ingresa en tu navegador a:
👉 **[http://localhost/Valle-Sereno-Spa/](http://localhost/Valle-Sereno-Spa/)**

---

## 🔑 Credenciales de Acceso (Entorno de Pruebas)

Todas las cuentas de prueba comparten la misma contraseña: **`Admin123*`**

| Rol | Usuario | Correo Corporativo | Contraseña | Alcance / Permisos |
| :--- | :--- | :--- | :--- | :--- |
| **Administrador** | `admin` | `admin@vallesereno.com` | `Admin123*` | Control total, reportes, usuarios y finanzas |
| **Recepcionista** | `recepcion` | `recepcion@vallesereno.com` | `Admin123*` | Agendamiento, registro de clientes y cobros |
| **Terapeuta** | `terapeuta` | `terapeuta@vallesereno.com` | `Admin123*` | Consulta de citas asignadas y atenciones |

> Los hashes bcrypt de `schema.sql` y `seed_data.sql` están verificados con `password_verify('Admin123*', $hash) === true`.

---

## 🔐 Seguridad & Reglas de Negocio Implementadas

- **CSRF global:** token obligatorio (`X-CSRF-TOKEN`) en todas las peticiones POST; inyectado automáticamente por el layout a todos los `fetch`.
- **Autorización por rol en el servidor:** la vista oculta botones, pero el servidor valida rol por módulo *y por acción* (ej. solo Recepción/Administración agendan citas o editan el catálogo).
- **Máquina de estados de citas:** `Pendiente → Confirmada → Completada` (con cancelación solo desde estados no cobrados); evita revivir citas canceladas o completadas.
- **Integridad de cobros:** sin pagos duplicados ni montos que excedan el total (bloqueo `FOR UPDATE` en transacción); el total de la cita se calcula en el servidor desde el catálogo.
- **Agendamiento anti-carreras:** verificación de cruce de horarios e inserción dentro de una transacción con bloqueo pesimista; solo terapeutas dentro de su turno registrado.
- **Caja:** apertura única (bloqueada contra doble apertura), cierre verificado y cuadre con tolerancia de medio centavo.
- **Sesiones:** ID regenerado al iniciar sesión (anti *session fixation*), cookies `httponly` + `SameSite=Lax` y cierre automático por 30 min de inactividad.
- **Respaldos:** generación/restauración solo para Administrador vía POST + CSRF; el directorio `storage/backups` está bloqueado ante acceso directo.
- **Errores:** el Front Controller usa lista blanca de controladores y solo invoca acciones públicas.

---

## 🧪 Pruebas Automatizadas

Pruebas unitarias sin dependencias externas (funcionan en XAMPP puro, sin Composer):

```bash
php tests/run_cliente_test.php    # Validaciones de clientes
php tests/run_pago_test.php       # Validaciones de cobro
php tests/run_reporte_test.php    # Generación de reportes por rango
php tests/run_servicio_test.php   # Validaciones del catálogo de servicios
```

- **15 aserciones** sobre la capa de servicios (con modelos simulados, no requieren BD).
- Los runners devuelven **código de salida ≠ 0** si alguna aserción falla (compatibles con CI).
> `tests/ClienteTest.php` es una plantilla PHPUnit heredada; para ejecutarla se requiere Composer/PHPUnit (no incluido). Los scripts `run_*` son las pruebas oficiales del proyecto.

---

## ✨ Características de la Pantalla de Login

- **Diseño Armónico Spa:** acabado *glassmorphism*, iluminación ambiental relajante, paleta verde bosque (`#1e3d34`) y toques en dorado champagne (`#c5a059`).
- **Carrusel de Servicios:** exhibición interactiva de experiencias del spa (Piedras Volcánicas, Aromaterapia, Circuito de Hidroterapia y Faciales Iluminadores).
- **Seguridad & UX:**
  - Contraseñas encriptadas mediante `password_hash()` con algoritmo `BCRYPT`.
  - Validación dinámica por Fetch/AJAX sin parpadeos de recarga.
  - Alternador para ver/ocultar contraseña.
  - Bloqueo de cuenta tras 3 intentos fallidos y alerta integrada de asistencia.

---

## 👥 Autores
Proyecto universitario de desarrollo de software - Spa Valle Sereno.
