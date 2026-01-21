import { cargarConfig } from "./config.js";

document.addEventListener("DOMContentLoaded", async () => {
  
  const CONFIG = await cargarConfig();

  /* ============================================================ 
    FOOTER 
  ============================================================ */
  document.querySelector(".footer__direccion").innerHTML = `
    ${CONFIG.direccionCalle}<br>
    ${CONFIG.direccionCP} ${CONFIG.direccionCiudad}<br>
    ${CONFIG.direccionPais}
  `;
 
  document.querySelector(".footer__contacto").innerHTML = ` 
    Tel: ${CONFIG.telefono}<br> 
    WhatsApp: ${CONFIG.whatsapp}<br> 
    Email: ${CONFIG.email} 
  `;
  
  /* ============================================================ 
    RESERVAS 
  ============================================================ */
  // Obtener contenedores
  const form = document.getElementById("form-reserva");
  const resultado = document.getElementById("resultado-precio");

  // Guardar tiempo de inicio
  let inicio = Date.now();

  // Calcular precio
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const datos = new FormData(form);

    // Validación de campos
    // Fecha de entrada
    const entrada = datos.get("entrada");
    if (!entrada) {
      resultado.innerHTML = "<p>⚠️ Debes seleccionar una fecha de entrada.</p>";
      return;
    }

    // Fecha de salida
    const salida = datos.get("salida");
    if (!salida) {
      resultado.innerHTML = "<p>⚠️ Debes seleccionar una fecha de salida.</p>";
      return;
    }

    // Validar que salida > entrada
    if (entrada >= salida) {
      resultado.innerHTML = "<p>⚠️ La fecha de salida debe ser posterior a la de entrada.</p>";
      return;
    }

    // Número de huéspedes
    const huespedes = datos.get("huespedes");
    if (!/^[0-9]+$/.test(huespedes) || huespedes < 1 ) {
      resultado.innerHTML = "<p>⚠️ Debes seleccionar un número de huéspedes correcto.</p>";
      return;
    }
    
    // Control Honeypot, si el campo oculto tiene contenido es spam
    if (datos.get("hp_field_reservas")) {
      resultado.innerHTML = "<p>⚠️ Detección de spam. Reserva bloqueada.</p>";
      return;
    }

    // Control tiempo, si tarda menos de 5 segundos es sospechoso
    let tiempo = Date.now() - inicio;
    if (tiempo < 5000) {
      resultado.innerHTML = "<p>⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.</p>";
      return;
    }

    // Envío al servidor
    try {
      const response = await fetch("/php/calcular-precio-reservas.php", {
        method: "POST",
        body: datos
      });

      // Mostrar respuesta
      resultado.innerHTML = await response.text();
      
      // Confirmar reserva (segundo formulario dinámico)
      const segundoForm = document.getElementById("form-confirmar");
      if (segundoForm) {
        segundoForm.addEventListener("submit", async (ev) => {
          ev.preventDefault();
          const datos2 = new FormData(segundoForm);

          // Envío al servidor
          try {
            const resp2 = await fetch("/php/insert-reserva.php", {
              method: "POST",
              body: datos2
            });

            const json = await resp2.json(); 
            
            if (json.ok) { 
              window.location.href = "php/pago-sesion.php"; 
              return; 
            } 
            
            resultado.innerHTML = "<p>⚠️ No se pudo iniciar el pago.</p>";
          
          } catch (error) {
            resultado.innerHTML = "<p>⚠️ Error al confirmar la reserva.</p>";
          }
        });
      }
    } catch (error) {
      resultado.innerHTML = "<p>⚠️ Error al calcular la reserva.</p>";
    }
  });
});

