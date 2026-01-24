import { cargarConfig } from "./config.js";

export async function cargarFooter() {
  const CONFIG = await cargarConfig();

  const direccion = document.querySelector(".footer__direccion");
  const contacto = document.querySelector(".footer__contacto");

  if (direccion) {
    direccion.innerHTML = `
      ${CONFIG.direccionCalle}<br>
      ${CONFIG.direccionCP} ${CONFIG.direccionCiudad}<br>
      ${CONFIG.direccionPais}
    `;
  }

  if (contacto) {
    contacto.innerHTML = `
      Tel: ${CONFIG.telefono}<br>
      WhatsApp: ${CONFIG.whatsapp}<br>
      Email: ${CONFIG.email}
    `;
  }
}

