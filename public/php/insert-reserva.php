<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Incluir configuración
require_once __DIR__ . '/../private/config.php';

// Control Honeypot, si el campo oculto tiene contenido es spam
if (!empty($_POST['hp_field_reservas'])) {
    die("<p>⚠️ Detección de spam. Reserva rechazada.</p>");
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

// Recoger datos del POST
$dni         = trim($_POST['dni'] ?? '');
$nombre      = trim($_POST['nombre'] ?? '');
$apellidos   = trim($_POST['apellidos'] ?? '');
$email       = trim($_POST['email'] ?? '');
$telefono    = trim($_POST['telefono'] ?? '');
$num_personas= (int)($_POST['personas'] ?? 0);
$entrada     = trim($_POST['entrada'] ?? '');
$salida      = trim($_POST['salida'] ?? '');
$precio      = trim($_POST['precio'] ?? '');

// Validaciones de datos
// DNI o Pasaporte
if (!preg_match('/^[A-Za-z0-9]{5,20}$/', $dni)) {
    die("<p>⚠️ DNI o pasaporte no válido.</p>");
}

// Nombre
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/', $nombre)) {
    die("<p>⚠️ El nombre no es válido.</p>");
}

// Apellidos
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,60}$/', $apellidos)) {
    die("<p>⚠️ Los apellidos no son válidos.</p>");
}

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("<p>⚠️ El correo electrónico no es válido.</p>");
}

// Teléfono
if (!preg_match('/^[0-9]{9,15}$/', $telefono)) {
    die("<p>⚠️ El teléfono debe tener entre 9 y 15 números.</p>");
}

// Número de personas
if ($num_personas < 1) {
    die("<p>⚠️ Número de personas no válido.</p>");
}

// Fechas
if (!$entrada || !$salida) {
    die("<p>⚠️ Debes seleccionar fechas válidas.</p>");
}

if ($entrada >= $salida) {
    die("<p>⚠️ La fecha de salida debe ser posterior a la de entrada.</p>");
}

// Precio (float positivo)
if (!preg_match('/^\d+(\.\d{1,2})?$/', $precio)) {
    die("<p>⚠️ El precio no es válido.</p>");
}

$precio = (float)$precio;

// Guardar datos en sesión
$_SESSION['reserva'] = [ 
    'dni' => $dni, 
    'nombre' => $nombre, 
    'apellidos' => $apellidos, 
    'email' => $email, 
    'telefono' => $telefono, 
    'num_personas' => $num_personas, 
    'entrada' => $entrada, 
    'salida' => $salida, 
    'precio' => $precio 
];

// Devuelve json
echo json_encode(["ok" => true]);
exit;
