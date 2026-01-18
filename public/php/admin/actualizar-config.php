<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config.php";

// Recoger datos
$localidad = $_POST['localidad'];
$vivienda = $_POST['vivienda'];
$direccion = $_POST['direccion'];
$latitud = $_POST['latitud'];
$longitud = $_POST['longitud'];

$informacionGeneral = json_encode(json_decode($_POST['informacionGeneral'], true), JSON_UNESCAPED_UNICODE);
$iconosIncluidos = json_encode(json_decode($_POST['iconosIncluidos'], true), JSON_UNESCAPED_UNICODE);
$politicasReserva = json_encode(json_decode($_POST['politicasReserva'], true), JSON_UNESCAPED_UNICODE);

// Actualizar BD
$sql = "UPDATE configuracion SET 
    localidad = '$localidad',
    vivienda = '$vivienda',
    direccion = '$direccion',
    latitud = '$latitud',
    longitud = '$longitud',
    informacionGeneral = '$informacionGeneral',
    iconosIncluidos = '$iconosIncluidos',
    politicasReserva = '$politicasReserva'
    WHERE id = 1";

$conexion->query($sql);

// Volver al panel
header("Location: configuracion.php?ok=1");
exit;


