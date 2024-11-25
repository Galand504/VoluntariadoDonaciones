// Llamar a la función para cargar los usuarios al cargar la página
document.addEventListener("DOMContentLoaded", function () {
    getAllUsuarios();
});
document.addEventListener("DOMContentLoaded", function () {
    // Asumiendo que tienes un botón de eliminar en cada fila de la tabla
    // y el botón tiene el atributo `data-id` con el id del usuario.
    const eliminarButtons = document.querySelectorAll('.btnEliminar');
    
    eliminarButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id_usuario = button.getAttribute('data-id');
            eliminarUsuario(id_usuario);  // Llamamos a la función para eliminar el usuario
        });
    });
});

// Función para eliminar un usuario
function eliminarUsuario(id_usuario) {
    // Confirmar la eliminación antes de hacer la solicitud
    const confirmar = confirm("¿Estás seguro de que deseas eliminar este usuario?");
    if (!confirmar) return;

    // Hacer la solicitud DELETE para eliminar el usuario
    fetch(`http://localhost/Crowdfunding/public/DeleteUsuario/${id_usuario}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);  // Mostrar el mensaje de éxito
            getAllUsuarios();  // Refrescar la lista de usuarios
        } else {
            alert(data.message);  // Mostrar el mensaje de error
        }
    })
    .catch(error => {
        console.error('Error al eliminar el usuario:', error);
        alert('Hubo un problema al eliminar el usuario.');
    });
}


// Función para obtener todos los usuarios y renderizar las tablas
function getAllUsuarios() {
    const apiUrl = "http://localhost/Crowdfunding/public/GetAllUsuarios";

    fetch(apiUrl, {
        method: "GET",
        headers: {
            "Accept": "application/json"
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la solicitud, código: " + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            const personas = data.persona || [];
            const empresas = data.empresa || [];
            renderUsuarios(personas, empresas); // Renderizar ambas tablas
        } else {
            alert("Error al obtener los usuarios: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
        alert("Hubo un problema al obtener los datos. Inténtalo nuevamente.");
    });
}

// Función para renderizar las tablas de personas y empresas
function renderUsuarios(personas, empresas) {
    const tablaPersonas = document.getElementById("tabla-usuarios-personas");
    const tablaEmpresas = document.getElementById("tabla-usuarios-empresas");

    // Limpiar las tablas antes de renderizar
    tablaPersonas.innerHTML = "";
    tablaEmpresas.innerHTML = "";

    // Renderizar personas
    personas.forEach(persona => {
        const fila = `
            <tr>
                <td>${persona.id_usuario}</td>
                <td>${persona.DNI}</td>
                <td>${persona.Nombre}</td> 
                <td>${persona.Apellido}</td>
                <td>${persona.email}</td>
                <td>${persona.contraseña}</td>
                <td>${persona.Edad}</td>
                <td>${persona.Telefono}</td>
                <td>${persona.Rol}</td>
                <td>
                    <button class="btn btn-warning" onclick="mostrarModalActualizar(${persona.id_usuario}, 'persona')">Actualizar</button>
                    <button class="btn btn-danger" onclick="eliminarUsuario(${persona.id_usuario}, 'persona')">Eliminar</button>
                </td>
            </tr>`;
        tablaPersonas.innerHTML += fila;
    });

    // Renderizar empresas
    empresas.forEach(empresa => {
        const fila = `
            <tr>
                <td>${empresa.id_usuario}</td>
                <td>${empresa.nombreEmpresa}</td>
                <td>${empresa.email}</td>
                <td>${empresa.contraseña}</td>
                <td>${empresa.razonSocial}</td>
                <td>${empresa.registroFiscal}</td>
                <td>${empresa.telefonoEmpresa}</td>
                <td>${empresa.direccion}</td>
                <td>${empresa.Rol}</td>
                <td>
                    <button class="btn btn-warning" onclick="mostrarModalActualizar(${empresa.id_usuario}, 'empresa')">Actualizar</button>
                    <button class="btn btn-danger" onclick="eliminarUsuario(${empresa.id_usuario}, 'empresa')">Eliminar</button>
                </td>
            </tr>`;
        tablaEmpresas.innerHTML += fila;
    });
}

// Función para mostrar el modal de actualización
function mostrarModalActualizar(id_usuario, tipo) {
    // Obtener el modal y los elementos de los campos
    const modal = new bootstrap.Modal(document.getElementById('modalActualizarUsuario'));
    const modalTitle = document.getElementById('modalActualizarUsuarioLabel');
    const inputId = document.getElementById('updateUsuarioId');
    const inputTipo = document.getElementById('updateUsuarioTipo');
    const inputEmail = document.getElementById('updateEmail');
    const personaFields = document.getElementById('personaFields');
    const empresaFields = document.getElementById('empresaFields');
    const inputRol = document.getElementById('updateUsuarioRol');  // Campo oculto para el rol

    // Limpiar los campos antes de mostrar los datos
    inputId.value = '';
    inputEmail.value = '';
    inputTipo.value = '';
    inputRol.value = '';  // Limpiar el campo de rol
    personaFields.style.display = 'none';
    empresaFields.style.display = 'none';

    // Obtener los datos de la API para el usuario
    fetch(`http://localhost/Crowdfunding/public/GetAllUsuarios/${id_usuario}?tipo=${tipo}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            let usuario = null;
            
            if (tipo === 'persona' && data.persona.length > 0) {
                // Buscar el usuario con el id_usuario correcto
                usuario = data.persona.find(u => u.id_usuario == id_usuario);
            } else if (tipo === 'empresa' && data.empresa.length > 0) {
                // Buscar el usuario con el id_usuario correcto
                usuario = data.empresa.find(u => u.id_usuario == id_usuario);  // Asumiendo que `id_empresa` es el identificador
            }            

            if (usuario) {
                // Asignar los valores del usuario
                inputId.value = usuario.id_usuario;
                inputEmail.value = usuario.email;
                inputTipo.value = tipo;
                inputRol.value = usuario.rol;  // Asignar el rol al campo oculto

                if (tipo === 'persona') {
                    modalTitle.textContent = 'Actualizar Usuario - Persona';
                    personaFields.style.display = 'block';

                    // Asignar datos de la persona
                    document.getElementById('updateDNI').value = usuario.DNI;
                    document.getElementById('updateNombre').value = usuario.Nombre;
                    document.getElementById('updateApellido').value = usuario.Apellido;
                    document.getElementById('updateTelefono').value = usuario.Telefono;
                    document.getElementById('updateEdad').value = usuario.Edad;
                } else if (tipo === 'empresa') {
                    modalTitle.textContent = 'Actualizar Usuario - Empresa';
                    empresaFields.style.display = 'block';

                    // Asignar datos de la empresa
                    document.getElementById('updateNombreEmpresa').value = usuario.nombreEmpresa;
                    document.getElementById('updateRazonSocial').value = usuario.razonSocial;
                    document.getElementById('updateTelefonoEmpresa').value = usuario.telefonoEmpresa;
                    document.getElementById('updateDireccion').value = usuario.direccion;
                    document.getElementById('updateRegistroFiscal').value = usuario.registroFiscal;
                }

                modal.show();
            }
        }
    })
    .catch(error => console.log('Error al cargar el usuario: ', error));
}

