<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../private/config.php';

/* ============================================================
   RECOGER DATOS DEL FORMULARIO
============================================================ */

// Strings
$dominio = $_POST['dominio'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$vivienda = $_POST['vivienda'] ?? '';
$imagenFondo = $_POST['imagenFondo'] ?? '';
$direccionCalle = $_POST['direccionCalle'] ?? '';
$direccionCP = $_POST['direccionCP'] ?? '';
$direccionCiudad = $_POST['direccionCiudad'] ?? '';
$direccionPais = $_POST['direccionPais'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$whatsapp = $_POST['whatsapp'] ?? '';
$email = $_POST['email'] ?? '';
$latitud = $_POST['latitud'] ?? '';
$longitud = $_POST['longitud'] ?? '';
$precioDiario = $_POST['precioDiario'] ?? '';

// Arrays JSON
$informacionGeneral = $_POST['informacionGeneral'] ?? '[]'; 
$lugaresInteres = $_POST['lugaresInteres'] ?? '[]'; 
$politicasReserva = $_POST['politicasReserva'] ?? '[]';
$galeria = $_POST['galeria'] ?? '[]';

// Números
$metrosCuadrados = intval($_POST['metrosCuadrados']);
$maxHuespedes = intval($_POST['maxHuespedes']);
$numHabitaciones = intval($_POST['numHabitaciones']);
$numBanos = intval($_POST['numBanos']);
$edadBebesGratis = intval($_POST['edadBebesGratis']);

// Checkboxes
$iconoGaraje = isset($_POST['iconoGaraje']) ? 1 : 0;
$iconoMascotas = isset($_POST['iconoMascotas']) ? 1 : 0;
$iconoChimenea = isset($_POST['iconoChimenea']) ? 1 : 0;
$iconoBarbacoa = isset($_POST['iconoBarbacoa']) ? 1 : 0;
$iconoJardin = isset($_POST['iconoJardin']) ? 1 : 0;
$iconoWifi = isset($_POST['iconoWifi']) ? 1 : 0;
$iconoEquipado = isset($_POST['iconoEquipado']) ? 1 : 0;
$iconoCalefaccion = isset($_POST['iconoCalefaccion']) ? 1 : 0;

/* ============================================================
   PREPARED STATEMENT
============================================================ */

$sql = "
UPDATE configuracion SET
    dominio = ?,
    titulo = ?,
    vivienda = ?,
    imagenFondo = ?,
    direccionCalle = ?,
    direccionCP = ?,
    direccionCiudad = ?,
    direccionPais = ?,
    telefono = ?,
    whatsapp = ?,
    email = ?,
    latitud = ?,
    longitud = ?,
    precioDiario = ?,

    informacionGeneral = ?,
    lugaresInteres = ?,
    politicasReserva = ?,
    galeria = ?,

    metrosCuadrados = ?,
    maxHuespedes = ?,
    numHabitaciones = ?,
    numBanos = ?,
    edadBebesGratis = ?,

    iconoGaraje = ?,
    iconoMascotas = ?,
    iconoChimenea = ?,
    iconoBarbacoa = ?,
    iconoJardin = ?,
    iconoWifi = ?,
    iconoEquipado = ?,
    iconoCalefaccion = ?

WHERE id = 1
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "sssssssssssdddssssiiiiiiiiiiiii",
    $dominio,
    $titulo,
    $vivienda,
    $imagenFondo,
    $direccionCalle,
    $direccionCP,
    $direccionCiudad,
    $direccionPais,
    $telefono,
    $whatsapp,
    $email,

    $latitud,
    $longitud,
    $precioDiario,

    $informacionGeneral,
    $lugaresInteres,
    $politicasReserva,
    $galeria,

    $metrosCuadrados,
    $maxHuespedes,
    $numHabitaciones,
    $numBanos,
    $edadBebesGratis,

    $iconoGaraje,
    $iconoMascotas,
    $iconoChimenea,
    $iconoBarbacoa,
    $iconoJardin,
    $iconoWifi,
    $iconoEquipado,
    $iconoCalefaccion
);


$stmt->execute();
$stmt->close();

/* ============================================================
   REDIRIGIR
============================================================ */

header("Location: configuracion.php?ok=1");
exit;



