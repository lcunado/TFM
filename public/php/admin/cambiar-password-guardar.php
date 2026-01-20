<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../private/config.php';

// Función para validar contraseñas seguras 
function password_segura($pass) { 
    if (strlen($pass) < 8) return false; 
    if (!preg_match('/[A-Z]/', $pass)) return false; 
    if (!preg_match('/[a-z]/', $pass)) return false; 
    if (!preg_match('/[0-9]/', $pass)) return false; 
    if (!preg_match('/[\W_]/', $pass)) return false; 
    return true; 
}
// Recoger datos del formulario
$actual = $_POST['actual'] ?? '';
$nueva = $_POST['nueva'] ?? '';
$repetir = $_POST['repetir'] ?? '';

// Validar que las nuevas coinciden
if ($nueva !== $repetir) {
    header("Location: cambiar-password.php?error=Las contraseñas no coinciden");
    exit;
}

// Validar seguridad de la nueva contraseña 
if (!password_segura($nueva)) { 
    header("Location: cambiar-password.php?error=La contraseña debe tener al menos 8 caracteres, mayúsculas, minúsculas, números y símbolos."); 
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
