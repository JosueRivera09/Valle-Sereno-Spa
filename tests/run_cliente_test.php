<?php
/**
 * Test Runner sencillo sin dependencias externas para el entorno XAMPP
 */
require_once __DIR__ . '/../app/services/ClienteService.php';

class FakeClienteModel extends Cliente {
    public function __construct() {}
    public function crear(array $data): bool {
        return true;
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
        echo "RESULTADOS DEL TEST: {$this->passed} pasados, {$this->failed} fallidos.\n";
        echo "----------------------------------------\n";
    }
}

$runner = new SimpleRunner();
$fakeModel = new FakeClienteModel();
$service = new ClienteService($fakeModel);

// 1. Test Validar Datos Correctos
$res1 = $service->validarDatos([
    'nombre_completo' => 'Laura Elena Gómez',
    'fecha_nacimiento' => '1990-05-15',
    'telefono' => '555-1234',
    'correo' => 'laura@ejemplo.com'
]);
$runner->assert($res1['valido'] === true, 'Validación de datos correctos de cliente');

// 2. Test Nombre Vacío
$res2 = $service->validarDatos([
    'nombre_completo' => '',
    'fecha_nacimiento' => '1990-05-15',
    'telefono' => '555-1234'
]);
$runner->assert($res2['valido'] === false, 'Rechazo de cliente sin nombre');

// 3. Test Correo Formato Inválido
$res3 = $service->validarDatos([
    'nombre_completo' => 'Carlos Pérez',
    'fecha_nacimiento' => '1985-10-20',
    'telefono' => '555-9876',
    'correo' => 'correo-invalido'
]);
$runner->assert($res3['valido'] === false, 'Rechazo de cliente con correo inválido');

// 4. Test Crear Cliente
$res4 = $service->crearCliente([
    'nombre_completo' => 'Sofía Ramírez',
    'fecha_nacimiento' => '1995-12-01',
    'telefono' => '555-4321',
    'correo' => 'sofia@ejemplo.com'
]);
$runner->assert($res4['success'] === true, 'Registro exitoso de cliente');

$runner->summary();
