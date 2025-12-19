CREATE TABLE cancelaciones (
  id_cancelacion INT AUTO_INCREMENT PRIMARY KEY,
  id_reserva INT NOT NULL,
  fecha_cancelacion DATETIME NOT NULL,
  importe_pagado DECIMAL(10,2) NOT NULL,
  importe_reembolsar DECIMAL(10,2) NOT NULL,
  motivo VARCHAR(255),
  estado_cancelacion ENUM('pendiente','reembolsado','no_reembolsable') NOT NULL DEFAULT 'pendiente',
  FOREIGN KEY (id_reserva) REFERENCES reservas(id)
);

