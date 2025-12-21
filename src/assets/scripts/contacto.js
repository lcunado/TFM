document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contact-form");
  const responseBox = document.getElementById("contact-response");

  if (!form) return;

  // Tiempo anti‑spam
  let inicio = Date.now();
  
  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // Evita recargar la página

    const formData = new FormData(form);

    // Validaciones
    // Nombre: solo letras y espacios, 2–40 caracteres
    const nombre = formData.get("nombre")?.trim();
    if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/.test(nombre)) {
      responseBox.innerHTML = "<p>⚠️ El nombre no es válido.</p>";
      return;
    }

    // Correo: validación estándar
    const correo = formData.get("correo")?.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      responseBox.innerHTML = "<p>⚠️ El correo electrónico no es válido.</p>";
      return;
    }

    // Mensaje: entre 5 y 500 caracteres
    const mensaje = formData.get("mensaje")?.trim();
    if (!mensaje || mensaje.length < 5 || mensaje.length > 500) {
      responseBox.innerHTML = "<p>⚠️ El mensaje debe tener entre 5 y 500 caracteres.</p>";
      return;
    }

    // Honeypot
    if (formData.get("hp_field_contacto")) {
      responseBox.innerHTML = "<p>⚠️ Detección de spam. Envío bloqueado.</p>";
      return;
    }

    // Tiempo mínimo
    const tiempo = Date.now() - inicio;
    if (tiempo < 5000) {
      responseBox.innerHTML = "<p>⚠️ Has enviado demasiado rápido. Inténtalo de nuevo.</p>";
      return;
    }

    // Envío al servidor
    try {
      const res = await fetch("contacto.php", {
        method: "POST",
        body: formData
      });

      const text = await res.text();
      responseBox.innerHTML = text; // Muestra la respuesta debajo del formulario

      // Limpieza 
      if (res.ok && text.includes("✅")) {
        form.reset();
      }
    } catch (err) {
      responseBox.innerHTML = "<p>⚠️ Error al enviar el formulario.</p>";
    }
  });
});
