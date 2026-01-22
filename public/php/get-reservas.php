<?php
// Incluir configuración
require_once __DIR__ . '/../private/config.php';

// Respuesta en JSON
header('Content-Type: application/json');

// Consultar reservas activas
$sql = "SELECT id, fecha_entrada, fecha_salida, estado 
        FROM reservas 
        WHERE estado IN ('pagado')"; // No las canceladas
$result = $conexion->query($sql);

// Respuesta
$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        "title" => "Reservado",
        "start" => $row["fecha_entrada"],
        "end"   => date('Y-m-d', strtotime($row["fecha_salida"])), 
        "color" => "#d9534f"
    ];
}

// Enviar eventos
echo json_encode($eventos);

// Cierre coenxión
$conexion->close();
?>