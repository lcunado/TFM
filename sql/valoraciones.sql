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
);
