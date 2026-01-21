<?php
session_start();
require_once __DIR__ . '/../private/config.php';

//Recoger los datos
$id_reserva = trim($_POST['id'] ?? '');
$dni        = trim($_POST['dni'] ?? '');
$motivo     = trim($_POST['motivo'] ?? '');
$hp_field   = trim($_POST['hp_field_cancelaciones'] ?? '');

// Control Honeypot, si el campo oculto tiene contenido es spam
if (!empty($hp_field)) {
    die("<p>⚠️ Detección de spam. Petición rechazada.</p>");
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

// Validaciones de datos
// ID 
if (!ctype_digit($id_reserva) || (int)$id_reserva <= 0) {
    die("<p>⚠️ ID de reserva no válido.</p>");
}

// DNI o pasaporte
if (!preg_match('/^[A-Za-z0-9]{5,20}$/', $dni)) {
    die("<p>⚠️ DNI o pasaporte no válido.</p>");
}

// Motivo
if (empty($motivo) || strlen($motivo) < 5 || strlen($motivo) > 500) {
    die("<p>⚠️ Debes indicar un motivo válido (entre 5 y 500 caracteres).</p>");
}
$motivo = htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8');

// Consultar la reserva
$stmt = $conexion->prepare("SELECT id, dni, fecha_entrada, precio, estado 
                            FROM reservas 
                            WHERE id = ? AND dni = ?");
$stmt->bind_param("is", $id_reserva, $dni);
$stmt->execute();
$resultado = $stmt->get_result();

// Error si no existe la reserva
if ($resultado->num_rows === 0) {
    die("<p>⚠️ No se encontró ninguna reserva con ese ID y DNI.</p>");
}

$reserva = $resultado->fetch_assoc();
$stmt->close();

// Validaciones de estado
$precioOriginal = (float)$reserva['precio'];
$fechaEntrada   = new DateTime($reserva['fecha_entrada']);
$hoy            = new DateTime();

// Error si ya está cancelada
if ($reserva['estado'] === 'cancelado') {
    die("<p>⚠️ La reserva con ID {$reserva['id']} ya está cancelada.</p>");
}

// Error si ya está disfrutada o en curso
if ($fechaEntrada < $hoy) {
    die("<p>⚠️ La reserva con ID {$reserva['id']} ya ha comenzado o finalizado y no puede cancelarse.</p>");
}

// Calcular el reembolso
// Obtener política de reembolso desde la BD
$conf = $conexion->query("SELECT diasReembolsoCompleto, porcentajeReembolso 
                        FROM configuracion 
                        LIMIT 1");
$politica = $conf->fetch_assoc();

$diasReembolsoCompleto = (int)$politica['diasReembolsoCompleto'];   // ej: 7
$porcentajeReembolso   = (float)$politica['porcentajeReembolso'];   // ej: 0.4

if ($reserva['estado'] !== 'pagado') {
    $reembolso = 0; // // Solo las reservas pagadas generan reembolso
} else {
    $diffDias = $hoy->diff($fechaEntrada)->days; // Días de diferencia entre hoy y la fecha de entrada
    // Política de reembolso
    if ($diffDias >= $diasReembolsoCompleto) {
    // Reembolso completo
    $reembolso = $precioOriginal;
} else {
    // Reembolso parcial según porcentaje
    $reembolso = $precioOriginal * $porcentajeReembolso;
}

}

// Formulario de confirmación
echo '<form id="form-cancelar" class="form" method="post" action="cancel-reserva.php">';
echo "<h3>Confirmar cancelación</h3>";
echo "<p>La reserva con ID {$reserva['id']} será cancelada.</p>";
echo "<p>Importe a reembolsar: <strong>{$reembolso} €</strong></p>";

echo '<input type="hidden" name="id" value="'.$reserva['id'].'">';
echo '<input type="hidden" name="dni" value="'.$reserva['dni'].'">';
echo '<input type="hidden" name="reembolso" value="'.$reembolso.'">';
echo '<input type="hidden" name="motivo" value="'.$motivo.'">';

echo '<div class="button__wrapper">
        <button type="submit" class="button">Confirmar cancelación</button>
      </div>';
echo '</form>';

?>
