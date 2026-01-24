<?php
require_once __DIR__ . '/../private/config.php';

// Crear tabla admin
$conexion->query("
    CREATE TABLE admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
        intentos INT DEFAULT 0,
        bloqueado_hasta DATETIME DEFAULT NULL,
        reset_token VARCHAR(255) DEFAULT NULL,
        reset_caduca DATETIME DEFAULT NULL
    )
");

// Crear tabla configuracion
$conexion->query("
    CREATE TABLE configuracion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dominio VARCHAR(255) NOT NULL,
        titulo VARCHAR(255) DEFAULT NULL, 
        vivienda VARCHAR(255) DEFAULT NULL, 
        imagenFondo VARCHAR(255) NOT NULL, 
        direccionCalle VARCHAR(255) NOT NULL, 
        direccionCP VARCHAR(20) NOT NULL, 
        direccionCiudad VARCHAR(100) NOT NULL, 
        direccionPais VARCHAR(100) NOT NULL, 
        latitud DECIMAL(10,6) DEFAULT NULL, 
        longitud DECIMAL(10,6) DEFAULT NULL, 
        telefono VARCHAR(50) DEFAULT NULL, 
        whatsapp VARCHAR(50) DEFAULT NULL, 
        email VARCHAR(255) DEFAULT NULL, 
        maxHuespedes INT(11) DEFAULT NULL, 
        informacionGeneral TEXT DEFAULT NULL, 
        lugaresInteres TEXT NOT NULL, 
        metrosCuadrados INT(11) NOT NULL DEFAULT 62, 
        numHabitaciones INT(11) NOT NULL DEFAULT 2, 
        numBanos INT(11) NOT NULL DEFAULT 2, 
        edadBebesGratis INT(11) NOT NULL DEFAULT 4, 
        iconoGaraje TINYINT(1) NOT NULL DEFAULT 1, 
        iconoMascotas TINYINT(1) NOT NULL DEFAULT 0, 
        iconoChimenea TINYINT(1) NOT NULL DEFAULT 0, 
        iconoJardin TINYINT(1) NOT NULL DEFAULT 0, 
        iconoBarbacoa TINYINT(1) NOT NULL DEFAULT 0, 
        iconoWifi TINYINT(1) NOT NULL DEFAULT 1, 
        iconoEquipado TINYINT(1) NOT NULL DEFAULT 1, 
        iconoCalefaccion TINYINT(1) NOT NULL DEFAULT 1, 
        horarioEntrada VARCHAR(10) NOT NULL, 
        horarioSalida VARCHAR(10) NOT NULL, 
        politicasReserva TEXT DEFAULT NULL, 
        precioDiario DECIMAL(10,2) DEFAULT NULL, 
        precioSabDom DECIMAL(10,2) DEFAULT NULL, 
        precioLimpieza DECIMAL(10,2) DEFAULT NULL, 
        diasReembolsoCompleto INT(11) NOT NULL DEFAULT 7, 
        porcentajeReembolso FLOAT(5,2) NOT NULL DEFAULT 0.40, 
        galeria LONGTEXT DEFAULT NULL 
    ) 
");

// Crear tabla reservas
$conexion->query("
    CREATE TABLE reservas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        payment_intent VARCHAR(255) DEFAULT NULL,
        dni VARCHAR(20) NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        apellidos VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        telefono VARCHAR(20) DEFAULT NULL,
        num_personas INT NOT NULL,
        fecha_entrada DATE NOT NULL,
        fecha_salida DATE NOT NULL,
        precio DECIMAL(10,2) NOT NULL,
        estado ENUM('pagado','cancelado') DEFAULT NULL
    ) 
");

// Crear tabla cancelaciones
$conexion->query("
    CREATE TABLE cancelaciones (
        id_cancelacion INT AUTO_INCREMENT PRIMARY KEY,
        id_reserva INT NOT NULL,
        fecha_cancelacion DATETIME NOT NULL,
        importe_pagado DECIMAL(10,2) NOT NULL,
        importe_reembolsar DECIMAL(10,2) NOT NULL,
        motivo VARCHAR(255) NOT NULL,
        estado_cancelacion ENUM('pendiente','reembolsado','no_reembolsable') NOT NULL DEFAULT 'pendiente',
        fecha_reembolso DATETIME NULL,
        FOREIGN KEY (id_reserva) REFERENCES reservas(id)
    )
");

// Crear tabla valoraciones
$conexion->query("
    CREATE TABLE valoraciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        general TINYINT NOT NULL,
        limpieza TINYINT NULL,
        veracidad TINYINT NULL,
        llegada TINYINT NULL,
        comunicacion TINYINT NULL,
        ubicacion TINYINT NULL,
        calidad TINYINT NULL,
        comentario TEXT,
        fecha_valoracion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) 
");

header("Location: crear-admin.php");
exit;
