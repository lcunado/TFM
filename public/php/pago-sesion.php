<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
require_once __DIR__ . "/../private/config.php";
require_once __DIR__ . "/../private/stripe-php-7/init.php";

\Stripe\Stripe::setApiKey($stripeSecretKey);

// Leer dominio desde la BD
$result = $conexion->query("SELECT dominio FROM configuracion LIMIT 1");
$dom = $result->fetch_assoc();
$dominio = $dom['dominio'];

// Obtener el precio 
if (!isset($_SESSION['reserva']['precio'])) { 
    die("⚠️ No se encontró el precio de la reserva."); 
} 
$precioEuros = floatval($_SESSION['reserva']['precio']); 
$precioCentimos = intval($precioEuros * 100); // Stripe usa céntimos

// Crear sesión de pago
$session = \Stripe\Checkout\Session::create([
    "payment_method_types" => ["card"],
    "line_items" => [[
        "price_data" => [
            "currency" => "eur",
            "product_data" => [
                "name" => "Reserva Vivienda Turística"
            ],
            "unit_amount" => $precioCentimos,
        ],
        "quantity" => 1,
    ]],
    "mode" => "payment",
    "success_url" => $dominio . "/php/pago-success.php",
    "cancel_url"  => $dominio . "/php/pago-cancel.php",

    // Enviar los datos de la reserva a Stripe 
    "metadata" => [ 
        "dni" => $_SESSION['reserva']['dni'], 
        "nombre" => $_SESSION['reserva']['nombre'], 
        "apellidos" => $_SESSION['reserva']['apellidos'], 
        "email" => $_SESSION['reserva']['email'], 
        "telefono" => $_SESSION['reserva']['telefono'], 
        "num_personas" => $_SESSION['reserva']['num_personas'], 
        "entrada" => $_SESSION['reserva']['entrada'], 
        "salida" => $_SESSION['reserva']['salida'], 
        "precio" => $_SESSION['reserva']['precio'] 
    ]
    "expand" => ["payment_intent"]
]);

header("Location: " . $session->url);
exit;
