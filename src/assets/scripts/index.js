import { cargarConfig } from "./config.js";

async function init() {
    const CONFIG = await cargarConfig();

    /* ------------------------------
       CABECERA PRINCIPAL
    ------------------------------ */

    document.querySelector(".localidad__nombre").textContent = CONFIG.localidad;
    document.querySelectorAll(".vivienda").forEach(el => el.textContent = CONFIG.vivienda);

    /* ------------------------------
       SECCIÓN INFORMACIÓN GENERAL
    ------------------------------ */

    const textosContainer = document.querySelector(".info__textos");
    textosContainer.innerHTML = "";
    CONFIG.informacionGeneral.textos.forEach(texto => {
        textosContainer.innerHTML += `<p>${texto}</p>`;
    });

    const lugaresContainer = document.querySelector(".info__lugares");
    lugaresContainer.innerHTML = "";
    CONFIG.informacionGeneral.lugares.forEach(lugar => {
        lugaresContainer.innerHTML += `<li>${lugar}</li>`;
    });

    /* ------------------------------
       SECCIÓN ICONOS INCLUIDOS
    ------------------------------ */

    const iconosContainer = document.querySelector(".icon__grid");
    iconosContainer.innerHTML = "";
    CONFIG.iconosIncluidos.forEach(item => {
        iconosContainer.innerHTML += `
            <div class="icon__item">
                <i class="fa ${item.icono}"></i>
                <span>${item.texto}</span>
            </div>
        `;
    });

    /* ------------------------------
       POLÍTICAS DE RESERVA
    ------------------------------ */

    const politicasIconos = document.querySelector(".politicas__iconos");
    politicasIconos.innerHTML = "";
    CONFIG.politicasReserva.iconos.forEach(item => {
        politicasIconos.innerHTML += `
            <div class="icon__item">
                <i class="fa ${item.icono}"></i>
                <span>${item.texto}</span>
            </div>
        `;
    });

    const politicasTextos = document.querySelector(".politicas__textos");
    politicasTextos.innerHTML = "";
    CONFIG.politicasReserva.textos.forEach(texto => {
        politicasTextos.innerHTML += `<p>${texto}</p>`;
    });

    /* ------------------------------
       MAPA
    ------------------------------ */

    const mapa = document.querySelector(".mapa");
    if (mapa) {
        mapa.src = `https://maps.google.com/maps?q=${CONFIG.latitud},${CONFIG.longitud}&z=15&output=embed`;
    }

    /* ------------------------------
       FOOTER
    ------------------------------ */

    document.querySelector(".footer__direccion").innerHTML = CONFIG.direccion; 
    document.querySelector(".footer__contacto").innerHTML = ` 
        Tel: ${CONFIG.telefono}<br> 
        WhatsApp: ${CONFIG.whatsapp}<br> 
        Email: ${CONFIG.email} 
    `;
}

init();

