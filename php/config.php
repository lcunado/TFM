<?php
// Configuración de tarifas
$precioDiario   = 60;
$precioSabDom   = 90;
$precioLimpieza = 125;

// Configuración de conexión a la base de datos
$db_host = "sql213.infinityfree.com";     // servidor de MySQL
$db_user = "if0_40560777";      // usuario de la BD
$db_pass = "L5zSDKLVVmAk";      // contraseña de la BD
$db_name = "if0_40560777_bd_pisoturistico";  // nombre de la BD

$conexion = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>