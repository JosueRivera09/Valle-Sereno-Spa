<?php
require_once __DIR__ . '/../app/models/Database.php';

$db = Database::getConnection();
$hash = password_hash('Admin123*', PASSWORD_BCRYPT);

$stmt = $db->prepare("UPDATE usuarios SET password_hash = :h");
$stmt->execute(['h' => $hash]);

echo "Usuarios actualizados correctamente con hash válido.\n";

$check = $db->query("SELECT u.usuario, u.password_hash, r.nombre as rol, e.correo FROM usuarios u JOIN roles r ON u.id_rol = r.id LEFT JOIN empleados e ON u.id_empleado = e.id");
while($row = $check->fetch(PDO::FETCH_ASSOC)) {
    echo "Usuario: {$row['usuario']} | Rol: {$row['rol']} | Correo: {$row['correo']}\n";
}
