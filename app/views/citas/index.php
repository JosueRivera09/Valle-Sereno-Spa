<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda & Citas de Spa</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        :root {
            --spa-primary: #2c3e50;
            --font-serif: Georgia, serif;
        }

        body {
            background-color: #f4f6f9;
        }

        /* Limitar el ancho máximo para que no se extienda en pantallas ultra ancha */
        .main-wrapper {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Estilos de la tarjeta contenedora */
        .card-spa {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        /* Mejoras en la tabla */
        .table thead th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            font-size: 0.92rem;
        }

        /* Iconos SVG inline */
        .icon-svg {
            display: inline-block;
            vertical-align: -0.125em;
            fill: currentColor;
        }

        .btn-spa-primary {
            background-color: var(--spa-primary);
            border-color: var(--spa-primary);
            color: #ffffff;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-spa-primary:hover {
            background-color: #1a252f;
            border-color: #1a252f;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(44, 62, 80, 0.2);
        }
    </style>
</head>
<body class="p-3 p-md-4">

    <div class="main-wrapper">
        <!-- Encabezado con Botón Activo -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--spa-primary); font-family: var(--font-serif);">
                    <!-- SVG Calendario Corazón -->
                    <svg class="icon-svg text-warning me-2" width="22" height="22" viewBox="0 0 16 16">
                        <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM1 14V4h14v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/>
                        <path d="M8 7.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132z"/>
                    </svg>
                    Agenda & Citas de Spa
                </h4>
                <p class="text-muted small mb-0">Control de sesiones terapéuticas, asignación de cabinas y horarios.</p>
            </div>
            <div>
                <button class="btn btn-spa-primary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
                    <svg class="icon-svg me-2" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Agendar Cita
                </button>
            </div>
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
        <div class="card card-spa">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Cliente</th>
                                <th>Terapeuta / Empleado</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($citas)): ?>
                                <?php foreach ($citas as $c): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">#<?= htmlspecialchars($c['id']) ?></td>
                                        <td class="fw-medium"><?= htmlspecialchars($c['cliente_nombre'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($c['empleado_nombre'] ?? 'N/A') ?></td>
                                        <td><?= !empty($c['fecha']) ? date('d/m/Y', strtotime($c['fecha'])) : '-' ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?= !empty($c['hora_inicio']) ? date('h:i A', strtotime($c['hora_inicio'])) : '--:--' ?> - 
                                                <?= !empty($c['hora_fin']) ? date('h:i A', strtotime($c['hora_fin'])) : '--:--' ?>
                                            </small>
                                        </td>
                                        <td class="fw-semibold text-dark">$<?= number_format($c['total'] ?? 0, 2) ?></td>
                                        <td>
                                            <?php
                                                $badgeClass = match($c['estado'] ?? '') {
                                                    'completado', 'atendido' => 'bg-success-subtle text-success border-success-subtle',
                                                    'cancelado' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                    default => 'bg-warning-subtle text-warning border-warning-subtle'
                                                };
                                            ?>
                                            <span class="badge border px-2 py-1 rounded-pill <?= $badgeClass ?>">
                                                <?= ucfirst(htmlspecialchars($c['estado'] ?? 'pendiente')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= htmlspecialchars($c['observaciones'] ?? '-') ?></small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php if (($c['estado'] ?? '') === 'pendiente'): ?>
                                                <form action="index.php?c=citas&a=cambiarEstado" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                    <input type="hidden" name="estado" value="completado">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Completar cita" onclick="return confirm('¿Confirmar que esta cita ha sido completada?');">
                                                        <svg class="icon-svg" width="14" height="14" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                                                    </button>
                                                </form>
                                                <form action="index.php?c=citas&a=cambiarEstado" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                    <input type="hidden" name="estado" value="cancelado">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar cita" onclick="return confirm('¿Está seguro de cancelar esta cita?');">
                                                        <svg class="icon-svg" width="14" height="14" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <!-- SVG Sin Citas -->
                                        <svg class="icon-svg text-secondary mb-3 d-block mx-auto opacity-50" width="40" height="40" viewBox="0 0 16 16">
                                            <path d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z"/>
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                        </svg>
                                        <span class="fw-medium">No hay citas registradas en la agenda.</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nueva Cita -->
    <div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="modalNuevaCitaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalNuevaCitaLabel" style="color: var(--spa-primary);">
                        Agendar Nueva Cita
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="index.php?c=citas&a=guardar" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cliente</label>
                            <select name="id_cliente" class="form-select" required>
                                <option value="" selected disabled>Seleccione un cliente...</option>
                                <?php if (!empty($clientes)): ?>
                                    <?php foreach ($clientes as $cli): ?>
                                        <option value="<?= $cli['id'] ?>"><?= htmlspecialchars($cli['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Terapeuta / Empleado</label>
                            <select name="id_empleado" class="form-select" required>
                                <option value="" selected disabled>Seleccione un terapeuta...</option>
                                <?php if (!empty($empleados)): ?>
                                    <?php foreach ($empleados as $emp): ?>
                                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['nombre']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Fecha</label>
                            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Hora Inicio</label>
                                <input type="time" name="hora_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold">Hora Fin</label>
                                <input type="time" name="hora_fin" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Monto Total ($)</label>
                            <input type="number" step="0.01" name="total" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Detalles de la sesión..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-spa-primary px-4">Guardar Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>