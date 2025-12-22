// Botón del menú hamburguesa
const toggle = document.querySelector(".header__toggle");

// Contenedor del menú de navegación
const nav = document.querySelector(".header__nav");

// Alternar la visibilidad del menú al hacer clic
toggle.addEventListener("click", () => {
  nav.classList.toggle("active");
});
