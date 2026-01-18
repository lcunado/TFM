/* ============================================================
   EDITOR VISUAL: INFORMACIÓN GENERAL
   ============================================================ */

function renderInfoGeneral() {
    const container = document.getElementById("infoGeneralContainer");
    container.innerHTML = "";

    infoGeneral.forEach((item, index) => {
        const div = document.createElement("div");
        div.classList.add("editor-item");

        div.innerHTML = `
            <input 
                type="text" 
                class="form__input"
                value="${item}"
                placeholder="Elemento"
                oninput="infoGeneral[${index}] = this.value"
            >
            <button 
                type="button" 
                class="button button--danger"
                onclick="removeInfoGeneral(${index})"
            >
                Eliminar
            </button>
        `;

        container.appendChild(div);
    });
}

function addInfoGeneral() {
    infoGeneral.push("");
    renderInfoGeneral();
}

function removeInfoGeneral(index) {
    infoGeneral.splice(index, 1);
    renderInfoGeneral();
}

/* ============================================================
   EDITOR VISUAL: ICONOS INCLUIDOS
   ============================================================ */

function renderIconos() {
    const container = document.getElementById("iconosContainer");
    container.innerHTML = "";

    iconosIncluidos.forEach((item, index) => {
        const div = document.createElement("div");
        div.classList.add("editor-item");

        div.innerHTML = `
            <input 
                type="text" 
                class="form__input"
                value="${item.icono}"
                placeholder="Nombre del icono"
                oninput="iconosIncluidos[${index}].icono = this.value"
            >
            <input 
                type="text" 
                class="form__input"
                value="${item.texto}"
                placeholder="Texto del icono"
                oninput="iconosIncluidos[${index}].texto = this.value"
            >
            <button 
                type="button" 
                class="button button--danger"
                onclick="removeIcono(${index})"
            >
                Eliminar
            </button>
        `;

        container.appendChild(div);
    });
}

function addIcono() {
    iconosIncluidos.push({ icono: "", texto: "" });
    renderIconos();
}

function removeIcono(index) {
    iconosIncluidos.splice(index, 1);
    renderIconos();
}

/* ============================================================
   EDITOR VISUAL: POLÍTICAS DE RESERVA
   ============================================================ */

function renderPoliticas() {
    const container = document.getElementById("politicasContainer");
    container.innerHTML = "";

    politicasReserva.forEach((item, index) => {
        const div = document.createElement("div");
        div.classList.add("editor-item");

        div.innerHTML = `
            <input 
                type="text" 
                class="form__input"
                value="${item}"
                placeholder="Política de reserva"
                oninput="politicasReserva[${index}] = this.value"
            >
            <button 
                type="button" 
                class="button button--danger"
                onclick="removePolitica(${index})"
            >
                Eliminar
            </button>
        `;

        container.appendChild(div);
    });
}

function addPolitica() {
    politicasReserva.push("");
    renderPoliticas();
}

function removePolitica(index) {
    politicasReserva.splice(index, 1);
    renderPoliticas();
}

/* ============================================================
   GUARDAR ANTES DE ENVIAR EL FORMULARIO
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {
    renderInfoGeneral();
    renderIconos();
    renderPoliticas();

    document.querySelector("form").addEventListener("submit", () => {
        document.getElementById("informacionGeneralInput").value = JSON.stringify(infoGeneral);
        document.getElementById("iconosIncluidosInput").value = JSON.stringify(iconosIncluidos);
        document.getElementById("politicasReservaInput").value = JSON.stringify(politicasReserva);
    });
});

