<?php
session_start();
// Incluir configuración
require_once __DIR__ . "/config.php";

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

// Control tiempo, si tarda menos de 5 segundos es sospechoso
if (!isset($_SESSION['form_start'])) {
    $_SESSION['form_start'] = time();
}
$tiempoEnvio = time() - $_SESSION['form_start'];
if ($tiempoEnvio < 5) {
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

// Enviar email
$mail = new PHPMailer(true);

try {
    // Configuración SMTP (Outlook/Hotmail)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $propietarioEmail;
    $mail->Password   = $propietarioPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente (el usuario que envía el mensaje)
    $mail->setFrom($correo, $nombre);

    // Destinatario (el propietario)
    $mail->addAddress($propietarioEmail);

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
