import { CONFIG } from './config.js';

document.addEventListener("DOMContentLoaded", () => {
  const setTextAll = (selector, value) => {
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
      el.textContent = value;
    });
  };

  setTextAll(".localidad__nombre", CONFIG.localidad);
  setTextAll(".vivienda", CONFIG.vivienda);
  setTextAll(".direccion", CONFIG.direccion);
  setTextAll(".telefono", CONFIG.telefono);
  setTextAll(".whatsapp", CONFIG.whatsapp);
  setTextAll(".email", CONFIG.email);

  const mapa = document.querySelector(".mapa");

  if (mapa && CONFIG.latitud && CONFIG.longitud) {
    mapa.src = `https://www.google.com/maps?q=${CONFIG.latitud},${CONFIG.longitud}&hl=es&z=16&output=embed`;
  }

  const info = CONFIG.informacionGeneral;

  // Párrafos
  const textosContainer = document.querySelector(".info__textos");
  if (textosContainer) {
    info.textos.forEach(texto => {
      const p = document.createElement("p");
      p.className = "bloque__text";
      p.textContent = texto;
      textosContainer.appendChild(p);
    });
  }

  // Lugares
  const lista = document.querySelector(".info__lugares");
  if (lista) {
    info.lugares.forEach(lugar => {
      const li = document.createElement("li");
      li.className = "bloque__item";
      li.textContent = lugar;
      lista.appendChild(li);
    });
  }

  const iconos = CONFIG.iconosIncluidos;
  const iconGrid = document.querySelector(".icon__grid");

  if (iconGrid) {
    iconos.forEach(({ icono, texto }) => {
      const item = document.createElement("div");
      item.className = "icon__item";

      const i = document.createElement("i");
      i.className = `fa-solid ${icono}`;

      const span = document.createElement("span");
      span.innerHTML = texto; 

      item.appendChild(i);
      item.appendChild(span);
      iconGrid.appendChild(item);
    });
  }

  const politicas = CONFIG.politicasReserva;

  // Iconos
  const iconosPolitica = document.querySelector(".politicas__iconos");
  if (iconosPolitica) {
    politicas.iconos.forEach(({ icono, texto }) => {
      const item = document.createElement("div");
      item.className = "icon__item";

      const i = document.createElement("i");
      i.className = `fa-solid ${icono}`;

      const span = document.createElement("span");
      span.textContent = texto;

      item.appendChild(i);
      item.appendChild(span);
      iconosPolitica.appendChild(item);
    });
  }

  // Textos
  const textosPolitica = document.querySelector(".politicas__textos");
  if (textosPolitica) {
    politicas.textos.forEach(texto => {
      const p = document.createElement("p");
      p.className = "bloque__text";
      p.textContent = texto;
      textosPolitica.appendChild(p);
    });
  }

  // Dirección
  const direccionFooter = document.querySelector(".footer__direccion");
  if (direccionFooter) {
    direccionFooter.innerHTML = CONFIG.direccion;
  }

  // Contacto footer
  const contactoFooter = document.querySelector(".footer__contacto");
  if (contactoFooter) {
    contactoFooter.innerHTML = `${CONFIG.telefono}<br>${CONFIG.email}`;
  }

  // Políticas de cancelación
  const cancelacion = document.querySelector(".form__cancelacion");

  if (cancelacion && CONFIG.politicasReserva?.textos?.length > 0) {
    // Busca el texto que contenga "cancelación" o usa el último como fallback
    const textoCancelacion = CONFIG.politicasReserva.textos.find(t =>
      t.toLowerCase().includes("cancelación")
    ) || CONFIG.politicasReserva.textos.at(-1);

    cancelacion.textContent = textoCancelacion;
  }

  // Contacto directo
  const contactoGrid = document.querySelector(".contacto__grid");

  if (contactoGrid) {
    // Teléfono
    const telefonoItem = document.createElement("div");
    telefonoItem.className = "icon__item";
    telefonoItem.innerHTML = `
      <a href="tel:${CONFIG.telefono.replace(/\s+/g, '')}">
        <i class="fa-solid fa-phone"></i>
      </a>
      <a href="tel:${CONFIG.telefono.replace(/\s+/g, '')}">
        <span>${CONFIG.telefono}</span>
      </a>
    `;
    contactoGrid.appendChild(telefonoItem);

    // WhatsApp
    const whatsappItem = document.createElement("div");
    whatsappItem.className = "icon__item";
    const whatsappLink = CONFIG.whatsapp.replace(/\D+/g, ''); // solo números
    whatsappItem.innerHTML = `
      <a href="https://wa.me/${whatsappLink}">
        <i class="fa-brands fa-whatsapp"></i>
      </a>
      <a href="https://wa.me/${whatsappLink}">
        <span>${CONFIG.telefono}</span>
      </a>
    `;
    contactoGrid.appendChild(whatsappItem);

    // Email
    const emailItem = document.createElement("div");
    emailItem.className = "icon__item";
    emailItem.innerHTML = `
      <a href="mailto:${CONFIG.email}">
        <i class="fa-solid fa-at"></i>
      </a>
      <a href="mailto:${CONFIG.email}">
        <span>${CONFIG.email}</span>
      </a>
    `;
    contactoGrid.appendChild(emailItem);
  }

  // Límite de huéspedes
  const inputHuespedes = document.getElementById("huespedes");
  if (inputHuespedes) {
    inputHuespedes.setAttribute("max", CONFIG.maxHuespedes);
  }

  // Precio base
  const precioComment = document.querySelector(".bloque__comment");
  if (precioComment) {
    precioComment.textContent = `Desde ${CONFIG.precioBase}€ / noche`;
  }

});

