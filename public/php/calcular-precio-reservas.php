<?php
session_start();
// Incluir configuración
require_once __DIR__ . '/../private/config.php';

// Control Honeypot, si el campo oculto tiene contenido es spam
$hp = trim($_POST['hp_field_reservas'] ?? '');
if (!empty($hp)) {
    die("<p>⚠️ Detección de spam. Petición rechazada.</p>");
}

// Control tiempo, si tarda menos de 3 segundos es sospechoso
if (!isset($_SESSION['form_start'])) {
    $_SESSION['form_start'] = time();
}
$tiempoEnvio = time() - $_SESSION['form_start'];
if ($tiempoEnvio < 3) {
    die("<p>⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.</p>");
}
$_SESSION['form_start'] = time(); // Reinicio del tiempo

// Consulta variables en la BD
$stmt = $conexion->prepare("
    SELECT precioDiario, precioSabDom, precioLimpieza, maxHuespedes
    FROM configuracion
    WHERE id = 1
    LIMIT 1
");
$stmt->execute();
$stmt->bind_result($precioDiario, $precioSabDom, $precioLimpieza, $maxHuespedes);
$stmt->fetch();
$stmt->close();

// Crear objetos del formulario
$entrada   = new DateTime($_POST['entrada']);
$salida    = new DateTime($_POST['salida']);
$huespedes = (int)$_POST['huespedes'];

// Validaciones de campos
// Fechas obligatorias
if (empty($_POST['entrada']) || empty($salida)) {
    die("<p>⚠️ Debes seleccionar fechas válidas.</p>");
}

// Validar formato YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $entrada) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $salida)) {
    die("<p>⚠️ Formato de fecha no válido.</p>");
}

// Validar huéspedes como número entero
if (!ctype_digit($huespedes)) {
    die("<p>⚠️ Número de huéspedes no válido.</p>");
}

// Fecha actual sin hora
$hoy = new DateTime();
$hoy->setTime(0, 0, 0);

// Validar fechas
if ($entrada < $hoy) {
    die("<p>⚠️ No se pueden hacer reservas anteriores a hoy.</p>");
}
if ($salida <= $entrada) {
    die("<p>⚠️ La fecha de salida debe ser posterior a la de entrada.</p>");
}

// Validar número de huéspedes
if ($huespedes < 1 || $huespedes > $maxHuespedes) {
    die("<p>⚠️ El número de huéspedes debe estar entre 1 y $maxHuespedes.</p>");
}

// Validar solapamiento con reservas activas existentes
$stmt = $conexion->prepare("
    SELECT id 
    FROM reservas 
    WHERE estado IN ('pendiente','pagado')
      AND fecha_entrada < ? 
      AND fecha_salida > ?
");
$entradaStr = $entrada->format('Y-m-d');
$salidaStr  = $salida->format('Y-m-d');
$stmt->bind_param("ss", $salidaStr, $entradaStr);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("<p>⚠️ Ya existe una reserva en esas fechas.</p>");
}
$stmt->close();

// Obtener festivos desde API Nager.Date
$pais = "ES"; // España
$año  = $entrada->format("Y");
$url  = "https://date.nager.at/api/v3/PublicHolidays/$año/$pais";

$festivosJson = @file_get_contents($url); // Descargar los datos
$festivosSet = [];

if ($festivosJson !== false) {
    $festivos = json_decode($festivosJson, true); // Convertir a array
    if (is_array($festivos)) {
        $festivosSet = array_column($festivos, "date"); // Agrega el campo date "YYYY-MM-DD"
    }
}

// Calcular número de noches
$interval = $entrada->diff($salida);
$noches = $interval->days;

if ($noches < 1) {
    die("<p>⚠️ Debes seleccionar al menos 1 noche.</p>");
}

$precioTotal = 0;

// Iterar cada día de la reserva
for ($i = 0; $i < $noches; $i++) {
    $dia = clone $entrada; // Clona la fecha de entrada
    $dia->modify("+$i day"); // Avanza 1 día en la iteración
    $fechaDia = $dia->format('Y-m-d'); // Convierte la fecha a formato YYYY-MM-DD

    // Fin de semana: sábado (6) o domingo (7)
    $esFinDeSemana = ($dia->format('N') == 6 || $dia->format('N') == 7); 
    
    // Festivo 
    $esFestivo = in_array($fechaDia, $festivosSet);

    // Precio según tipo de día
    if ($esFinDeSemana || $esFestivo) {
        $precioTotal += $precioSabDom * $huespedes;
    } else {
        $precioTotal += $precioDiario * $huespedes;
    }
}

// Añadir coste de limpieza
$precioTotal += $precioLimpieza;

// Validación final
if ($precioTotal <= 0) {
    die("<p>⚠️ Error al calcular el precio. Inténtalo de nuevo.</p>");
}

// Segundo form de confirmación
echo '<form id="form-confirmar" class="form">';

// Mostrar resultado
echo "<h2>Resumen de la reserva</h2>";
echo "<p>Reserva de $noches noches para $huespedes huésped(es).</p>";
echo "<p>Precio total: <strong>" . number_format($precioTotal, 2, ',', '.') . " €</strong></p>";

// Formulario de datos personales
echo "<h2>Confirma la reserva</h2>";
echo '<div class="form__info">';
echo '<label>DNI</label><input type="text" name="dni" required>';
echo '<label>Nombre</label><input type="text" name="nombre" required>';
echo '<label>Apellidos</label><input type="text" name="apellidos" required>';
echo '<label>Email</label><input type="email" name="email" required>';
echo '<label>Teléfono</label><input type="tel" name="telefono" required>';
echo '</div>';

// Valores ocultos del cálculo
echo '<input type="hidden" name="entrada" value="' . $entrada->format('Y-m-d') . '">';
echo '<input type="hidden" name="salida" value="' . $salida->format('Y-m-d') . '">';
echo '<input type="hidden" name="precio" value="' . $precioTotal . '">';
echo '<input type="hidden" name="personas" value="' . $huespedes . '">';

echo "
    <div class='payment-info'>
        <p><strong>Método de pago:</strong> el pago se realizará de forma segura a través de <strong>Stripe</strong> mediante tarjeta de crédito o débito.</p>
        <div class='payment-info__icons'></div>
        <p class='payment-info__note'>Tus datos bancarios no se almacenan en nuestro servidor.</p>
    </div>";

echo '<div class="button__wrapper">
        <button type="submit" class="button">Pagar reserva</button>
      </div>';

echo '</form>';

?>