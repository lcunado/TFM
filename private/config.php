<?php

// Contraseña email del propietario
$propietarioPassword = 'xxxx xxxx xxxx xxxx';

// Configuración de conexión a la base de datos
$db_host = "sql213.infinityfree.com";     // servidor de MySQL
$db_user = "if0_xxxxxxxx";      // usuario de la BD
$db_pass = "xxxxxxxxxxxx";      // contraseña de la BD
$db_name = "if0_xxxxxxxx_bd_pisoturistico";  // nombre de la BD

$conexion = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

// Stripe API Keys
$stripeSecretKey = "sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$stripePublicKey = "pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
// Webhook
$stripeWebhookSecret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

?>