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
    echo "<p>✅ Reserva confirmada correctamente. Consulta tu correo.</p>";
    /*
    // --- Envío de correos con PHPMailer ---
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';        // Servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = $propietarioEmail;   
        $mail->Password   = 'TU_APP_PASSWORD';      
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remitente
        $mail->setFrom($propietarioEmail, 'Reservas Piso Burgos');

        // --- Correo al usuario ---
        $mail->addAddress($email, $nombre);
        $mail->Subject = "Confirmación de tu reserva en Piso Turístico Burgos";
        $mail->Body    = "Hola $nombre,\n\nTu reserva ha sido confirmada.\n\n".
                         "Entrada: $entrada\n".
                         "Salida: $salida\n".
                         "Personas: $num_personas\n".
                         "Precio: $precio €\n\n".
                         "¡Gracias por reservar con nosotros!";
        $mail->send();

        // --- Correo al propietario ---
        $mail->clearAddresses();
        $mail->addAddress($propietarioEmail);
        $mail->Subject = "Nueva reserva confirmada";
        $mail->Body    = "Se ha confirmado una nueva reserva:\n\n".
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
    }*/

} else {
    echo "<p>Error al confirmar la reserva: " . $stmt->error . "</p>";
}

$stmt->close();
$conexion->close();
?>