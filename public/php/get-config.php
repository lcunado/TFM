<?php
header('Content-Type: application/json; charset=utf-8');

// Conexión a la base de datos
require_once __DIR__ . "/config.php";

// Obtener el registro único de configuración
$sql = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
$result = $conexion->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(["error" => "No se encontró configuración"]);
    exit;
}

$config = $result->fetch_assoc();

// Convertir los campos JSON almacenados como texto en arrays/objetos reales
$config['informacionGeneral'] = json_decode($config['informacionGeneral'], true);
$config['iconosIncluidos'] = json_decode($config['iconosIncluidos'], true);
$config['politicasReserva'] = json_decode($config['politicasReserva'], true);
$config['galeria'] = json_decode($config['galeria'], true);

// Devolver todo como JSON
echo json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
