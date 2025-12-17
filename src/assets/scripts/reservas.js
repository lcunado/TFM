document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-reserva");
  const resultado = document.getElementById("resultado-precio");

  // Paso 1: calcular precio
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const datos = new FormData(form);

    try {
      const response = await fetch("./calcular-precio-reservas.php", {
        method: "POST",
        body: datos
      });
      resultado.innerHTML = await response.text();
      
      // Paso 2: confirmar reserva (segundo formulario dinámico)
      const segundoForm = document.getElementById("form-confirmar");
      if (segundoForm) {
        segundoForm.addEventListener("submit", async (ev) => {
          ev.preventDefault();
          const datos2 = new FormData(segundoForm);
          
          try {
            const resp2 = await fetch("./insert-reserva.php", {
              method: "POST",
              body: datos2
            });
            resultado.innerHTML = await resp2.text();
            // Actualiza calendario
            if (window.calendar) {
              window.calendar.refetchEvents();
            }
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

