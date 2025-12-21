<?php
session_start();
// Incluir configuración
require_once "config.php";

//Incluir phpmailer
require 'vendor/phpmailer/Exception.php';
require 'vendor/phpmailer/PHPMailer.php';
require 'vendor/phpmailer/SMTP.php';

// Envío de correos con PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Insertar en la BD
$stmt = $conexion->prepare("INSERT INTO reservas 
    (dni, nombre, apellidos, email, telefono, num_personas, fecha_entrada, fecha_salida, precio) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssisssd", 
    $dni, $nombre, $apellidos, $email, $telefono, $num_personas, $entrada, $salida, $precio
);

if ($stmt->execute()) {
    $id = $conexion->insert_id;
    echo "<p>✅ Reserva confirmada correctamente. Consulta tu correo.</p>";
    
    // Enviar correos
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
        $mail->isHTML(true); // Activar formato HTML

        $mail->Body = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>Hola $nombre,</h2>
                    <p>Tu reserva ha sido <strong>confirmada</strong>. Aquí tienes los detalles:</p>

                    <table style='border-collapse: collapse; margin: 20px 0; width: 100%;'>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>ID de reserva:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$id</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Nombre:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$nombre $apellidos</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>DNI:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$dni</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Email:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$email</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Teléfono:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$telefono</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Total de personas:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$num_personas</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Fecha de entrada:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$entrada</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Fecha de salida:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$salida</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Total:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$precio €</td>
                        </tr>
                    </table>

                    <p><strong>Recuerda:</strong> dispones de 24 horas para realizar el pago de la estancia.</p>
                    <p>Número de cuenta: <strong>$numeroCuenta</strong></p>
                    <p>Si tienes alguna duda, no dudes en contactarnos.</p>
                    <p style='margin-top: 30px;'>¡Gracias por reservar con nosotros!</p>
                </body>
            </html>
        ";

        $mail->send();

        // Correo al propietario
        $mail->clearAddresses();
        $mail->addAddress($propietarioEmail);
        $mail->Subject = "Nueva reserva confirmada";
        $mail->isHTML(true); // Activar formato HTML

        $mail->Body = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>Nueva reserva confirmada</h2>
                    <p>Se ha confirmado una nueva reserva con los siguientes detalles:</p>

                    <table style='border-collapse: collapse; margin: 20px 0; width: 100%;'>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>ID de reserva:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$id</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Cliente:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$nombre $apellidos</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>DNI:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$dni</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Email:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$email</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Teléfono:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$telefono</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Personas:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$num_personas</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Entrada:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$entrada</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Salida:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$salida</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Precio:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$precio €</td>
                    </tr>
                    </table>

                    <p><strong>Número de cuenta para el pago:</strong> $numeroCuenta</p>
                </body>
            </html>
        ";

        $mail->send();

    } catch (Exception $e) {
        echo "<p>⚠️ Error al enviar correos: {$mail->ErrorInfo}</p>";
    }

} else {
    echo "<p>⚠️ Error al confirmar la reserva: " . $stmt->error . "</p>";
}

$stmt->close();
$conexion->close();
?>