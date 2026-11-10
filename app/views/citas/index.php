<?php
// Contadores para métricas SaaS
$totalCitas = count($citas ?? []);
$citasConfirmadas = count(array_filter($citas ?? [], fn($c) => $c['estado'] === 'Confirmada'));
$citasPendientes  = count(array_filter($citas ?? [], fn($c) => $c['estado'] === 'Pendiente'));
$citasCompletadas = count(array_filter($citas ?? [], fn($c) => $c['estado'] === 'Completada'));
?>

<!-- Encabezado de Página SaaS -->
<div class="saas-page-header">
    <div class="saas-header-info">
        <span class="saas-breadcrumb"><i class="bi bi-calendar3 me-1"></i>Operación Diaria</span>
        <h1 class="saas-title">
            <?= ($_SESSION['rol_nombre'] ?? '') === 'Terapeuta' ? 'Mis Citas & Agenda Asignada' : 'Agenda & Citas Terapéuticas' ?>
        </h1>
        <p class="saas-subtitle">
            <?= ($_SESSION['rol_nombre'] ?? '') === 'Terapeuta' 
                ? 'Control de sesiones programadas, pacientes asignados y registro de estados de atención.' 
                : 'Programación centralizada de cabinas, terapeutas disponibles y estado de atenciones en tiempo real.' ?>
        </p>
    </div>
    <?php if (in_array($_SESSION['rol_nombre'] ?? '', ['Administrador', 'Recepcionista'])): ?>
    <div class="saas-header-actions">
        <button class="btn-saas-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
            <i class="bi bi-calendar-plus"></i>
            <span>Agendar Nueva Cita</span>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Alertas de Sesión -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['mensaje_tipo'] ?? 'info') ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
<?php endif; ?>

<!-- Grid de KPIs / Estadísticas SaaS -->
<div class="saas-stats-grid">
    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Total en Agenda</span>
            <div class="saas-stat-icon">
                <i class="bi bi-calendar3"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $totalCitas ?></div>
        <div class="saas-stat-meta">
            <span class="text-muted">Citas registradas</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Confirmadas</span>
            <div class="saas-stat-icon is-blue">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $citasConfirmadas ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-success py-0 px-2">Listas para atención</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Pendientes</span>
            <div class="saas-stat-icon is-gold">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $citasPendientes ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-warning py-0 px-2">Requieren confirmación</span>
        </div>
    </div>

    <div class="saas-stat-card">
        <div class="saas-stat-top">
            <span class="saas-stat-label">Completadas</span>
            <div class="saas-stat-icon is-purple">
                <i class="bi bi-patch-check"></i>
            </div>
        </div>
        <div class="saas-stat-value"><?= $citasCompletadas ?></div>
        <div class="saas-stat-meta">
            <span class="saas-badge saas-badge-info py-0 px-2">Servicios finalizados</span>
        </div>
    </div>
</div>

