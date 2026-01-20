document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("nueva");
    const salida = document.getElementById("seguridad-pass");

    if (!input || !salida) return;

    input.addEventListener("input", () => {
        const pass = input.value;
        let errores = [];

        if (pass.length < 8) errores.push("• Mínimo 8 caracteres");
        if (!/[A-Z]/.test(pass)) errores.push("• Una mayúscula");
        if (!/[a-z]/.test(pass)) errores.push("• Una minúscula");
        if (!/[0-9]/.test(pass)) errores.push("• Un número");
        if (!/[\W_]/.test(pass)) errores.push("• Un símbolo");

        if (pass.length === 0) {
            salida.innerHTML = "";
            return;
        }

        if (errores.length === 0) {
            salida.style.color = "var(--color-component)";
            salida.innerHTML = "Contraseña segura";
        } else {
            salida.style.color = "var(--color-danger)";
            salida.innerHTML = "Faltan requisitos:<br>" + errores.join("<br>");
        }
    });
});
