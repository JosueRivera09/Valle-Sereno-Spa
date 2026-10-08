<?php
require_once __DIR__ . '/../app/services/PagoService.php';

class FakePagoModel extends PagoModel {
    public function __construct() {}
    public function registrarPago(array $data): bool { return true; }
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
        echo "RESULTADOS PAGOS TEST: {$this->passed} pasados, {$this->failed} fallidos.\n";
        echo "----------------------------------------\n";
    }
}

$runner = new SimpleRunner();
$service = new PagoService(new FakePagoModel());

// 1. Validar Pago Correcto
$res1 = $service->validarPago([
    'id_cita' => 5,
    'tipo_pago' => 'Efectivo',
    'monto' => 450.00
]);
$runner->assert($res1['valido'] === true, 'Validación de cobro válido');

// 2. Rechazo por Cita Inválida
$res2 = $service->validarPago([
    'id_cita' => 0,
    'tipo_pago' => 'Efectivo',
    'monto' => 450.00
]);
$runner->assert($res2['valido'] === false, 'Rechazo de cobro sin cita seleccionada');

// 3. Rechazo por Método de Pago No Soportado
$res3 = $service->validarPago([
    'id_cita' => 5,
    'tipo_pago' => 'Criptomoneda',
    'monto' => 450.00
]);
$runner->assert($res3['valido'] === false, 'Rechazo de método de pago inválido');

// 4. Rechazo por Monto 0 o Negativo
$res4 = $service->validarPago([
    'id_cita' => 5,
    'tipo_pago' => 'Tarjeta',
    'monto' => 0.00
]);
$runner->assert($res4['valido'] === false, 'Rechazo de cobro con monto de $0.00');

$runner->summary();
