<?php
require_once __DIR__ . '/../app/services/ServicioService.php';

class FakeServicioModel extends ServicioModel {
    public function __construct() {}
    public function crearServicio(array $data): bool { return true; }
    public function crearCategoria(array $data): bool { return true; }
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
        echo "RESULTADOS SERVICIOS TEST: {$this->passed} pasados, {$this->failed} fallidos.\n";
        echo "----------------------------------------\n";
    }
}

$runner = new SimpleRunner();
$service = new ServicioService(new FakeServicioModel());

// 1. Validar Servicio Correcto
$res1 = $service->validarServicio([
    'id_categoria' => 1,
    'nombre' => 'Masaje Relajante',
    'costo' => 850.00,
    'duracion_minutos' => 60
]);
$runner->assert($res1['valido'] === true, 'Validación de servicio válido');

// 2. Rechazo por Categoría Inválida
$res2 = $service->validarServicio([
    'id_categoria' => 0,
    'nombre' => 'Masaje Relajante',
    'costo' => 850.00,
    'duracion_minutos' => 60
]);
$runner->assert($res2['valido'] === false, 'Rechazo de servicio sin categoría');

// 3. Rechazo por Duración Negativa o 0
$res3 = $service->validarServicio([
    'id_categoria' => 1,
    'nombre' => 'Facial Hidratante',
    'costo' => 600.00,
    'duracion_minutos' => 0
]);
$runner->assert($res3['valido'] === false, 'Rechazo por duración de 0 minutos');

// 4. Creación de Categoría sin Nombre
$res4 = $service->crearCategoria(['nombre' => '']);
$runner->assert($res4['success'] === false, 'Rechazo de categoría con nombre vacío');

$runner->summary();
