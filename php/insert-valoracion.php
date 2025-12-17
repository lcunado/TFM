<?php
require_once "config.php";

// Recoger datos del formulario
$nombre       = $_POST["nombre"]       ?? "";
$general      = $_POST["general"]      ?? 0;
$limpieza     = $_POST["limpieza"]     ?? 0;
$veracidad    = $_POST["veracidad"]    ?? 0;
$llegada      = $_POST["llegada"]      ?? 0;
$comunicacion = $_POST["comunicacion"] ?? 0;
$ubicacion    = $_POST["ubicacion"]    ?? 0;
$calidad      = $_POST["calidad"]      ?? 0;
$comentario   = $_POST["comentario"]   ?? "";

// Validación básica
if (empty($nombre) || $general == 0) {
    die("<p>⚠️ Error: datos incompletos.</p>");
}

// Insertar en la base de datos
$stmt = $conexion->prepare("
    INSERT INTO valoraciones 
    (nombre, general, limpieza, veracidad, llegada, comunicacion, ubicacion, calidad, comentario, fecha_valoracion)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "siiiiiiis",
    $nombre,
    $general,
    $limpieza,
    $veracidad,
    $llegada,
    $comunicacion,
    $ubicacion,
    $calidad,
    $comentario
);

if ($stmt->execute()) {
    echo "<p>✅ ¡Tu valoración se ha guardado correctamente!</p>";
} else {
    echo "<p>⚠️ Error al guardar la valoración: " . $stmt->error . "</p>";
}

$stmt->close();
$conexion->close();
?>