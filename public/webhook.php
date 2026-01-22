<?php
// Stripe Webhook — Confirmación de pago
require_once __DIR__ . '/private/config.php';
require_once __DIR__ . '/private/stripe-php-7/init.php';

//Incluir phpmailer
require __DIR__ . '/php/vendor/phpmailer/Exception.php'; 
require __DIR__ . '/php/vendor/phpmailer/PHPMailer.php'; 
require __DIR__ . '/php/vendor/phpmailer/SMTP.php';

// Envío de correos con PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

\Stripe\Stripe::setApiKey($stripeSecretKey);

// Leer el cuerpo del POST enviado por Stripe
$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$secret = $stripeWebhookSecret;

try {
    // Validar firma del webhook
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $secret
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit("⚠️ Payload inválido");
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit("⚠️ Firma no válida");
}

// Procesar evento
if ($event['type'] === 'checkout.session.completed') {

    $session = $event['data']['object'];
    $meta = $session['metadata'];
    
    // Recuperar datos de la reserva 
    $nombre_user       = $meta['nombre'];
    $apellidos_user    = $meta['apellidos'];
    $dni_user          = $meta['dni'];
    $email_user        = $meta['email'];
    $telefono_user     = $meta['telefono'];
    $num_personas_user = $meta['num_personas'];
    $entrada_user      = $meta['entrada'];
    $salida_user       = $meta['salida'];
    $precio_user       = $meta['precio'];
    $paymentIntent     = null; // Se envía después

    // Insertar reserva en la BD
    $stmt = $conexion->prepare("INSERT INTO reservas 
        (payment_intent, dni, nombre, apellidos, email, telefono, num_personas, fecha_entrada, fecha_salida, precio, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pagado')");

    $stmt->bind_param(
        "ssssssissd",
        $paymentIntent,
        $dni_user,
        $nombre_user,
        $apellidos_user,
        $email_user,
        $telefono_user,
        $num_personas_user,
        $entrada_user,
        $salida_user,
        $precio_user
    );

    $stmt->execute();
    $id = $conexion->insert_id;
    $stmt->close();

    // Enviar correos 
    
    $mail = new PHPMailer(true);

    $res = $conexion->query("SELECT email, dominio, vivienda 
                            FROM configuracion 
                            LIMIT 1");

    $config = $res->fetch_assoc();

    $emailPropietario = $config['email'];
    $dominio          = $config['dominio'];
    $vivienda          = $config['vivienda'];

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';   // Servidor SMTP de gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = $emailPropietario;   
        $mail->Password   = $propietarioPassword;    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remitente
        $mail->setFrom($emailPropietario, 'Reservas Alojamiento Turistico');

        // Correo al usuario
        $mail->addAddress($email_user, $nombre_user);
        $mail->Subject = "Reserva Confirmada";
        $mail->isHTML(true); // Activar formato HTML

        $mail->Body = "
            <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>Hola $nombre_user,</h2>
                    <p>Tu reserva en <strong>$vivienda</strong> ha sido <strong>confirmada</strong>. Aquí tienes los detalles:</p>

                    <table style='border-collapse: collapse; margin: 20px 0; width: 100%;'>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>ID de reserva:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$id</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Nombre:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$nombre_user $apellidos_user</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>DNI:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$dni_user</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Email:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$email_user</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Teléfono:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$telefono_user</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Total de personas:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$num_personas_user</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Fecha de entrada:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$entrada_user</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Fecha de salida:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$salida_user</td>
                        </tr>
                        <tr style='background-color: #f6f2f2;'>
                            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Total:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ccc;'>$precio_user €</td>
                        </tr>
                    </table>

                    <p>Si tienes alguna duda, no dudes en contactarnos:</p>
                    <p>
                        <a href='$dominio/contacto.html' style='color:#0066cc;'> 
                        $vivienda/contacto 
                        </a>
                    </p>
                    <p style='margin-top: 30px;'>¡Gracias por reservar con nosotros!</p>
                </body>
            </html>
        ";

        $mail->send();

        // Correo al propietario
        $mail->clearAddresses();
        $mail->addAddress($emailPropietario);
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
                        <td style='padding: 8px; border: 1px solid #ccc;'>$nombre_user $apellidos_user</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>DNI:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$dni_user</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Email:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$email_user</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Teléfono:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$telefono_user</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Personas:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$num_personas_user</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Entrada:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$entrada_user</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Salida:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$salida_user</td>
                    </tr>
                    <tr style='background-color: #f6f2f2;'>
                        <td style='padding: 8px; border: 1px solid #ccc;'><strong>Precio:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ccc;'>$precio_user €</td>
                    </tr>
                    </table>

                </body>
            </html>
        ";

        $mail->send();

    } catch (Exception $e) {
        echo "<p>⚠️ Error al enviar correos: {$mail->ErrorInfo}</p>";
    }

    http_response_code(200);
    exit;
}

// Recuperar el payment intent
if ($event['type'] === 'payment_intent.succeeded') { 
    $pi = $event['data']['object']; 
    $paymentIntentId = $pi['id']; 

    // Recuperar DNI desde metadata 
    $dni = $pi['metadata']['dni']; 
    if ($dni) { 
        // Actualizar la última reserva creada con ese DNI 
        $stmt = $conexion->prepare(" UPDATE reservas 
                                    SET payment_intent = ? 
                                    WHERE dni = ? 
                                    ORDER BY id 
                                    DESC LIMIT 1 "); 
        $stmt->bind_param("ss", $paymentIntentId, $dni); 
        $stmt->execute(); 
        $stmt->close(); 
    } 
    
    http_response_code(200); 
    exit; 
} 

http_response_code(200); 
exit;