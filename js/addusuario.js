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
    document.getElementById("formulario").addEventListener("submit", function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de manera tradicional
    
        // Obtener tipo de usuario
        const Tipo = document.getElementById("Tipo").value;

        // Crear un objeto FormData para recolectar los datos del formulario
        const formData = new FormData(this);
    
        
        formData.append("Tipo", Tipo);
    
        // Obtener la URL de la API
        const apiUrl = "/Rutas/AddUsuario.php";
    
        // Crear una solicitud AJAX con fetch para enviar los datos a la API
        fetch(apiUrl, {
            method: "POST", // Utilizamos el método POST
            body: formData, // Enviamos los datos del formulario
            headers: {
                "Accept": "application/json" // Indicamos que aceptamos respuestas en formato JSON
            }
        })
        .then(response => response.json()) // Convertimos la respuesta a JSON
        .then(data => {
            if (data.success) {
                // Si la respuesta indica éxito, mostramos un mensaje de éxito
                alert("Usuario registrado correctamente");
                document.getElementById("formulario").reset(); // Limpiamos el formulario
            } else {
                // Si ocurre un error, mostramos un mensaje de error
                alert("Hubo un error al registrar al usuario: " + data.message);
            }
        })
        .catch(error => {
            // Si hay un error en la solicitud, lo mostramos en consola
            console.error("Error en la solicitud:", error);
            alert("Hubo un problema al procesar tu solicitud.");
        });
    });
    