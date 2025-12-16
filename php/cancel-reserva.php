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

$id_reserva = $_POST['id'] ?? 0;
$dni        = $_POST['dni'] ?? '';
$reembolso  = $_POST['reembolso'] ?? 0;

if ($id_reserva > 0 && !empty($dni)) {
    // Consultar la reserva
    $stmtSelect = $conexion->prepare("SELECT nombre, apellidos, email, telefono, num_personas, fecha_entrada, fecha_salida, precio 
                                      FROM reservas 
                                      WHERE id = ? AND dni = ?");
    $stmtSelect->bind_param("is", $id_reserva, $dni);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    $reserva = $result->fetch_assoc();
    $stmtSelect->close();

    if ($reserva) {
        // Guardar datos en variables
        $nombre      = $reserva['nombre'];
        $apellidos   = $reserva['apellidos'];
        $email       = $reserva['email'];
        $telefono    = $reserva['telefono'];
        $num_personas= $reserva['num_personas'];
        $entrada     = $reserva['fecha_entrada'];
        $salida      = $reserva['fecha_salida'];
        $precio      = $reserva['precio'];
    
        // Eliminar la reserva
        $stmtDelete = $conexion->prepare("DELETE FROM reservas WHERE id = ? AND dni = ?");
        $stmtDelete->bind_param("is", $id_reserva, $dni);

        if ($stmtDelete->execute()) {
            echo "<p>✅ Reserva cancelada correctamente. Consulta tu correo.</p>";
            echo "<p>Si se ha realizado el pago, se reembolsarán <strong>{$reembolso} €</strong>.</p>";

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
                            </table>
                        </body>
                    </html>
                ";
                $mail->send();

            } catch (Exception $e) {
                echo "<p>Error al enviar correos: {$mail->ErrorInfo}</p>";
            }

        } else {
            echo "<p>Error al cancelar la reserva: " . $stmtDelete->error . "</p>";
        }

        $stmtDelete->close();
    } else {
        echo "<p>No se encontró ninguna reserva con esos datos.</p>";
    }
} else {
    echo "<p>Datos de cancelación incompletos.</p>";
}

$conexion->close();
?>