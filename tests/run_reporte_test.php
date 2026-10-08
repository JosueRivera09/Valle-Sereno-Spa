<?php
require_once __DIR__ . '/../app/services/ReporteService.php';

class FakeReporteModel extends ReporteModel {
    public function __construct() {}
    public function obtenerFiltroRangoFechas(string $inicio, string $fin): array {
        return [
            ['id' => 1, 'fecha_pago' => '2026-10-01 10:00:00', 'tipo_pago' => 'Efectivo', 'monto' => 500.00, 'cliente_nombre' => 'Ana López']
        ];
    }
}

class SimpleRunner {
    private int $passed = 0;
    private int $failed = 0;

    public function assert(bool $condition, string $testName): void {
        if ($condition) {
            echo " [OK] $testName\n";
            $this->passed++;
        } else {
            echo " [FAIL] $testName\n";
            $this->failed++;
        }
    }

    public function summary(): void {
        echo "\n----------------------------------------\n";
        echo "RESULTADOS REPORTES TEST: {$this->passed} pasados, {$this->failed} fallidos.\n";
        echo "----------------------------------------\n";
    }
}

$runner = new SimpleRunner();
$service = new ReporteService(new FakeReporteModel());

// 1. Validar Rango Válido
$res1 = $service->generarReporteRango('2026-10-01', '2026-10-07');
$runner->assert($res1['success'] === true && count($res1['data']) > 0, 'Generar reporte con rango válido');

// 2. Rechazo por Rango Invertido
$res2 = $service->generarReporteRango('2026-10-10', '2026-10-01');
$runner->assert($res2['success'] === false, 'Rechazo cuando fecha inicio es posterior a fecha fin');

// 3. Rechazo por Fechas Vacías
$res3 = $service->generarReporteRango('', '2026-10-07');
$runner->assert($res3['success'] === false, 'Rechazo de fechas vacías');

$runner->summary();
