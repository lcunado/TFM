<?php
// Incluir configuración
require_once "config.php";

// Recoger datos del formulario
$entrada   = new DateTime($_POST['entrada']);
$salida    = new DateTime($_POST['salida']);
$huespedes = (int)$_POST['huespedes'];

// Validar fechas
if ($salida <= $entrada) {
    die("<p>La fecha de salida debe ser posterior a la de entrada.</p>");
}

// Calcular número de noches
$interval = $entrada->diff($salida);
$noches = $interval->days;

$precioTotal = 0;

// Iterar cada día de la reserva
for ($i = 0; $i < $noches; $i++) {
    $dia = clone $entrada;
    $dia->modify("+$i day");

    $esFinDeSemana = ($dia->format('N') == 6 || $dia->format('N') == 7); // sábado=6, domingo=7
    if ($esFinDeSemana) {
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