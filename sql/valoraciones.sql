CREATE TABLE valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    general TINYINT NOT NULL CHECK (general BETWEEN 1 AND 5),
    limpieza TINYINT NULL CHECK (limpieza BETWEEN 1 AND 5),
    veracidad TINYINT NULL CHECK (veracidad BETWEEN 1 AND 5),
    llegada TINYINT NULL CHECK (llegada BETWEEN 1 AND 5),
    comunicacion TINYINT NULL CHECK (comunicacion BETWEEN 1 AND 5),
    ubicacion TINYINT NULL CHECK (ubicacion BETWEEN 1 AND 5),
    calidad TINYINT NULL CHECK (calidad BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_valoracion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
