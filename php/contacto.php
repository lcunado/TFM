<?php
// Incluir configuración
require_once "config.php";

//Incluir phpmailer
require 'vendor/phpmailer/Exception.php';
require 'vendor/phpmailer/PHPMailer.php';
require 'vendor/phpmailer/SMTP.php';

// Envío de correos con PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Recoger datos del POST
$nombre  = $_POST['nombre'] ?? '';
$correo  = $_POST['correo'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

if ($nombre && $correo && $mensaje) {
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
} else {
    echo "<p>⚠️ Faltan datos en el formulario.</p>";
}