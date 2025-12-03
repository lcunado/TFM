<?php
// Incluir configuración
require_once "config.php";

header('Content-Type: application/json');

$sql = "SELECT id, fecha_entrada, fecha_salida FROM reservas";
$result = $conexion->query($sql);

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        "title" => "Reservado",
        "start" => $row["fecha_entrada"],
        "end"   => date('Y-m-d', strtotime($row["fecha_salida"] . ' +1 day')),
        "color" => "#d9534f"
    ];
}

echo json_encode($eventos);
$conexion->close();
?>