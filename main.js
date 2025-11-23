// main.js
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contactForm");
    if (!form) return; // En index.html no hay formulario

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        // Campos del formulario
        const nombreInput = document.getElementById("nombre");
        const correoInput = document.getElementById("correo");
        const mensajeInput = document.getElementById("mensaje");
        const successMessage = document.getElementById("successMessage");

        // Mensajes de error
        const errorNombre = document.getElementById("error-nombre");
        const errorCorreo = document.getElementById("error-correo");
        const errorMensaje = document.getElementById("error-mensaje");

        // Limpiar errores y mensaje de éxito previos
        errorNombre.textContent = "";
        errorCorreo.textContent = "";
        errorMensaje.textContent = "";
        successMessage.textContent = "";

        let esValido = true;

        const nombre = nombreInput.value.trim();
        const correo = correoInput.value.trim();
        const mensaje = mensajeInput.value.trim();

        // Validar nombre (obligatorio, sin números)
        const nombreRegex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
        if (nombre === "") {
            errorNombre.textContent = "El nombre es obligatorio.";
            esValido = false;
        } else if (!nombreRegex.test(nombre)) {
            errorNombre.textContent = "El nombre solo puede contener letras y espacios.";
            esValido = false;
        }

        // Validar correo (obligatorio, formato email)
        if (correo === "") {
            errorCorreo.textContent = "El correo es obligatorio.";
            esValido = false;
        } else {
            const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!correoRegex.test(correo)) {
                errorCorreo.textContent = "Ingresa un correo electrónico válido.";
                esValido = false;
            }
        }

        // Validar mensaje (obligatorio, máximo 100 caracteres)
        if (mensaje === "") {
            errorMensaje.textContent = "El mensaje es obligatorio.";
            esValido = false;
        } else if (mensaje.length > 100) {
            errorMensaje.textContent = "El mensaje no puede tener más de 100 caracteres.";
            esValido = false;
        }

        if (!esValido) {
            return;
        }

        // Guardar en el navegador (localStorage)
        // Estructura: [{ nombre, correo, mensaje, fecha }, ...]
        const nuevoMensaje = {
            nombre: nombre,
            correo: correo,
            mensaje: mensaje,
            fecha: new Date().toISOString()
        };

        const KEY = "contactFormMessages";
        const mensajesGuardados = JSON.parse(localStorage.getItem(KEY) || "[]");
        mensajesGuardados.push(nuevoMensaje);
        localStorage.setItem(KEY, JSON.stringify(mensajesGuardados));

        // Mostrar mensaje de éxito en la página
        successMessage.textContent = "Mensaje enviado con éxito.";

        // (Opcional) limpiar el formulario
        form.reset();
    });
});
