<?php
// Stripe Webhook — Confirmación de pago
require_once __DIR__ . '/private/config.php';
require_once __DIR__ . '/private/stripe-php-7/init.php';

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

    // Recuperar datos de la reserva 
    
    $meta = $session['metadata'];

    // Insertar reserva en la BD
    $stmt = $conexion->prepare("INSERT INTO reservas 
        (dni, nombre, apellidos, email, telefono, num_personas, fecha_entrada, fecha_salida, precio, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pagado')");

    $stmt->bind_param(
        "ssssisssd",
        $meta['dni'],
        $meta['nombre'],
        $meta['apellidos'],
        $meta['email'],
        $meta['telefono'],
        $meta['num_personas'],
        $meta['entrada'],
        $meta['salida'],
        $meta['precio']
    );

    $stmt->execute();
    $stmt->close();

    // Enviar correos 


}

http_response_code(200);
echo "OK";
