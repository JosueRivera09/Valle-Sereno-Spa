<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/services/ClienteService.php';

class ClienteTest extends TestCase {
    private $mockModel;
    private $service;

    protected function setUp(): void {
        $this->mockModel = $this->createMock(Cliente::class);
        $this->service = new ClienteService($this->mockModel);
    }

    public function testValidarDatosCorrectos(): void {
        $data = [
            'nombre_completo' => 'Laura Elena Gómez',
            'fecha_nacimiento' => '1990-05-15',
            'telefono' => '555-1234',
            'correo' => 'laura@ejemplo.com'
        ];
        $res = $this->service->validarDatos($data);
        $this->assertTrue($res['valido']);
    }

    public function testValidarSinNombre(): void {
        $data = [
            'nombre_completo' => '',
            'fecha_nacimiento' => '1990-05-15',
            'telefono' => '555-1234'
        ];
        $res = $this->service->validarDatos($data);
        $this->assertFalse($res['valido']);
        $this->assertStringContainsString('nombre completo', $res['mensaje']);
    }

    public function testValidarCorreoInvalido(): void {
        $data = [
            'nombre_completo' => 'Carlos Pérez',
            'fecha_nacimiento' => '1985-10-20',
            'telefono' => '555-9876',
            'correo' => 'correo-invalido'
        ];
        $res = $this->service->validarDatos($data);
        $this->assertFalse($res['valido']);
        $this->assertStringContainsString('formato válido', $res['mensaje']);
    }

    public function testCrearClienteExitoso(): void {
        $data = [
            'nombre_completo' => 'Sofía Ramírez',
            'fecha_nacimiento' => '1995-12-01',
            'telefono' => '555-4321',
            'correo' => 'sofia@ejemplo.com',
            'estado' => 'activo'
        ];

        $this->mockModel->expects($this->once())
            ->method('crear')
            ->with($data)
            ->willReturn(true);

        $res = $this->service->crearCliente($data);
        $this->assertTrue($res['success']);
    }
}
