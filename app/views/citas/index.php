<!-- Encabezado con Botón Activo -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
            <i class="bi bi-calendar-heart text-warning me-2"></i>
            <?= ($_SESSION['rol_nombre'] ?? '') === 'Terapeuta' ? 'Mis Citas & Agenda Asignada' : 'Agenda & Citas de Spa' ?>
        </h4>
        <p class="text-muted small mb-0">
            <?= ($_SESSION['rol_nombre'] ?? '') === 'Terapeuta' 
                ? 'Consulta de tus sesiones de tratamiento programadas y pacientes asignados.' 
                : 'Control de sesiones terapéuticas, asignación de cabinas y horarios.' ?>
        </p>
    </div>
    <?php if (in_array($_SESSION['rol_nombre'] ?? '', ['Administrador', 'Recepcionista'])): ?>
    <div>
        <button class="btn btn-spa-primary btn-sm px-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Agendar Cita</span>
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

<!-- Tabla Dinámica de Citas -->
<div class="spa-card p-4">
    <div class="table-responsive-none">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #556b61;">
                    <th>PACIENTE & CITAS</th>
                    <th>TERAPEUTA</th>
                    <th>FECHA & HORARIO</th>
                    <th>TOTAL</th>
                    <th>ESTADO</th>
                    <th class="text-end">ACCIONES</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                <?php if (empty($citas)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-1 text-muted"></i>
                            <?= ($_SESSION['rol_nombre'] ?? '') === 'Terapeuta' 
                                ? 'No tienes citas de tratamiento asignadas por el momento.' 
                                : 'No hay citas registradas en la base de datos.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($citas as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">
                                    <span class="text-muted fw-normal me-1">#<?= $c['id'] ?></span>
                                    <?= htmlspecialchars($c['cliente_nombre']) ?>
                                </div>
                                <?php if (!empty($c['observaciones'])): ?>
                                    <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($c['observaciones']) ?>">
                                        <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($c['observaciones']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-dark fw-medium">
                                    <i class="bi bi-person-badge text-primary me-1"></i><?= htmlspecialchars($c['terapeuta_nombre'] ?? $c['empleado_nombre'] ?? 'Sin Asignar') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark mb-0">
                                    <i class="bi bi-calendar3 text-warning me-1"></i><?= date('d/m/Y', strtotime($c['fecha'])) ?>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i><?= date('h:i A', strtotime($c['hora_inicio'])) ?> - <?= date('h:i A', strtotime($c['hora_fin'])) ?>
                                </small>
                            </td>
                            <td>
                                <span class="fw-bold fs-6" style="font-family: var(--font-serif); color: var(--spa-primary);">
                                    C$<?= number_format($c['total'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $estadoClass = match($c['estado']) {
                                        'Confirmada' => 'bg-success-subtle text-success border border-success-subtle',
                                        'Pendiente'  => 'bg-warning-subtle text-warning border border-warning-subtle',
                                        'Completada' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'Cancelada'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        default      => 'bg-secondary-subtle text-secondary'
                                    };
                                ?>
                                <span class="badge <?= $estadoClass ?> rounded-pill px-2 py-1">
                                    <?= htmlspecialchars($c['estado']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php if ($c['estado'] !== 'Cancelada' && $c['estado'] !== 'Completada'): ?>
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($c['estado'] === 'Pendiente'): ?>
                                            <button class="btn btn-outline-success py-1 px-2" title="Confirmar Cita" onclick="cambiarEstadoCita(<?= $c['id'] ?>, 'Confirmada')">
                                                <i class="bi bi-play-fill"></i> <span class="d-none d-lg-inline ms-1">Confirmar</span>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-outline-primary py-1 px-2" title="Marcar Completada" onclick="cambiarEstadoCita(<?= $c['id'] ?>, 'Completada')">
                                            <i class="bi bi-check2-all"></i> <span class="d-none d-lg-inline ms-1">Completar</span>
                                        </button>
                                        <?php if (($_SESSION['rol_nombre'] ?? '') !== 'Terapeuta'): ?>
                                            <button class="btn btn-outline-danger py-1 px-2" title="Cancelar Cita" onclick="cambiarEstadoCita(<?= $c['id'] ?>, 'Cancelada')">
                                                <i class="bi bi-x-lg"></i> <span class="d-none d-lg-inline ms-1">Cancelar</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border"><i class="bi bi-lock me-1"></i>Finalizada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nueva Cita Rediseñado & Optimizado -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: var(--spa-primary);">
                <div>
                    <h5 class="modal-title fw-bold mb-0" style="font-family: var(--font-serif);">
                        <i class="bi bi-calendar-plus text-warning me-2"></i> Agendar Cita Terapéutica
                    </h5>
                    <small class="text-white-50" style="font-size: 0.78rem;">Selección interactiva de paciente, fecha, servicio y terapeuta disponible.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?c=citas&a=guardar" method="POST" id="formNuevaCita">
                <div class="modal-body p-4">
                    
                    <!-- 1. Selección o Registro Express de Cliente -->
                    <div class="p-3 mb-4 rounded-3 border" style="background: #f8faf9;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small text-dark mb-0">
                                <i class="bi bi-person-heart text-success me-1"></i> Paciente / Cliente <span class="text-danger">*</span>
                            </label>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold" onclick="toggleFormNuevoCliente()">
                                <i class="bi bi-person-plus-fill me-1"></i><span id="btnTextNuevoCliente">+ ¿Cliente nuevo? Registrar rápido</span>
                            </button>
                        </div>

                        <!-- Selector de Cliente Existente -->
                        <div id="seccionClienteExistente">
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
                        <div id="seccionClienteNuevo" class="d-none mt-3 pt-3 border-top">
                            <div class="alert alert-info py-2 px-3 small mb-3">
                                <i class="bi bi-info-circle me-1"></i> Registra un nuevo paciente de inmediato y se asignará automáticamente a esta cita.
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6 mb-2">
                                    <input type="text" id="express_nombre" class="form-control form-control-sm" placeholder="Nombre completo *">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="tel" id="express_telefono" class="form-control form-control-sm" placeholder="Teléfono *">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="date" id="express_fecha_nac" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="email" id="express_correo" class="form-control form-control-sm" placeholder="Correo (opcional)">
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-1 px-3" onclick="guardarClienteExpress()">
                                <i class="bi bi-check-lg me-1"></i> Confirmar Nuevo Cliente
                            </button>
                        </div>
                    </div>

                    <!-- 2. Selección de Servicio & Tarifa -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark">
                            <i class="bi bi-stars text-warning me-1"></i> Tratamiento / Servicio <span class="text-muted font-normal">(Opcional para cálculo automático)</span>
                        </label>
                        <select id="select_servicio" class="form-select" onchange="seleccionarServicio(this)">
                            <option value="" data-costo="0" data-duracion="60">-- Seleccionar Tratamiento del Menú --</option>
                            <?php foreach ($servicios as $srv): ?>
                                <option value="<?= $srv['id'] ?>" data-costo="<?= $srv['costo'] ?>" data-duracion="<?= $srv['duracion_minutos'] ?>">
                                    <?= htmlspecialchars($srv['nombre']) ?> - C$<?= number_format($srv['costo'], 2) ?> (<?= (int)$srv['duracion_minutos'] ?> min)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 3. Fecha & Horario -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Fecha de Atencion <span class="text-danger">*</span></label>
                            <input type="date" id="input_fecha" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" onchange="actualizarHorarioYDisponibilidad()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Hora Inicio <span class="text-danger">*</span></label>
                            <input type="time" id="input_hora_inicio" name="hora_inicio" class="form-control" value="10:00" onchange="actualizarHorarioYDisponibilidad()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Hora Fin <span class="text-danger">*</span></label>
                            <input type="time" id="input_hora_fin" name="hora_fin" class="form-control" value="11:00" onchange="actualizarHorarioYDisponibilidad()" required>
                        </div>
                    </div>

                    <!-- 4. Selección de Terapeuta Disponible en Tiempo Real -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark">
                            <i class="bi bi-person-badge text-primary me-1"></i> Terapeuta Asignado <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted" style="font-size: 0.75rem;" id="lblEstadoTerapeutas">Verificando terapeutas libres para este horario...</small>
                        </div>
                        <select id="select_id_empleado" name="id_empleado" class="form-select" required>
                            <option value="">-- Seleccionar Terapeuta Disponible --</option>
                            <?php foreach ($terapeutas as $t): ?>
                                <option value="<?= $t['id'] ?>">
                                    <?= htmlspecialchars($t['nombre_completo']) ?> (<?= htmlspecialchars($t['cargo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 5. Monto e Indicaciones -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Monto Total (C$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">C$</span>
                                <input type="number" step="0.01" id="input_total" name="total" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-dark">Observaciones / Indicaciones Especiales</label>
                            <textarea name="observaciones" class="form-control" rows="1" placeholder="Ej. Sensibilidad en piel, preferencia de aceite aromático..."></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spa-primary btn-sm px-4">
                        <i class="bi bi-calendar-check me-1"></i> Agendar Cita
                    </button>
                </div>
            </form>
        </div>
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

            toggleFormNuevoCliente();
            alert('¡Cliente registrado correctamente!');
        } else {
            alert('Error: ' + res.mensaje);
        }
    });
}

function seleccionarServicio(select) {
    const opt = select.options[select.selectedIndex];
    const costo = opt.getAttribute('data-costo');
    const duracion = parseInt(opt.getAttribute('data-duracion') || 60);

    if (costo) {
        document.getElementById('input_total').value = parseFloat(costo).toFixed(2);
    }

    // Calcular hora fin automática basada en la duración del servicio
    const horaInicioStr = document.getElementById('input_hora_inicio').value;
    if (horaInicioStr) {
        const parts = horaInicioStr.split(':');
        const dateObj = new Date();
        dateObj.setHours(parseInt(parts[0]), parseInt(parts[1]) + duracion, 0);
        
        const h = String(dateObj.getHours()).padStart(2, '0');
        const m = String(dateObj.getMinutes()).padStart(2, '0');
        document.getElementById('input_hora_fin').value = `${h}:${m}`;
    }

    actualizarHorarioYDisponibilidad();
}

function actualizarHorarioYDisponibilidad() {
    const fecha = document.getElementById('input_fecha').value;
    const horaInicio = document.getElementById('input_hora_inicio').value;
    const horaFin = document.getElementById('input_hora_fin').value;
    const idServicio = document.getElementById('select_servicio')?.value || '';
    const selectTerapeutas = document.getElementById('select_id_empleado');
    const lblState = document.getElementById('lblEstadoTerapeutas');

    if (!fecha || !horaInicio || !horaFin) return;

    lblState.innerHTML = '<span class="text-warning"><i class="bi bi-arrow-repeat spin"></i> Verificando terapeutas libres...</span>';

    fetch(`index.php?c=citas&a=consultarDisponibilidad&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}&id_servicio=${idServicio}`)
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            selectTerapeutas.innerHTML = '<option value="">-- Seleccionar Terapeuta Disponible --</option>';
            if (res.data.length > 0) {
                res.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.text = `${t.nombre_completo} (${t.cargo})`;
                    selectTerapeutas.add(opt);
                });
                lblState.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i> ${res.data.length} terapeuta(s) disponible(s) en este horario.</span>`;
            } else {
                lblState.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> No hay terapeutas disponibles en este horario (cruce detectado).</span>';
            }
        }
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
</script>