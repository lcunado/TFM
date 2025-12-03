<?php
// Incluir configuración
require_once "config.php";

// Recoger datos del POST
$dni         = $_POST['dni'] ?? '';
$nombre      = $_POST['nombre'] ?? '';
$apellidos   = $_POST['apellidos'] ?? '';
$email       = $_POST['email'] ?? '';
$telefono    = $_POST['telefono'] ?? '';
$num_personas= (int)($_POST['personas'] ?? 0);
$entrada     = $_POST['entrada'] ?? '';
$salida      = $_POST['salida'] ?? '';
$precio      = (float)($_POST['precio'] ?? 0);

// Preparar sentencia
$stmt = $conexion->prepare("INSERT INTO reservas 
    (dni, nombre, apellidos, email, telefono, num_personas, fecha_entrada, fecha_salida, precio) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssisssd", 
    $dni, $nombre, $apellidos, $email, $telefono, $num_personas, $entrada, $salida, $precio
);

if ($stmt->execute()) {
    echo "<p>✅ Reserva confirmada correctamente.</p>";
} else {
    echo "<p>Error al confirmar la reserva: " . $stmt->error . "</p>";
}

$stmt->close();
$conexion->close();
?>