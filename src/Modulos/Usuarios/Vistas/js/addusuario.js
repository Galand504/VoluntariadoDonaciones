document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulario');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        let tipoUsuario = document.getElementById('Tipo').value;
        const email = document.getElementById('email').value.trim();
        const contraseña = document.getElementById('contraseña').value.trim();
        const Rol = document.getElementById('Rol').value.trim();

        let userData = {
            email,
            contraseña,
            Rol,
            Tipo: tipoUsuario
        };

        if (tipoUsuario === 'Persona') {
            userData = {
                ...userData,
                nombre: document.getElementById('nombre').value.trim(),
                apellido: document.getElementById('apellido').value.trim(),
                dni: document.getElementById('dni').value.trim(),
                edad: document.getElementById('edad').value.trim(),
                telefono: document.getElementById('telefono').value.trim()
            };
        } else if (tipoUsuario === 'Empresa') {
            userData = {
                ...userData,
                nombreEmpresa: document.getElementById('nombreEmpresa').value.trim(),
                razonSocial: document.getElementById('razonSocial').value.trim(),
                registroFiscal: document.getElementById('registroFiscal').value.trim(),
                telefonoEmpresa: document.getElementById('telefonoEmpresa').value.trim(),
                direccion: document.getElementById('direccion').value.trim()
            };
        }

        console.log('Datos a enviar:', JSON.stringify(userData, null, 2));

        try {
            const response = await fetch('http://localhost/Crowdfunding/public/AddUsuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(userData)
            });

            const data = await response.json();
            console.log('Respuesta del servidor:', data);

            if (data.status === 'success' || data.status === 200) {
                alert('Usuario creado exitosamente');
                window.location.href = 'crud.html';
            } else {
                alert(data.message || 'Error al crear el usuario');
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            alert('Error al crear el usuario. Por favor, intente nuevamente.');
        }
    });
});

function toggleForm() {
    const tipoUsuario = document.getElementById('Tipo').value;
    const personaFields = document.getElementById('form_persona');
    const empresaFields = document.getElementById('form_empresa');

    if (tipoUsuario === 'Persona') {
        personaFields.style.display = 'block';
        empresaFields.style.display = 'none';
    } else {
        personaFields.style.display = 'none';
        empresaFields.style.display = 'block';
    }
}