document.getElementById('btnGuardarCambios').addEventListener('click', updateUsuario);
// Función para actualizar el usuario
function updateUsuario() {
    const id_usuario = document.getElementById('updateUsuarioId').value;  // Obtenemos correctamente el ID
    const tipo = document.getElementById('updateUsuarioTipo').value;

    let payload = {
        id_usuario: id_usuario,
        email: document.getElementById('updateEmail').value,
        contraseña: document.getElementById('updateContraseña').value,  // Asegúrate de capturar este campo
        tipo: tipo,  // Tipo debe ser incluido
    };

    if (tipo === 'persona') {
        payload = {
            ...payload,
            nombre: document.getElementById('updateNombre').value,
            apellido: document.getElementById('updateApellido').value,
            dni: document.getElementById('updateDNI').value,
            edad: document.getElementById('updateEdad').value,
            telefono: document.getElementById('updateTelefono').value
        };
    } else if (tipo === 'empresa') {
        payload = {
            ...payload,
            nombreEmpresa: document.getElementById('updateNombreEmpresa').value,
            razonSocial: document.getElementById('updateRazonSocial').value,
            telefonoEmpresa: document.getElementById('updateTelefonoEmpresa').value,
            direccion: document.getElementById('updateDireccion').value,
            registroFiscal: document.getElementById('updateRegistroFiscal').value
        };
    }

    // Verifica que todos los campos obligatorios estén presentes antes de enviar la solicitud
    if (!payload.id_usuario || !payload.email || !payload.contraseña || !payload.tipo) {
        alert("Faltan datos obligatorios.");
        return;  // Evita continuar si faltan datos
    }

    fetch(`http://localhost/Crowdfunding/public/UpdateUsuario/${id_usuario}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Usuario actualizado correctamente.');
            getAllUsuarios(); // Refrescar la tabla después de actualizar
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarUsuario'));
            modal.hide();
        } else {
            alert(`Error al actualizar usuario: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        alert('Hubo un problema al actualizar el usuario.');
    });
}

