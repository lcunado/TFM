<?php
require_once "config.php";

$id_reserva = $_POST['id'] ?? 0;
$dni        = $_POST['dni'] ?? '';

if ($id_reserva > 0 && !empty($dni)) {
    $stmt = $conexion->prepare("SELECT id, dni, fecha_entrada, precio FROM reservas WHERE id = ? AND dni = ?");
    $stmt->bind_param("is", $id_reserva, $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $reserva = $resultado->fetch_assoc();
        $precioOriginal = (float)$reserva['precio'];
        $fechaEntrada   = new DateTime($reserva['fecha_entrada']);
        $hoy            = new DateTime();

        $diffDias = $hoy->diff($fechaEntrada)->days;

        // Política de cancelación: 100% si faltan ≥7 días, 40% si menos
        if ($diffDias >= 7) {
            $reembolso = $precioOriginal;
        } else {
            $reembolso = $precioOriginal * 0.4;
        }

        // Mostrar confirmación con botón
        echo '<form id="form-cancelar" class="form" method="post" action="cancel-reserva.php">';
        echo "<h3>Confirmar cancelación</h3>";
        echo "<p>La reserva con ID {$reserva['id']} será cancelada.</p>";
        echo "<p>Importe a reembolsar: <strong>{$reembolso} €</strong></p>";

        echo '<input type="hidden" name="id" value="'.$reserva['id'].'">';
        echo '<input type="hidden" name="dni" value="'.$reserva['dni'].'">';
        echo '<input type="hidden" name="reembolso" value="'.$reembolso.'">';

        echo '<div class="button__wrapper">
                <button type="submit" class="button">Confirmar cancelación</button>
            </div>';
        echo '</form>';
    } else {
        echo "<p>⚠️ No se encontró ninguna reserva con ese ID y DNI.</p>";
    }

    $stmt->close();
} else {
    echo "<p>⚠️ Datos de cancelación incompletos.</p>";
}

?>
