document.addEventListener("DOMContentLoaded", () => {
  // Obtener los contenedores
  const form = document.getElementById("form-cancel");
  const resultado = document.getElementById("resultado-precio-cancel");

  // Guardar tiempo de inicio
  let inicio = Date.now();

  // Calcular reembolso
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const datos = new FormData(form);

    // Validación de campos
    // Validar ID (solo números)
    const id = datos.get("id");
    if (!/^[0-9]+$/.test(id)) {
      resultado.innerHTML = "<p>⚠️ El ID debe ser un número válido.</p>";
      return;
    }

    // Validar DNI o Pasaporte (letras y números, 5-20 chars)
    const dni = datos.get("dni");
    if (!/^[A-Za-z0-9]{5,20}$/.test(dni)) {
      resultado.innerHTML = "<p>⚠️ DNI o pasaporte no válido.</p>";
      return;
    }

    // Validar motivo (mínimo 5 caracteres)
    const motivo = datos.get("motivo").trim();
    if (motivo.length < 5 || motivo.length > 500) {
      resultado.innerHTML = "<p>⚠️ El motivo debe tener entre 5 y 500 caracteres.</p>";
      return;
    }

    // Control Honeypot, si el campo oculto tiene contenido es spam
    if (datos.get("hp_field_cancelaciones")) {
      resultado.innerHTML = "<p>⚠️ Detección de spam. Cancelación bloqueada.</p>";
      return;
    }

    // Control tiempo, si tarda menos de 5 segundos es sospechoso
    let tiempo = Date.now() - inicio;
    if (tiempo < 5000) {
      resultado.innerHTML = "<p>⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.</p>";
      return;
    }

    // Petición al servidor
    try {
      const response = await fetch("/php/calcular-precio-reembolso.php", {
        method: "POST",
        body: datos
      });
      //Mostrar respuesta
      resultado.innerHTML = await response.text();

      // Confirmar cancelación (segundo formulario dinámico)
      const segundoForm = document.getElementById("form-cancelar");

      if (segundoForm) {
        // Evento para confirmar la cancelación
        segundoForm.addEventListener("submit", async (ev) => {
          ev.preventDefault();
          const datos2 = new FormData(segundoForm);
          
          //Petición al servidor
          try {
            const resp2 = await fetch("/php/cancel-reserva.php", {
              method: "POST",
              body: datos2
            });
            
            // Mostrar respuesta
            resultado.innerHTML = await resp2.text();
            
            // Limpiar formularios
            form.reset();          
            segundoForm.reset();   

            // Reiniciar tiempo anti-spam 
            inicio = Date.now();

            // Actualizar calendario 
            if (window.calendar) {
              window.calendar.refetchEvents();
            }
          } catch (error) {
            resultado.innerHTML = "<p>⚠️ Error al confirmar la cancelación.</p>";
          }
        });
      }
    } catch (error) {
      resultado.innerHTML = "<p>⚠️ Error al calcular el reembolso.</p>";
    }
  });
});
