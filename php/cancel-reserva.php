<?php
require_once "config.php";

$id_reserva = $_POST['id'] ?? 0;
$dni        = $_POST['dni'] ?? '';
$reembolso  = $_POST['reembolso'] ?? 0;

if ($id_reserva > 0 && !empty($dni)) {
    $stmtDelete = $conexion->prepare("DELETE FROM reservas WHERE id = ? AND dni = ?");
    $stmtDelete->bind_param("is", $id_reserva, $dni);

    if ($stmtDelete->execute()) {
        echo "<p>✅ Reserva cancelada correctamente.</p>";
        echo "<p>Se reembolsarán <strong>{$reembolso} €</strong>.</p>";

    } else {
        echo "<p>Error al cancelar la reserva: " . $stmtDelete->error . "</p>";
    }

    $stmtDelete->close();
} else {
    echo "<p>Datos de cancelación incompletos.</p>";
}

$conexion->close();
?>