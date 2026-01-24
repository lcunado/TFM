<?php
require_once __DIR__ . '/../private/config.php';

// Comprobar si ya está instalado
$existe = $conexion->query("SHOW TABLES LIKE 'configuracion'");

if ($existe && $existe->num_rows > 0) {
    header("Location: ../php/admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Instalación</title>
</head>
<body>
    <h2>Instalación del sistema</h2>
    <p>No se ha detectado ninguna instalación previa.</p>
    <a href="crear-tablas.php">Iniciar instalación</a>
</body>
</html>
