<?php
session_start();
// Incluir configuración
require_once __DIR__ . '/../private/config.php';

//Incluir phpmailer
require 'vendor/phpmailer/Exception.php';
require 'vendor/phpmailer/PHPMailer.php';
require 'vendor/phpmailer/SMTP.php';

// Envío de correos con PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Recoger datos del POST
$nombre    = trim($_POST['nombre'] ?? '');
$correo    = trim($_POST['correo'] ?? '');
$mensaje   = trim($_POST['mensaje'] ?? '');
$hp_field  = trim($_POST['hp_field_contacto'] ?? '');

// Control Honeypot, si el campo oculto tiene contenido es spam
if (!empty($hp_field)) {
    die("<p>⚠️ Detección de spam. Envío rechazado.</p>");
}

// Control tiempo, si tarda menos de 3 segundos es sospechoso
if (!isset($_SESSION['form_start'])) {
    $_SESSION['form_start'] = time();
}
$tiempoEnvio = time() - $_SESSION['form_start'];
if ($tiempoEnvio < 3) {
    die("<p>⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.</p>");
}
$_SESSION['form_start'] = time(); // Reinicio del tiempo

// Validación de campos
// Nombre: solo letras y espacios, 2–40 caracteres
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/', $nombre)) {
    die("<p>⚠️ El nombre no es válido.</p>");
}

// Correo: formato estándar
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("<p>⚠️ El correo electrónico no es válido.</p>");
}

// Mensaje: entre 5 y 500 caracteres
if (strlen($mensaje) < 5 || strlen($mensaje) > 500) {
    die("<p>⚠️ El mensaje debe tener entre 5 y 500 caracteres.</p>");
}
$mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');

// Obtener email del propietario desde la BD 
$stmt = $conexion->prepare(" 
    SELECT email 
    FROM configuracion 
    WHERE id = 1 LIMIT 1 
"); 
$stmt->execute(); 
$stmt->bind_result($emailPropietario); 
$stmt->fetch(); 
$stmt->close(); 

if (!$emailPropietario) { 
    die("<p>⚠️ Error al obtener el email del propietario.</p>"); 
}

// Enviar email
$mail = new PHPMailer(true);

try {
    // Configuración SMTP 
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $emailPropietario;
    $mail->Password   = $propietarioPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente (el usuario que envía el mensaje)
    $mail->setFrom($correo, $nombre);

    // Destinatario (el propietario)
    $mail->addAddress($emailPropietario);

    // Contenido
    $mail->Subject = "Nuevo mensaje de contacto";
    $mail->Body    = "Has recibido un nuevo mensaje:\n\n".
                    "Nombre: $nombre\n".
                    "Correo: $correo\n\n".
                    "Mensaje:\n$mensaje";

    $mail->send();
    echo "<p>✅ Mensaje enviado correctamente. Gracias por contactarnos.</p>";
} catch (Exception $e) {
    echo "<p>⚠️ Error al enviar el mensaje: {$mail->ErrorInfo}</p>";
}
