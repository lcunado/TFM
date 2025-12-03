<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Incluir configuración
require_once "config.php";

// Recoger datos del formulario
$entrada   = new DateTime($_POST['entrada']);
$salida    = new DateTime($_POST['salida']);
$huespedes = (int)$_POST['huespedes'];

// Fecha actual (solo día sin hora)
$hoy = new DateTime();
$hoy->setTime(0, 0, 0);

// Validar fechas
if ($entrada < $hoy) {
    die("<p>⚠️ No se pueden hacer reservas anteriores a hoy.</p>");
}
if ($salida <= $entrada) {
    die("<p>⚠️ La fecha de salida debe ser posterior a la de entrada.</p>");
}

// Validar solapamiento con reservas existentes
$stmt = $conexion->prepare("
    SELECT id 
    FROM reservas 
    WHERE fecha_entrada < ? AND fecha_salida > ?
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

$festivosJson = @file_get_contents($url);
$festivosSet = [];

if ($festivosJson !== false) {
    $festivos = json_decode($festivosJson, true);
    if (is_array($festivos)) {
        $festivosSet = array_column($festivos, "date"); // array de YYYY-MM-DD
    }
}

// Calcular número de noches
$interval = $entrada->diff($salida);
$noches = $interval->days;

$precioTotal = 0;

// Iterar cada día de la reserva
for ($i = 0; $i < $noches; $i++) {
    $dia = clone $entrada;
    $dia->modify("+$i day");
    $fechaDia = $dia->format('Y-m-d');

    $esFinDeSemana = ($dia->format('N') == 6 || $dia->format('N') == 7); // sábado=6, domingo=7
    $esFestivo     = in_array($fechaDia, $festivosSet);

    if ($esFinDeSemana || $esFestivo) {
        $precioTotal += $precioSabDom * $huespedes;
    } else {
        $precioTotal += $precioDiario * $huespedes;
    }
}

// Añadir limpieza
$precioTotal += $precioLimpieza;

// Segundo form
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

echo '<div class="button__wrapper">
        <button type="submit" class="button">Confirmar reserva</button>
      </div>';

echo '</form>';

?>