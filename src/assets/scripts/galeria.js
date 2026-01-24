import { cargarConfig } from "./config.js";
import { cargarFooter } from "./footer.js";

document.addEventListener("DOMContentLoaded", cargarFooter);

document.addEventListener("DOMContentLoaded", async () => {

  const CONFIG = await cargarConfig();

  /* ------------------------------
    GALERÍA DINÁMICA
  ------------------------------ */
  const galeria = document.getElementById("galeria");

  // Limpiar por si acaso
  galeria.innerHTML = "";

  // Generar dinámicamente las figuras desde la BD
  CONFIG.galeria.forEach((foto, index) => {
    const figure = document.createElement("figure");
    figure.classList.add("galeria__item");

    const img = document.createElement("img");
    img.src = "/assets/images/galeria/" + foto.src;
    img.alt = foto.titulo;
    img.classList.add("galeria__image");

    const caption = document.createElement("figcaption");
    caption.classList.add("galeria__caption");
    caption.textContent = foto.titulo;

    figure.appendChild(img);
    figure.appendChild(caption);
    galeria.appendChild(figure);
  });

  /* ------------------------------
    VISOR 
  ------------------------------ */

  const visor = document.getElementById("visor");
  const visorImg = document.getElementById("visor-img");
  const prevBtn = document.getElementById("prev");
  const nextBtn = document.getElementById("next");

  // Recolectar imágenes después de generarlas
  const imagenes = Array.from(document.querySelectorAll(".galeria__image"));
  let indiceActual = 0;

  function mostrarImagen(index) {
    indiceActual = index;
    visorImg.src = imagenes[indiceActual].src;
    visor.classList.remove("hidden");
  }

  galeria.addEventListener("click", (e) => {
    if (e.target.tagName === "IMG") {
      const index = imagenes.indexOf(e.target);
      mostrarImagen(index);
    }
  });

  visor.addEventListener("click", (e) => {
    if (e.target === visor) {
      visor.classList.add("hidden");
    }
  });

  prevBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
    mostrarImagen(indiceActual);
  });

  nextBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    indiceActual = (indiceActual + 1) % imagenes.length;
    mostrarImagen(indiceActual);
  });

  document.addEventListener("keydown", (e) => {
    if (visor.classList.contains("hidden")) return;

    if (e.key === "ArrowLeft") {
      indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
      mostrarImagen(indiceActual);
    }

    if (e.key === "ArrowRight") {
      indiceActual = (indiceActual + 1) % imagenes.length;
      mostrarImagen(indiceActual);
    }

    if (e.key === "Escape") {
      visor.classList.add("hidden");
    }
  });

});

