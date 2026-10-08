<?php
/**
 * Controlador de Respaldos & Mantenimiento del Sistema
 * Exclusivo para el rol Administrador
 */
require_once __DIR__ . '/../services/RespaldoService.php';

class RespaldosController {
    private RespaldoService $service;

    public function __construct() {
        AuthHelper::requireRole(['Administrador']);
        $this->service = new RespaldoService();
    }

    public function index(): void {
        $tablas = $this->service->obtenerEstadoTablas();
        $respaldos = $this->service->obtenerListaRespaldos();

        $tamanoTotalMB = 0;
        $totalFilas = 0;
        foreach ($tablas as $t) {
            $tamanoTotalMB += $t['tamano_kb'] / 1024;
            $totalFilas += $t['filas'];
        }

        $pageTitle = "Respaldos & Mantenimiento - Valle Sereno Spa";
        $activePage = 'respaldos';
        $contentView = __DIR__ . '/../views/respaldos/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function descargar(): void {
        $this->service->descargarRespaldoDirecto();
    }

    public function generarLocal(): void {
        header('Content-Type: application/json');
        $res = $this->service->generarYGuardarRespaldo();
        echo json_encode($res);
    }

    public function optimizar(): void {
        header('Content-Type: application/json');
        $res = $this->service->optimizarBaseDatos();
        echo json_encode(['success' => true, 'mensaje' => 'Tablas optimizadas con éxito.', 'detalles' => $res]);
    }

    public function restaurar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_sql'])) {
            $res = $this->service->restaurarDesdeArchivo($_FILES['archivo_sql']);
            $_SESSION['mensaje'] = $res['mensaje'];
            $_SESSION['mensaje_tipo'] = $res['success'] ? 'success' : 'danger';
        }
        header('Location: index.php?c=respaldos&a=index');
        exit;
    }

    public function eliminar(): void {
        header('Content-Type: application/json');
        $filename = $_POST['filename'] ?? '';
        $res = $this->service->eliminarRespaldo($filename);
        echo json_encode($res);
    }
}
