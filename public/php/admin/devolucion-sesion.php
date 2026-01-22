<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../../private/config.php';
require_once __DIR__ . '/../../private/stripe-php-7/init.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);

// Validar datos recibidos
if (!isset($_POST['id_cancelacion'], $_POST['id_reserva'])) {
    header("Location: cancelaciones.php?refund=error&msg=Datos incompletos");
    exit;
}

$idCancelacion = intval($_POST['id_cancelacion']);
$idReserva = intval($_POST['id_reserva']);

// Obtener payment_intent
$sql = "SELECT payment_intent FROM reservas WHERE id_reserva = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idReserva);
$stmt->execute();
$stmt->bind_result($paymentIntent);
$stmt->fetch();
$stmt->close();

if (!$paymentIntent) {
    header("Location: cancelaciones.php?refund=error&msg=No se encontró el payment_intent");
    exit;
}

// Obtener importe a reembolsar
$sql = "SELECT importe_reembolsar FROM cancelaciones WHERE id_cancelacion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCancelacion);
$stmt->execute();
$stmt->bind_result($importeReembolsar);
$stmt->fetch();
$stmt->close();

if ($importeReembolsar === null) {
    header("Location: cancelaciones.php?refund=error&msg=No se encontró el importe a reembolsar");
    exit;
}

$importeCents = intval($importeReembolsar * 100);

// Realizar devolución en Stripe
try {
    $refund = \Stripe\Refund::create([
        'payment_intent' => $paymentIntent,
        'amount' => $importeCents
    ]);

} catch (Exception $e) {
    $error = urlencode($e->getMessage());
    header("Location: cancelaciones.php?refund=error&msg=$error");
    exit;
}

// Actualizar BD
$sql = "UPDATE cancelaciones 
        SET estado_cancelacion = 'reembolsada', fecha_reembolso = NOW() 
        WHERE id_cancelacion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCancelacion);
$stmt->execute();
$stmt->close();

// Redirigir con éxito
header("Location: cancelaciones.php?refund=ok");
exit;






