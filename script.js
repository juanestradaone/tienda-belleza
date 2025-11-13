document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const registroForm = document.getElementById("registro");
    const recuperarForm = document.getElementById("recuperar");

    mostrarFormulario("login");

    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", (e) => {
            const destino = link.getAttribute("href").replace("#", "");
            mostrarFormulario(destino);
            e.preventDefault();
        });
    });

    function mostrarFormulario(id) {
        loginForm.classList.remove("active");
        registroForm.classList.remove("active");
        recuperarForm.classList.remove("active");

        if (id === "login-form" || id === "login") {
            loginForm.classList.add("active");
        } else if (id === "registro") {
            registroForm.classList.add("active");
        } else if (id === "recuperar") {
            recuperarForm.classList.add("active");
        }
    }
    [loginForm, registroForm, recuperarForm].forEach(form => {
        form.addEventListener("submit", () => {
            // No usamos e.preventDefault() aquí
            console.log("Formulario enviado");
        });
    });

});

