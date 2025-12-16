document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contact-form");
  const responseBox = document.getElementById("contact-response");

  if (!form) return;
  
  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // Evita recargar la página

    const formData = new FormData(form);

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
