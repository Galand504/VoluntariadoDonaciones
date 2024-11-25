function toggleForm() {
    const tipo = document.getElementById("Tipo").value;

    // Mostrar/ocultar formularios dinámicamente
    document.getElementById("form_persona").style.display = tipo === "Persona" ? "block" : "none";
    document.getElementById("form_empresa").style.display = tipo === "Empresa" ? "block" : "none";
}

// Asociar el evento también usando el atributo onchange en el HTML
document.getElementById("Tipo").addEventListener("change", toggleForm);

// Manejar el envío del formulario con AJAX
document.getElementById("formulario").addEventListener("submit", function (event) {
    event.preventDefault(); // Evitar el envío tradicional del formulario

    // Crear un objeto con todos los datos del formulario
    const formData = new FormData(this);
    const data = {};
    
    formData.forEach((value, key) => {
        data[key] = value; // Convertir FormData a un objeto regular
    });

    // Añadir manualmente el tipo de usuario (Persona o Empresa)
    const tipoUsuario = document.getElementById("Tipo").value;
    data["Tipo"] = tipoUsuario;
    

    // URL del servidor donde se procesará la solicitud
    const apiUrl = "http://localhost/Crowdfunding/public/AddUsuario";

    // Realizar la solicitud AJAX con fetch
    fetch(apiUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json", // Enviar los datos como JSON
            "Accept": "application/json" // Aceptar respuestas en formato JSON
        },
        body: JSON.stringify(data) // Convertir el objeto a JSON antes de enviarlo
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud, código: " + response.status);
            }
            return response.json(); // Convertir la respuesta a JSON
        })
        .then(data => {
            if (data.status === "success") {
                alert("Usuario registrado correctamente.");
                document.getElementById("formulario").reset(); // Limpiar formulario
                // Ocultar los formularios dinámicos tras limpiar
                document.getElementById("form_persona").style.display = "none";
                document.getElementById("form_empresa").style.display = "none";
            } else {
                alert("Error al registrar usuario: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
            alert("Hubo un problema al procesar tu solicitud. Inténtalo nuevamente.");
        });
});