<!-- Tarjeta Contenedor con Tabla de Citas -->
<div class="saas-card">
    <div class="saas-card-head">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h2 class="saas-card-title me-3">
                <i class="bi bi-list-task text-primary"></i>
                <span>Listado de Citas</span>
            </h2>
            <!-- Filtros Segmentados por Estado -->
            <div class="saas-filter-tabs">
                <button type="button" class="saas-tab-btn is-active" onclick="filtrarCitasPorEstado('todos', this)">Todas</button>
                <button type="button" class="saas-tab-btn" onclick="filtrarCitasPorEstado('Pendiente', this)">Pendientes</button>
                <button type="button" class="saas-tab-btn" onclick="filtrarCitasPorEstado('Confirmada', this)">Confirmadas</button>
                <button type="button" class="saas-tab-btn" onclick="filtrarCitasPorEstado('Completada', this)">Completadas</button>
                <button type="button" class="saas-tab-btn" onclick="filtrarCitasPorEstado('Cancelada', this)">Canceladas</button>
            </div>
        </div>

        <!-- Buscador integrado -->
        <div class="saas-search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="busquedaCitaInput" class="saas-search-input" placeholder="Buscar por paciente, terapeuta, ID..." onkeyup="filtrarTablaCitas()">
        </div>
    </div>

    <div class="saas-card-body p-0">
        <div class="table-responsive">
            <table class="saas-table" id="tablaCitas">
                <thead>
                    <tr>
                        <th>PACIENTE & CITAS</th>
                        <th>TERAPEUTA ASIGNADO</th>
                        <th>FECHA & HORARIO</th>
                        <th>MONTO TOTAL</th>
                        <th>ESTADO</th>
                        <th class="text-end">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citas)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                                <?= ($_SESSION['rol_nombre'] ?? '') === 'Terapeuta' 
                                    ? 'No tienes citas de tratamiento asignadas por el momento.' 
                                    : 'No hay citas registradas en la base de datos.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($citas as $c): ?>
                            <?php
                                $estado = $c['estado'];
                                $badgeClass = match($estado) {
                                    'Confirmada' => 'saas-badge-success',
                                    'Pendiente'  => 'saas-badge-warning',
                                    'Completada' => 'saas-badge-info',
                                    'Cancelada'  => 'saas-badge-danger',
                                    default      => 'saas-badge-neutral'
                                };
                                $initialsCli = strtoupper(mb_substr($c['cliente_nombre'] ?? 'P', 0, 1, 'UTF-8'));
                            ?>
                            <tr class="fila-cita" data-estado="<?= htmlspecialchars($estado) ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="saas-avatar is-primary"><?= $initialsCli ?></span>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                <span class="text-muted fw-normal me-1">#<?= $c['id'] ?></span>
                                                <?= htmlspecialchars($c['cliente_nombre']) ?>
                                            </div>
                                            <?php if (!empty($c['observaciones'])): ?>
                                                <small class="text-muted d-block text-truncate" style="max-width: 240px;" title="<?= htmlspecialchars($c['observaciones']) ?>">
                                                    <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($c['observaciones']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-person-badge text-primary me-1"></i>
                                        <span class="text-dark fw-medium"><?= htmlspecialchars($c['terapeuta_nombre'] ?? $c['empleado_nombre'] ?? 'Sin Asignar') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-calendar3 me-1 text-muted"></i><?= date('d/m/Y', strtotime($c['fecha'])) ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i><?= date('h:i A', strtotime($c['hora_inicio'])) ?> - <?= date('h:i A', strtotime($c['hora_fin'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">
                                        C$ <?= number_format($c['total'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="saas-badge <?= $badgeClass ?>">
                                        <span class="saas-badge-dot"></span>
                                        <?= htmlspecialchars($estado) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if ($c['estado'] !== 'Cancelada' && $c['estado'] !== 'Completada'): ?>
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($c['estado'] === 'Pendiente'): ?>
                                                <button class="btn btn-sm btn-outline-success py-1 px-2" title="Confirmar Cita" onclick="cambiarEstadoCita(<?= $c['id'] ?>, 'Confirmada')">
                                                    <i class="bi bi-play-fill"></i> <span class="d-none d-lg-inline ms-1">Confirmar</span>
                                                </button>
                                            <?php elseif ($c['estado'] === 'Confirmada'): ?>
                                                <button class="btn btn-sm btn-outline-primary py-1 px-2" title="Marcar Completada" onclick="cambiarEstadoCita(<?= $c['id'] ?>, 'Completada')">
                                                    <i class="bi bi-check2-all"></i> <span class="d-none d-lg-inline ms-1">Completar</span>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (($_SESSION['rol_nombre'] ?? '') !== 'Terapeuta'): ?>
                                                <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Cancelar Cita" onclick="cambiarEstadoCita(<?= $c['id'] ?>, 'Cancelada')">
                                                    <i class="bi bi-x-lg"></i> <span class="d-none d-lg-inline ms-1">Cancelar</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="saas-badge saas-badge-neutral"><i class="bi bi-lock me-1"></i>Finalizada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filtrarTablaCitas() {
    const term = (document.getElementById('busquedaCitaInput')?.value || '').toLowerCase();
    const rows = document.querySelectorAll('.fila-cita');
    rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(term) ? '' : 'none';
    });
}

function filtrarCitasPorEstado(estado, btn) {
    document.querySelectorAll('.saas-tab-btn').forEach(b => b.classList.remove('is-active'));
    if (btn) btn.classList.add('is-active');

    const rows = document.querySelectorAll('.fila-cita');
    rows.forEach(r => {
        const rEstado = r.getAttribute('data-estado');
        if (estado === 'todos' || rEstado === estado) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}
</script>

<?php
$horaActual = (int)date('H');
if ($horaActual >= 18) {
    $defFecha = date('Y-m-d', strtotime('+1 day'));
    $defHoraIni = '09:00';
    $defHoraFin = '10:00';
} else {
    $defFecha = date('Y-m-d');
    $proxHora = max(8, $horaActual + 1);
    $defHoraIni = sprintf('%02d:00', $proxHora);
    $defHoraFin = sprintf('%02d:00', min(20, $proxHora + 1));
}
?>
<!-- Modal Nueva Cita -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="index.php?c=citas&a=guardar" method="POST" id="formNuevaCita" class="modal-content modal-editorial">
            <input type="hidden" name="csrf_token" value="<?= AuthHelper::csrfToken() ?>">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Agenda</span>
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus me-2"></i>Agendar Cita Terapéutica
                    </h5>
                    <p class="modal-sub">Selección interactiva de paciente, fecha, servicio y terapeuta disponible.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto; max-height: calc(85vh - 140px);">

                    <!-- 01. Paciente / Cliente -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="form-section-num">01</span>
                            <h6 class="form-section-title">Paciente / Cliente</h6>
                            <button type="button" class="modal-link ms-auto" onclick="toggleFormNuevoCliente()">
                                <i class="bi bi-person-plus-fill me-1"></i><span id="btnTextNuevoCliente">¿Cliente nuevo? Registrar rápido</span>
                            </button>
                        </div>

                        <!-- Selector de Cliente Existente -->
                        <div id="seccionClienteExistente">
                            <label class="form-label">Buscar cliente registrado <span class="text-danger">*</span></label>
                            <select id="select_id_cliente" name="id_cliente" class="form-select" required>
                                <option value="">-- Buscar o Seleccionar Cliente --</option>
                                <?php foreach ($clientes as $cl): ?>
                                    <option value="<?= $cl['id'] ?>">
                                        <?= htmlspecialchars($cl['nombre_completo']) ?> (Tel: <?= htmlspecialchars($cl['telefono']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Formulario Express Cliente Nuevo -->
                        <div id="seccionClienteNuevo" class="d-none mt-3">
                            <div class="modal-note">
                                <i class="bi bi-info-circle"></i>
                                <span>Registra un nuevo paciente de inmediato y se asignará automáticamente a esta cita.</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" id="express_nombre" class="form-control form-control-sm" placeholder="Nombre completo *">
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" id="express_telefono" class="form-control form-control-sm" placeholder="Teléfono *">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" id="express_fecha_nac" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" id="express_correo" class="form-control form-control-sm" placeholder="Correo (opcional)">
                                </div>
                            </div>
                            <button type="button" class="btn btn-gold btn-sm mt-2 px-3" onclick="guardarClienteExpress()">
                                <i class="bi bi-check-lg me-1"></i> Confirmar Nuevo Cliente
                            </button>
                        </div>
                    </div>

                    <!-- 02. Tratamiento / Servicio -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="form-section-num">02</span>
                            <h6 class="form-section-title">Tratamiento / Servicio</h6>
                        </div>
                        <label class="form-label">Tratamiento del catálogo <span class="text-muted">(opcional · cálculo automático)</span></label>
                        <select id="select_servicio" name="id_servicio" class="form-select" onchange="seleccionarServicio(this)">
                            <option value="" data-costo="0" data-duracion="60">-- Seleccionar Tratamiento del Menú --</option>
                            <?php foreach ($servicios as $srv): ?>
                                <option value="<?= $srv['id'] ?>" data-costo="<?= $srv['costo'] ?>" data-duracion="<?= $srv['duracion_minutos'] ?>">
                                    <?= htmlspecialchars($srv['nombre']) ?> - C$<?= number_format($srv['costo'], 2) ?> (<?= (int)$srv['duracion_minutos'] ?> min)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 03. Fecha & Horario -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="form-section-num">03</span>
                            <h6 class="form-section-title">Fecha &amp; Horario</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Fecha de atención <span class="text-danger">*</span></label>
                                <input type="date" id="input_fecha" name="fecha" class="form-control" value="<?= $defFecha ?>" onchange="actualizarHorarioYDisponibilidad()" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hora inicio <span class="text-danger">*</span></label>
                                <input type="time" id="input_hora_inicio" name="hora_inicio" class="form-control" value="<?= $defHoraIni ?>" onchange="actualizarHorarioYDisponibilidad()" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hora fin <span class="text-danger">*</span></label>
                                <input type="time" id="input_hora_fin" name="hora_fin" class="form-control" value="<?= $defHoraFin ?>" onchange="actualizarHorarioYDisponibilidad()" required>
                            </div>
                        </div>
                    </div>

                    <!-- 04. Terapeuta Disponible -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="form-section-num">04</span>
                            <h6 class="form-section-title">Terapeuta Asignado</h6>
                        </div>
                        <label class="form-label">Terapeuta disponible <span class="text-danger">*</span></label>
                        <select id="select_id_empleado" name="id_empleado" class="form-select" required>
                            <option value="">-- Seleccionar Terapeuta Disponible --</option>
                            <?php foreach ($terapeutas as $t): ?>
                                <option value="<?= $t['id'] ?>">
                                    <?= htmlspecialchars($t['nombre_completo']) ?> (<?= htmlspecialchars($t['cargo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted d-block mt-2" style="font-size: 0.75rem;" id="lblEstadoTerapeutas">
                            <i class="bi bi-arrow-repeat me-1"></i>Verificando terapeutas libres para este horario...
                        </small>
                    </div>

                    <!-- 05. Monto & Indicaciones -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="form-section-num">05</span>
                            <h6 class="form-section-title">Monto &amp; Indicaciones</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Monto total (C$) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">C$</span>
                                    <input type="number" step="0.01" id="input_total" name="total" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Observaciones / Indicaciones especiales</label>
                                <textarea name="observaciones" class="form-control" rows="1" placeholder="Ej. Sensibilidad en piel, preferencia de aceite aromático..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-quiet btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-calendar-check me-1"></i> Agendar Cita
                    </button>
                </div>
        </form>
    </div>
</div>

<script>
function toggleFormNuevoCliente() {
    const secExistente = document.getElementById('seccionClienteExistente');
    const secNuevo = document.getElementById('seccionClienteNuevo');
    const select = document.getElementById('select_id_cliente');
    const btnText = document.getElementById('btnTextNuevoCliente');

    if (secNuevo.classList.contains('d-none')) {
        secNuevo.classList.remove('d-none');
        secExistente.classList.add('d-none');
        select.removeAttribute('required');
        btnText.innerText = '← Seleccionar de la lista de clientes';
        document.getElementById('express_nombre')?.focus();
    } else {
        secNuevo.classList.add('d-none');
        secExistente.classList.remove('d-none');
        select.setAttribute('required', 'required');
        btnText.innerText = '+ ¿Cliente nuevo? Registrar rápido';
    }
}

function guardarClienteExpress() {
    const nombre = document.getElementById('express_nombre').value.trim();
    const telefono = document.getElementById('express_telefono').value.trim();
    const fechaNac = document.getElementById('express_fecha_nac').value;
    const correo = document.getElementById('express_correo').value.trim();

    if (!nombre || !telefono) {
        alert('Por favor ingresa el nombre y teléfono del nuevo paciente.');
        return;
    }

    const fd = new FormData();
    fd.append('nombre_completo', nombre);
    fd.append('telefono', telefono);
    fd.append('fecha_nacimiento', fechaNac);
    fd.append('correo', correo);

    fetch('index.php?c=citas&a=crearClienteExpress', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.cliente) {
            const select = document.getElementById('select_id_cliente');
            const opt = document.createElement('option');
            opt.value = res.cliente.id;
            opt.text = `${res.cliente.nombre_completo} (Tel: ${res.cliente.telefono})`;
            opt.selected = true;
            select.add(opt);

            // Limpiar campos express
            document.getElementById('express_nombre').value = '';
            document.getElementById('express_telefono').value = '';
            document.getElementById('express_fecha_nac').value = '';
            document.getElementById('express_correo').value = '';

            toggleFormNuevoCliente();
            alert('¡Cliente registrado correctamente!');
        } else {
            alert('Error: ' + (res.mensaje || 'No se pudo registrar al cliente.'));
        }
    })
    .catch(() => alert('Ocurrió un error de conexión al registrar el cliente.'));
}

// Validación integral antes de enviar el formulario de nueva cita
const formCita = document.getElementById('formNuevaCita');
if (formCita) {
    formCita.addEventListener('submit', function(e) {
        const secNuevo = document.getElementById('seccionClienteNuevo');
        const selectCliente = document.getElementById('select_id_cliente');

        if (secNuevo && !secNuevo.classList.contains('d-none') && !selectCliente.value) {
            e.preventDefault();
            alert('Por favor confirma primero el registro del nuevo paciente haciendo clic en "Confirmar Nuevo Cliente", o vuelve a la lista de pacientes.');
            return;
        }

        const ini = document.getElementById('input_hora_inicio').value;
        const fin = document.getElementById('input_hora_fin').value;
        if (ini && fin && fin <= ini) {
            e.preventDefault();
            alert('La hora de fin debe ser posterior a la hora de inicio.');
            return;
        }

        const selectTerapeuta = document.getElementById('select_id_empleado');
        if (!selectTerapeuta || !selectTerapeuta.value) {
            e.preventDefault();
            alert('Debes seleccionar un terapeuta disponible para continuar.');
            return;
        }
    });
}

function seleccionarServicio(select) {
    const opt = select.options[select.selectedIndex];
    const costo = parseFloat(opt.getAttribute('data-costo') || '0');
    const duracion = parseInt(opt.getAttribute('data-duracion') || 60);
    const inputTotal = document.getElementById('input_total');

    if (costo > 0) {
        inputTotal.value = costo.toFixed(2);
        inputTotal.readOnly = true;
    } else {
        inputTotal.readOnly = false;
    }

    // Calcular hora fin automática basada en la duración del servicio
    const horaInicioStr = document.getElementById('input_hora_inicio').value;
    if (horaInicioStr) {
        const parts = horaInicioStr.split(':');
        const dateObj = new Date();
        dateObj.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10) + duracion, 0);
        
        const h = String(dateObj.getHours()).padStart(2, '0');
        const m = String(dateObj.getMinutes()).padStart(2, '0');
        document.getElementById('input_hora_fin').value = `${h}:${m}`;
    }

    actualizarHorarioYDisponibilidad();
}

function actualizarHorarioYDisponibilidad() {
    const fecha = document.getElementById('input_fecha')?.value;
    const horaInicio = document.getElementById('input_hora_inicio')?.value;
    const selectServicio = document.getElementById('select_servicio');
    const idServicio = selectServicio?.value || '';
    const selectTerapeutas = document.getElementById('select_id_empleado');
    const lblState = document.getElementById('lblEstadoTerapeutas');

    if (!selectTerapeutas || !lblState) return;

    // Recalcular hora fin si cambió la hora de inicio y hay un servicio con duración definida
    if (idServicio && selectServicio.selectedIndex >= 0) {
        const duracion = parseInt(selectServicio.options[selectServicio.selectedIndex].getAttribute('data-duracion') || 0);
        if (duracion > 0 && horaInicio) {
            const parts = horaInicio.split(':');
            const dateObj = new Date();
            dateObj.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10) + duracion, 0);
            document.getElementById('input_hora_fin').value =
                `${String(dateObj.getHours()).padStart(2, '0')}:${String(dateObj.getMinutes()).padStart(2, '0')}`;
        }
    }

    const horaFin = document.getElementById('input_hora_fin')?.value;

    if (!fecha || !horaInicio || !horaFin) return;

    if (horaFin <= horaInicio) {
        lblState.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> La hora de fin debe ser posterior a la de inicio.</span>';
        return;
    }

    lblState.innerHTML = '<span class="text-warning"><i class="bi bi-arrow-repeat spin"></i> Verificando disponibilidad...</span>';

    fetch(`index.php?c=citas&a=consultarDisponibilidad&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}&id_servicio=${idServicio}`)
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const prevSelected = selectTerapeutas.value;
            selectTerapeutas.innerHTML = '<option value="">-- Seleccionar Terapeuta Disponible --</option>';
            if (res.data && res.data.length > 0) {
                res.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.text = `${t.nombre_completo} (${t.cargo})`;
                    if (String(t.id) === String(prevSelected)) {
                        opt.selected = true;
                    }
                    selectTerapeutas.add(opt);
                });
                lblState.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i> ${res.data.length} terapeuta(s) disponible(s) en este horario.</span>`;
            } else {
                lblState.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> No hay terapeutas con turno libre en este horario.</span>';
            }
        }
    })
    .catch(() => {
        lblState.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> No se pudo verificar la disponibilidad. Intente de nuevo.</span>';
    });
}

function cambiarEstadoCita(id, nuevoEstado) {
    if (!confirm(`¿Deseas cambiar el estado de la cita #${id} a '${nuevoEstado}'?`)) return;

    const fd = new FormData();
    fd.append('id', id);
    fd.append('estado', nuevoEstado);

    fetch('index.php?c=citas&a=cambiarEstado', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            location.reload();
        } else {
            alert('Error: ' + res.mensaje);
        }
    })
    .catch(err => {
        alert('Ocurrió un error de conexión al actualizar la cita.');
    });
}

// Inicialización de eventos al cargar el DOM
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalNuevaCita');

    // Evitar que la tecla Enter en el formulario express envíe la cita completa antes de confirmar cliente
    document.querySelectorAll('#seccionClienteNuevo input').forEach(inp => {
        inp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                guardarClienteExpress();
            }
        });
    });

    // Ejecutar verificación de disponibilidad automáticamente al desplegar el modal
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function() {
            actualizarHorarioYDisponibilidad();
        });
    }

    // Abrir modal automáticamente si proviene de la redirección de "Nueva Cita" (?crear=1)
    const params = new URLSearchParams(window.location.search);
    if (params.get('crear') === '1' || params.get('nueva') === '1') {
        if (modalEl) {
            const abrirModal = () => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();

                    // Limpiar el parámetro de la URL para que no vuelva a abrirse si el usuario recarga la página
                    const url = new URL(window.location.href);
                    url.searchParams.delete('crear');
                    url.searchParams.delete('nueva');
                    window.history.replaceState({}, document.title, url.toString());
                }
            };

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                abrirModal();
            } else {
                setTimeout(abrirModal, 250);
            }
        }
    }
});
</script>