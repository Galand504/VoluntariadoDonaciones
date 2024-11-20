   document.getElementById('Tipo').addEventListener('change', function() {
        var Tipo = this.value;
        
        // Ocultar ambos formularios inicialmente
        document.getElementById('form_persona').style.display = 'none';
        document.getElementById('form_empresa').style.display = 'none';
        
        // Mostrar el formulario correspondiente según la selección
        if (Tipo === 'Persona') {
            document.getElementById('form_persona').style.display = 'block';
        } else if (Tipo === 'Empresa') {
            document.getElementById('form_empresa').style.display = 'block';
        }
    });
    // Función para mostrar el formulario adecuado según el tipo de usuario
function toggleForm() {
    const tipoUsuario = document.getElementById("Tipo").value;
    if (tipoUsuario === "Persona") {
        document.getElementById("form_persona").style.display = "block";
        document.getElementById("form_empresa").style.display = "none";
    } else {
        document.getElementById("form_persona").style.display = "none";
        document.getElementById("form_empresa").style.display = "block";
    }
}

document.getElementById("formulario").addEventListener("submit", function(event) {
    event.preventDefault(); // Evita el envío tradicional del formulario

    // Obtener el tipo de usuario seleccionado
    const tipoUsuario = document.getElementById("Tipo").value;

    // Crear un objeto FormData
    const formData = new FormData(this);

    // Agregar tipo de usuario al FormData
    formData.append("Tipo", tipoUsuario);

    // Definir la URL de la API
    const apiUrl = "http://localhost/Crowdfunding/src/Rutas/AddUsuario.php";

    // Realizar la solicitud utilizando fetch
    fetch(apiUrl, {
        method: "POST",
        body: formData,
        headers: {
            "Accept": "application/json" // Indica que esperamos una respuesta en formato JSON
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la solicitud, código de respuesta: " + response.status);
        }
        return response.json(); // Convertir la respuesta a JSON
    })
    .then(data => {
        if (data.status === 'success') {
            alert("Usuario registrado correctamente");
            document.getElementById("formulario").reset(); // Limpiar el formulario
        } else {
            alert("Hubo un error al registrar al usuario: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
        alert("Hubo un problema al procesar tu solicitud.");
    });
});
