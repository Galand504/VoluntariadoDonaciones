function renderUsuarios(data) {
    console.log('Iniciando renderizado con datos:', data);

    // Renderizar usuarios tipo persona
    const tbodyPersonas = document.querySelector('#tabla-usuarios-personas');
    console.log('Elemento tbody personas encontrado:', tbodyPersonas);
    
    if (tbodyPersonas) {
        tbodyPersonas.innerHTML = '';
        if (data.persona && Array.isArray(data.persona)) {
            console.log(`Renderizando ${data.persona.length} personas`);
            data.persona.forEach((persona, index) => {
                console.log(`Renderizando persona ${index + 1}:`, persona);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${persona.id_usuario || ''}</td>
                    <td>${persona.DNI || ''}</td>
                    <td>${persona.Nombre || ''}</td>
                    <td>${persona.Apellido || ''}</td>
                    <td>${persona.email || ''}</td>
                    <td>${persona.contraseña || ''}</td>
                    <td>${persona.Edad || ''}</td>
                    <td>${persona.Telefono || ''}</td>
                    <td>${persona.Rol || ''}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarUsuario(${persona.id_usuario}, 'persona')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${persona.id_usuario})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                `;
                tbodyPersonas.appendChild(tr);
                console.log('Fila agregada para persona:', tr.innerHTML);
            });
        } else {
            console.log('No hay datos de personas o no es un array:', data.persona);
        }
    } else {
        console.error('No se encontró el elemento tbody para personas');
    }

    // Renderizar usuarios tipo empresa
    const tbodyEmpresas = document.querySelector('#tabla-usuarios-empresas');
    console.log('Elemento tbody empresas encontrado:', tbodyEmpresas);
    
    if (tbodyEmpresas) {
        tbodyEmpresas.innerHTML = '';
        if (data.empresa && Array.isArray(data.empresa)) {
            console.log(`Renderizando ${data.empresa.length} empresas`);
            data.empresa.forEach((empresa, index) => {
                console.log(`Renderizando empresa ${index + 1}:`, empresa);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${empresa.id_usuario || ''}</td>
                    <td>${empresa.nombreEmpresa || ''}</td>
                    <td>${empresa.email || ''}</td>
                    <td>${empresa.contraseña || ''}</td>
                    <td>${empresa.razonSocial || ''}</td>
                    <td>${empresa.registroFiscal || ''}</td>
                    <td>${empresa.telefonoEmpresa || ''}</td>
                    <td>${empresa.direccion || ''}</td>
                    <td>${empresa.Rol || ''}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarUsuario(${empresa.id_usuario}, 'empresa')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${empresa.id_usuario})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                `;
                tbodyEmpresas.appendChild(tr);
                console.log('Fila agregada para empresa:', tr.innerHTML);
            });
        } else {
            console.log('No hay datos de empresas o no es un array:', data.empresa);
        }
    } else {
        console.error('No se encontró el elemento tbody para empresas');
    }
}

function getAllUsuarios() {
    console.log('Iniciando getAllUsuarios');
    const apiUrl = "http://localhost/Crowdfunding/public/GetAllUsuarios";
    const token = localStorage.getItem('jwt_token');

    fetch(apiUrl, {
        method: "GET",
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
        }
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Datos recibidos del servidor:", data);
        if (data.status === 200) {
            console.log("Llamando a renderUsuarios con:", data.data.data);
            renderUsuarios(data.data.data);
        } else {
            console.error("Error en la respuesta:", data.message);
            alert("Error al obtener los usuarios: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
        if (error.message.includes('401')) {
            alert("Sesión expirada. Por favor, inicie sesión nuevamente.");
            window.location.href = '../html/login.html';
        } else {
            alert("Error al obtener los datos. Por favor, intente nuevamente.");
        }
    });
}

// Función para eliminar usuario
function eliminarUsuario(id) {
    if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
        const apiUrl = `http://localhost/Crowdfunding/public/DeleteUsuario/${id}`;
        const token = localStorage.getItem('jwt_token');

        fetch(apiUrl, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                alert('Usuario eliminado con éxito');
                getAllUsuarios(); // Recargar la lista
            } else {
                alert(data.message || 'Error al eliminar el usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el usuario');
        });
    }
}

