document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-cancel");
  const resultado = document.getElementById("resultado-precio-cancel");

  // Paso 1: calcular reembolso
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const datos = new FormData(form);

    try {
      const response = await fetch("./calcular-precio-reembolso.php", {
        method: "POST",
        body: datos
      });
      resultado.innerHTML = await response.text();

      // Paso 2: confirmar cancelación (segundo formulario dinámico)
      console.log("HTML insertado:", resultado.innerHTML);
      const segundoForm = document.getElementById("form-cancelar");
      console.log("Segundo form encontrado:", segundoForm);

      if (segundoForm) {
        segundoForm.addEventListener("submit", async (ev) => {
          ev.preventDefault();
          const datos2 = new FormData(segundoForm);

          try {
            const resp2 = await fetch("./cancel-reserva.php", {
              method: "POST",
              body: datos2
            });
            resultado.innerHTML = await resp2.text();

            // Actualiza calendario si existe
            if (window.calendar) {
              window.calendar.refetchEvents();
            }
          } catch (error) {
            resultado.innerHTML = "<p>Error al confirmar la cancelación.</p>";
          }
        });
      }
    } catch (error) {
      resultado.innerHTML = "<p>Error al calcular el reembolso.</p>";
    }
  });
});
