CREATE TABLE configuracion (
    id INT PRIMARY KEY,

    -- Datos básicos del alojamiento
    localidad VARCHAR(255),
    vivienda VARCHAR(255),
    direccion TEXT,
    latitud DECIMAL(10,6),
    longitud DECIMAL(10,6),
    telefono VARCHAR(50),
    whatsapp VARCHAR(50),
    email VARCHAR(255),
    precioBase DECIMAL(10,2),
    maxHuespedes INT,
    
    -- Datos complejos en formato JSON
    informacionGeneral TEXT,   -- JSON
    iconosIncluidos TEXT,      -- JSON
    politicasReserva TEXT,     -- JSON

    -- Tarifas del backend
    precioDiario DECIMAL(10,2),
    precioSabDom DECIMAL(10,2),
    precioLimpieza DECIMAL(10,2),

    -- Datos del propietario
    propietarioEmail VARCHAR(255),
    propietarioPassword VARCHAR(255),
    numeroCuenta VARCHAR(50)
);

