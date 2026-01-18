<?php
// Configuración de tarifas
$precioDiario   = 20;
$precioSabDom   = 40;
$precioLimpieza = 125;
$maxHuespedes   = 5;

// Email del propietario
$propietarioEmail = 'contactorefugiodelmirador@gmail.com';
$propietarioPassword = 'wjwx dagq kaxz xmjx';

// Número de cuenta bancaria
$numeroCuenta = "ES29 2100 0414 6102 0026 5167";

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