# 🌿 Valle Sereno - Spa & Wellness

Sistema integral para la gestión administrativa, agenda terapéutica, control de cabinas, clientes y facturación de un centro de Spa y Bienestar. Proyecto desarrollado con fines académicos implementando **Arquitectura N-Capas** y el patrón **MVC (Modelo - Vista - Controlador)**.

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
SpaValleSereno/
├── app/
│   ├── controllers/         # [Capa Controlador]: Enrutamiento y flujo de peticiones
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── CitasController.php
│   │   ├── ClientesController.php
│   │   ├── ServiciosController.php
│   │   ├── EmpleadosController.php
│   │   ├── PagosController.php
│   │   ├── ReportesController.php
│   │   └── UsuariosController.php
│   ├── models/              # [Capa de Datos]: Acceso a base de datos y entidades
│   │   ├── Database.php     # Conexión PDO Singleton
│   │   └── Usuario.php      # Consultas y persistencia de usuarios
│   ├── services/            # [Capa de Negocio]: Reglas de negocio, validaciones y lógica
│   │   └── AuthService.php
│   └── views/               # [Capa de Presentación]: Interfaces de usuario
│       ├── layouts/
│       │   └── main.php     # Plantilla base con barra superior y barra lateral (Sidebar)
│       ├── auth/
│       │   └── login.php    # Login armónico con carrusel de servicios spa
│       ├── dashboard/
│       │   └── index.php    # Panel central con métricas e indicadores de spa
│       ├── citas/           # Gestión de citas y agenda terapéutica
│       ├── clientes/        # Directorio y expedientes de clientes
│       ├── servicios/       # Catálogo de terapias, masajes y faciales
│       ├── empleados/       # Personal terapéutico y disponibilidad
│       ├── pagos/           # Caja y facturación de servicios
│       ├── reportes/        # Analítica y balance para administrador
│       └── usuarios/        # Control de accesos y roles del sistema
├── config/                  # Configuraciones globales y credenciales
│   └── config.php
├── database/                # Scripts de base de datos
│   └── schema.sql           # Tablas, relaciones, empleados y usuarios con Bcrypt
├── public/                  # Recursos estáticos
│   └── css/
│       └── spa-theme.css    # Hoja de estilos con diseño armónico
├── .gitignore
├── index.php                # Front Controller (Punto único de entrada)
└── README.md
```

---

## ⚙️ Instalación y Puesta en Marcha

### 1. Requisitos Previos
- [XAMPP](https://www.apachefriends.org/) instalado con **Apache** y **MySQL** activos.
- Navegador web moderno (Chrome, Edge, Firefox).

### 2. Clonar el Repositorio
Ubica la terminal dentro de tu carpeta `htdocs` de XAMPP:
```bash
cd c:/xampp/htdocs
git clone <TU_URL_DE_GITHUB> SpaValleSereno
```

### 3. Configurar la Base de Datos
1. Abre **phpMyAdmin** en [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Crea una base de datos llamada:
   ```sql
   CREATE DATABASE spa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Importa el archivo [`database/schema.sql`](database/schema.sql) o ejecútalo desde la consola de MySQL:
   ```bash
   c:\xampp\mysql\bin\mysql.exe -u root spa_db < c:\xampp\htdocs\SpaValleSereno\database\schema.sql
   ```

### 4. Abrir la Aplicación
Ingresa en tu navegador a:
👉 **[http://localhost/SpaValleSereno/](http://localhost/SpaValleSereno/)**

---

## 🔑 Credenciales de Acceso (Entorno de Pruebas)

Todas las cuentas de prueba comparten la misma contraseña: **`Admin123*`**

| Rol | Usuario | Correo Corporativo | Contraseña | Alcance / Permisos |
| :--- | :--- | :--- | :--- | :--- |
| **Administrador** | `admin` | `admin@vallesereno.com` | `Admin123*` | Control total, reportes, usuarios y finanzas |
| **Recepcionista** | `recepcion` | `recepcion@vallesereno.com` | `Admin123*` | Agendamiento, registro de clientes y cobros |
| **Terapeuta** | `terapeuta` | `terapeuta@vallesereno.com` | `Admin123*` | Consulta de citas asignadas y atenciones |

---

## ✨ Características de la Pantalla de Login

- **Diseño Armónico Spa:** Acabado *glassmorphism*, iluminación ambiental relajante, paleta verde bosque (`#1e3d34`) y toques en dorado champagne (`#c5a059`).
- **Carrusel de Servicios:** Exhibición interactiva de experiencias del 1spa (Piedras Volcánicas, Aromaterapia, Circuito de Hidroterapia y Faciales Iluminadores).
- **Seguridad & UX:**
  - Contraseñas encriptadas mediante `password_hash()` con algoritmo `BCRYPT`.
  - Validación dinámica por Fetch/AJAX sin parpadeos de recarga.
  - Alternador para ver/ocultar contraseña.
  - Alerta integrada para asistencia de credenciales y soporte.

---

## 👥 Autores
Proyecto universitario de desarrollo de software - Spa Valle Sereno.
