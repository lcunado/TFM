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
if (!empty($_POST['hp_field_cancelaciones'])) {
    die("<p>⚠️ Detección de spam. Cancelación rechazada.</p>");
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

// Recoger los datos sin espacios
$id_reserva = trim($_POST['id'] ?? '');
$dni        = trim($_POST['dni'] ?? '');
$reembolso  = trim($_POST['reembolso'] ?? '');
$motivo     = trim($_POST['motivo'] ?? '');

// Validaciones de datos
// Validar ID numérico
if (!ctype_digit($id_reserva) || (int)$id_reserva <= 0) {
    die("<p>⚠️ ID de reserva inválido.</p>");
}

// Validar DNI o Pasaporte (letras y números, 5-20 chars)
if (!preg_match('/^[A-Za-z0-9]{5,20}$/', $dni)) {
    die("<p>⚠️ DNI o pasaporte no válido.</p>");
}

// Validar motivo (entre 5 y 500 caracteres)
if (strlen($motivo) < 5 || strlen($motivo) > 500) {
    die("<p>⚠️ El motivo debe tener entre 5 y 500 caracteres.</p>");
}

// Validar reembolso como número
if (!is_numeric($reembolso) || $reembolso < 0) {
    die("<p>⚠️ El importe de reembolso no es válido.</p>");
}
$motivo = htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8');

// Consultar la reserva
$stmtSelect = $conexion->prepare("SELECT nombre, apellidos, email, telefono, num_personas, fecha_entrada, fecha_salida, precio 
                                    FROM reservas 
                                    WHERE id = ? AND dni = ?");
$stmtSelect->bind_param("is", $id_reserva, $dni);
$stmtSelect->execute();
$result = $stmtSelect->get_result();
$reserva = $result->fetch_assoc();
$stmtSelect->close();

// Si existe la reserva
if ($reserva) {
    // Guardar datos en variables
    $nombre      = $reserva['nombre'];
    $apellidos   = $reserva['apellidos'];
    $email       = $reserva['email'];
    $telefono    = $reserva['telefono'];
    $num_personas= $reserva['num_personas'];
    $entrada     = $reserva['fecha_entrada'];
    $salida      = $reserva['fecha_salida'];
    $precio      = (float)$reserva['precio'];
    
    // Actualizar el estado de la reserva
    $stmtDelete = $conexion->prepare("UPDATE reservas SET estado = 'cancelado' WHERE id = ? AND dni = ?");
    $stmtDelete->bind_param("is", $id_reserva, $dni);

    if ($stmtDelete->execute()) {
        // Insertar en cancelaciones
        $estadoCancelacion = ($reembolso > 0) ? 'pendiente' : 'no_reembolsable';
        $stmtCancel = $conexion->prepare("INSERT INTO cancelaciones 
                (id_reserva, fecha_cancelacion, importe_pagado, importe_reembolsar, motivo, estado_cancelacion) 
                VALUES (?, NOW(), ?, ?, ?, ?)");
        $stmtCancel->bind_param("iddss", $id_reserva, $precio, $reembolso, $motivo, $estadoCancelacion);
        $stmtCancel->execute();
        $stmtCancel->close();

        echo "<p>✅ Reserva cancelada correctamente. Consulta tu correo.</p>";
        echo "<p>Se reembolsarán <strong>{$reembolso} €</strong>.</p>";

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
            $mail->Subject = "Cancelación de Reserva";
            $mail->isHTML(true);

            $mail->Body = "
                <html>
                    <body style='font-family: Arial, sans-serif; color: #333;'>
                        <h2>Hola $nombre,</h2>
                        <p>Tu reserva con ID <strong>$id_reserva</strong> ha sido <span style='color:red;'>cancelada</span>.</p>

                        <table style='border-collapse: collapse; margin: 20px 0; width: 100%;'>
                            <tr style='background-color: #f6f2f2;'>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Cliente:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$nombre $apellidos</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>DNI:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$dni</td>
                            </tr>
                            <tr style='background-color: #f6f2f2;'>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Email:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$email</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Teléfono:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$telefono</td>
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
                            <tr>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Reembolso:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$reembolso €</td>
                            </tr>
                            <tr style='background-color: #f6f2f2;'>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Motivo:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$motivo</td>
                            </tr>
                        </table>

                        <p>Si el pago se realizó correctamente, recibirá el reembolso en la misma cuenta.</p>
                        <p style='margin-top: 30px;'>Sentimos las molestias y esperamos verte pronto.</p>
                    </body>
                </html>
            ";
            $mail->send();

            // Correo al propietario
            $mail->clearAddresses();
            $mail->addAddress($propietarioEmail);
            $mail->Subject = "Reserva Cancelada";
            $mail->isHTML(true);

            $mail->Body = "
                <html>
                    <body style='font-family: Arial, sans-serif; color: #333;'>
                        <h2>Reserva Cancelada</h2>
                        <p>Se ha cancelado la siguiente reserva:</p>

                        <table style='border-collapse: collapse; margin: 20px 0; width: 100%;'>
                            <tr style='background-color: #f6f2f2;'>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>ID de reserva:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$id_reserva</td>
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
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Entrada:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$entrada</td>
                            </tr>
                            <tr style='background-color: #f6f2f2;'>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Salida:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$salida</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Precio:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$precio €</td>
                            </tr>
                            <tr style='background-color: #f6f2f2;'>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Reembolso:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$reembolso €</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; border: 1px solid #ccc;'><strong>Motivo:</strong></td>
                                <td style='padding: 8px; border: 1px solid #ccc;'>$motivo</td>
                            </tr>
                        </table>
                    </body>
                </html>
            ";
            $mail->send();

        } catch (Exception $e) {
            echo "<p>⚠️ Error al enviar correos: {$mail->ErrorInfo}</p>";
        }

    } else {
        echo "<p>⚠️ Error al cancelar la reserva: " . $stmtDelete->error . "</p>";
    }

    $stmtDelete->close();
} else {
    echo "<p>⚠️ No se encontró ninguna reserva con esos datos.</p>";
}

$conexion->close();
?>