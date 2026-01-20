<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../private/config.php';

// Recoger datos del formulario
$actual = $_POST['actual'] ?? '';
$nueva = $_POST['nueva'] ?? '';
$repetir = $_POST['repetir'] ?? '';

// Validar que las nuevas coinciden
if ($nueva !== $repetir) {
    header("Location: cambiar-password.php?error=Las contraseñas no coinciden");
    exit;
}

// Obtener contraseña actual de la BD
$sql = "SELECT password_hash FROM admin WHERE id = 1 LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();
$admin = $resultado->fetch_assoc();

if (!$admin) {
    header("Location: cambiar-password.php?error=Error interno");
    exit;
}

// Verificar contraseña actual
if (!password_verify($actual, $admin['password_hash'])) {
    header("Location: cambiar-password.php?error=La contraseña actual no es correcta");
    exit;
}

// Guardar nueva contraseña
$nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);

$sql = "UPDATE admin SET password_hash = ? WHERE id = 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $nuevoHash);
$stmt->execute();

header("Location: cambiar-password.php?ok=1");
exit;
