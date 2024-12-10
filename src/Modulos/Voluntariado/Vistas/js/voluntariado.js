document.addEventListener('DOMContentLoaded', function() {
    cargarVoluntariados();

    // Event Listeners para los botones de participar
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('boton-participar')) {
            const idActividad = e.target.closest('.invitacion').dataset.id;
            document.getElementById('id_actividad').value = idActividad;
            document.getElementById('participarModal').style.display = 'block';
        }
    });

    // Event Listeners para cerrar modales
    document.querySelectorAll('.close').forEach(function(closeBtn) {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // Manejar envío del formulario
    document.getElementById('formularioVoluntariado').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const data = {
            idProyecto: document.getElementById('id_actividad').value,
            disponibilidad: document.getElementById('disponibilidad').value
        };

        try {
            const response = await fetch('http://localhost/Crowdfunding/public/voluntariado/vincular', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const responseData = await response.json();
            console.log('Respuesta de vinculación:', responseData);

            if (responseData.status === 'OK') {
                // Cerrar el modal de participación
                document.getElementById('participarModal').style.display = 'none';
                
                // Limpiar el formulario
                document.getElementById('formularioVoluntariado').reset();
                
                // Mostrar mensaje de éxito con el modal personalizado
                const mensaje = responseData.data.message || '¡Registro exitoso! Gracias por unirte como voluntario.';
                showAlert(mensaje);
                
                // Recargar los voluntariados para actualizar la lista
                await cargarVoluntariados();
            } else {
                showAlert(responseData.message || 'Error al procesar el registro');
            }
        } catch (error) {
            console.error('Error al vincular:', error);
            showAlert('Error al procesar el registro. Por favor, intente nuevamente.');
        }
    });
});

async function cargarVoluntariados() {
    const token = localStorage.getItem('jwt_token');
    console.log('Token recuperado:', token);

    if (!token) {
        console.error('No hay token disponible');
        window.location.href = '/src/Modulos/Usuarios/Vistas/login.php';
        return;
    }

    try {
        const response = await fetch('http://localhost/Crowdfunding/public/voluntariado/obtener', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const responseData = await response.json();
        console.log('Respuesta completa:', responseData);

        if (responseData.status === 'OK') {
            const voluntariados = responseData.data.data;
            console.log('Voluntariados a mostrar:', voluntariados);
            
            if (Array.isArray(voluntariados)) {
                mostrarVoluntariados(voluntariados);
            } else {
                console.log('No hay voluntariados disponibles');
                alert('No hay voluntariados disponibles en este momento');
            }
        } else {
            if (responseData.status === 401) {
                console.error('Token inválido');
                localStorage.removeItem('jwt_token');
                window.location.href = '/src/Modulos/Usuarios/Vistas/login.php';
            } else {
                console.error('Error en la respuesta:', responseData);
                alert(responseData.message || 'Error al cargar los voluntariados');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar las actividades de voluntariado');
    }
}

function mostrarVoluntariados(voluntariados) {
    if (!Array.isArray(voluntariados)) {
        console.error('Se esperaba un array de voluntariados:', voluntariados);
        return;
    }

    const container = document.querySelector('.container');
    container.innerHTML = '';

    if (voluntariados.length === 0) {
        container.innerHTML = '<p class="no-voluntariados">No hay voluntariados disponibles en este momento.</p>';
        return;
    }

    voluntariados.forEach(function(voluntariado) {
        const card = `
            <div class="invitacion" data-id="${voluntariado.idProyecto}">
                <i class="fas fa-hands-helping icono"></i>
                <div class="detalle">
                    <p class="titulo">${voluntariado.titulo}</p>
                    <p class="descripcion">${voluntariado.descripcion}</p>
                    <p class="objetivo">${voluntariado.objetivo}</p>
                    <p class="voluntarios">
                        <strong>Voluntarios actuales:</strong> 
                        <span class="voluntarios-count">${voluntariado.voluntarios_vinculados}</span>
                    </p>
                    <button class="boton-participar">Participar como Voluntario</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', card);
    });
}

// Agregar esta función para mostrar alertas personalizadas
function showAlert(message) {
    const alertModal = document.getElementById('alertModal');
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.textContent = message;
    alertModal.style.display = 'block';

    // Cerrar con el botón de aceptar
    const btnOk = alertModal.querySelector('.btn-ok');
    btnOk.onclick = function() {
        alertModal.style.display = 'none';
    }

    // Cerrar con la X
    const closeBtn = alertModal.querySelector('.close');
    closeBtn.onclick = function() {
        alertModal.style.display = 'none';
    }

    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        if (event.target == alertModal) {
            alertModal.style.display = 'none';
        }
    }
}