<?php
session_start();
require_once __DIR__ . '/../private/config.php';

// Recoger datos del formulario
$nombre       = trim($_POST["nombre"] ?? "");
$general      = trim($_POST["general"] ?? 0);
$limpieza     = trim($_POST["limpieza"] ?? 0);
$veracidad    = trim($_POST["veracidad"] ?? 0);
$llegada      = trim($_POST["llegada"] ?? 0);
$comunicacion = trim($_POST["comunicacion"] ?? 0);
$ubicacion    = trim($_POST["ubicacion"] ?? 0);
$calidad      = trim($_POST["calidad"] ?? 0);
$comentario   = trim($_POST["comentario"] ?? "");
$hp_field     = trim($_POST["hp_field_valoraciones"] ?? "");

// Validaciones
// Nombre
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/', $nombre)) {
    die("<p>⚠️ El nombre no es válido.</p>");
}

// General
if (!preg_match('/^[1-5]$/', $general)) {
    die("<p>⚠️ La valoración general es obligatoria y debe ser entre 1 y 5.</p>");
}

// Comentario
if (strlen($comentario) < 5 || strlen($comentario) > 500) {
    die("<p>⚠️ El comentario debe tener entre 5 y 500 caracteres.</p>");
}
$comentario = htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8');

// Control Honeypot, si el campo oculto tiene contenido es spam
if (!empty($hp_field)) {
    die("<p>⚠️ Detección de spam. Valoración rechazada.</p>");
}

// Control tiempo, si tarda menos de 5 segundos es sospechoso
if (!isset($_SESSION['form_start'])) {
    $_SESSION['form_start'] = time();
}
$tiempoEnvio = time() - $_SESSION['form_start'];
if ($tiempoEnvio < 5) {
    die("<p>⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.</p>");
}
$_SESSION['form_start'] = time(); // Reinicio del tiempo

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