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
    $id = $conexion->insert_id;
    echo "<p>✅ Reserva confirmada correctamente. Consulta tu correo.</p>";
    
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';   // Servidor SMTP de gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = $propietarioEmail;   
        $mail->Password   = $propietarioPassword;    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remitente
        $mail->setFrom($propietarioEmail, 'Reservas Piso Turistico');

        // Correo al usuario
        $mail->addAddress($email, $nombre);
        $mail->Subject = "Reserva Confirmada";
        $mail->Body    = "Hola $nombre,\n\nTu reserva ha sido confirmada.\n\n".
                         "Id: $id\n".
                         "Entrada: $entrada\n".
                         "Salida: $salida\n".
                         "Personas: $num_personas\n".
                         "Precio: $precio €\n\n".
                         "¡Gracias por reservar con nosotros!";
        $mail->send();

        // Correo al propietario
        $mail->clearAddresses();
        $mail->addAddress($propietarioEmail);
        $mail->Subject = "Nueva reserva confirmada";
        $mail->Body    = "Se ha confirmado una nueva reserva:\n\n".
                         "Id: $id\n".
                         "Cliente: $nombre $apellidos\n".
                         "DNI: $dni\n".
                         "Email: $email\n".
                         "Teléfono: $telefono\n".
                         "Entrada: $entrada\n".
                         "Salida: $salida\n".
                         "Personas: $num_personas\n".
                         "Precio: $precio €";
        $mail->send();

    } catch (Exception $e) {
        echo "<p>Error al enviar correos: {$mail->ErrorInfo}</p>";
    }

} else {
    echo "<p>Error al confirmar la reserva: " . $stmt->error . "</p>";
}

$stmt->close();
$conexion->close();
?>