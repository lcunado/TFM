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