// Función para editar usuario
function editarUsuario(id, tipo) {
    const token = localStorage.getItem('jwt_token');
    
    fetch(`http://localhost/Crowdfunding/public/GetUsuarioById?id=${id}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 || data.status === 'success') {
            // Asignar valores básicos
            document.getElementById('updateUsuarioId').value = id;
            document.getElementById('updateEmail').value = data.data.email || '';
            document.getElementById('updateUsuarioTipo').value = tipo; // Mantener como hidden

            // Mostrar/ocultar campos según el tipo
            const personaFields = document.getElementById('personaFields');
            const empresaFields = document.getElementById('empresaFields');

            console.log('Tipo de usuario:', tipo); // Para depuración

            if (tipo.toLowerCase() === 'persona') {
                personaFields.style.display = 'block';
                empresaFields.style.display = 'none';
                
                // Llenar campos de persona
                document.getElementById('updateDNI').value = data.data.dni || '';
                document.getElementById('updateNombre').value = data.data.nombre || '';
                document.getElementById('updateApellido').value = data.data.apellido || '';
                document.getElementById('updateTelefono').value = data.data.telefono || '';
                document.getElementById('updateEdad').value = data.data.edad || '';
            } else if (tipo.toLowerCase() === 'empresa') {
                personaFields.style.display = 'none';
                empresaFields.style.display = 'block';
                
                // Llenar campos de empresa
                document.getElementById('updateNombreEmpresa').value = data.data.nombreEmpresa || '';
                document.getElementById('updateRazonSocial').value = data.data.razonSocial || '';
                document.getElementById('updateRegistroFiscal').value = data.data.registroFiscal || '';
                document.getElementById('updateTelefonoEmpresa').value = data.data.telefonoEmpresa || '';
                document.getElementById('updateDireccion').value = data.data.direccion || '';
            }

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalActualizarUsuario'));
            modal.show();
        } else {
            alert(data.message || 'Error al obtener los datos del usuario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al obtener los datos del usuario');
    });
}

// Manejar el botón de guardar cambios
document.getElementById('btnGuardarCambios').addEventListener('click', function() {
    const id = document.getElementById('updateUsuarioId').value;
    const tipoOriginal = document.getElementById('updateUsuarioTipo').value;
    // Normalizar el tipo con la primera letra en mayúscula
    const tipo = tipoOriginal.charAt(0).toUpperCase() + tipoOriginal.slice(1).toLowerCase();
    const token = localStorage.getItem('jwt_token');

    // Datos base que siempre se envían
    let userData = {
        id_usuario: id,
        email: document.getElementById('updateEmail').value.trim(),
        tipo: tipo // Usar el tipo normalizado
    };

    // Agregar campos específicos según el tipo
    if (tipo === 'Persona') {
        userData = {
            ...userData,
            nombre: document.getElementById('updateNombre').value.trim(),
            apellido: document.getElementById('updateApellido').value.trim(),
            dni: document.getElementById('updateDNI').value.trim(),
            edad: document.getElementById('updateEdad').value.trim(),
            telefono: document.getElementById('updateTelefono').value.trim()
        };
    } else if (tipo === 'Empresa') {
        userData = {
            ...userData,
            nombreEmpresa: document.getElementById('updateNombreEmpresa').value.trim(),
            razonSocial: document.getElementById('updateRazonSocial').value.trim(),
            registroFiscal: document.getElementById('updateRegistroFiscal').value.trim(),
            telefonoEmpresa: document.getElementById('updateTelefonoEmpresa').value.trim(),
            direccion: document.getElementById('updateDireccion').value.trim()
        };
    }

    // Si hay contraseña, agregarla
    const contraseña = document.getElementById('updateContraseña').value.trim();
    if (contraseña) {
        userData.contraseña = contraseña;
    }

    console.log('Tipo de usuario:', tipo);
    console.log('Datos a enviar:', userData);

    fetch('http://localhost/Crowdfunding/public/UpdateUsuario', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.status === 200 || data.status === 'success') {
            // Cerrar el modal primero
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarUsuario'));
            modal.hide();
            
            // Mostrar mensaje y luego recargar
            if (confirm('Usuario actualizado exitosamente')) {
                window.location.href = window.location.href;
            } else {
                window.location.href = window.location.href;
            }
        } else {
            alert(data.message || 'Error al actualizar el usuario');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        alert('Error al actualizar el usuario. Por favor, revise la consola para más detalles.');
    });
});

// Agregar event listener para el cambio de tipo
document.getElementById('updateUsuarioTipo').addEventListener('change', function() {
    const personaFields = document.getElementById('personaFields');
    const empresaFields = document.getElementById('empresaFields');
    
    if (this.value === 'Persona') {
        personaFields.style.display = 'block';
        empresaFields.style.display = 'none';
    } else {
        personaFields.style.display = 'none';
        empresaFields.style.display = 'block';
    }
});

// Asegurarse de que el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar la tabla
    getAllUsuarios();
    
    // Agregar event listener para el formulario de actualización
    const formUpdate = document.getElementById('formUpdateUsuario');
    if (formUpdate) {
        formUpdate.addEventListener('submit', function(e) {
            e.preventDefault();
            // El botón de guardar cambios manejará la actualización
        });
    }

    // Inicializar el cambio de tipo de usuario
    const tipoSelect = document.getElementById('updateUsuarioTipo');
    if (tipoSelect) {
        // Disparar el evento change inicialmente para configurar los campos correctamente
        tipoSelect.dispatchEvent(new Event('change'));
    }
});

