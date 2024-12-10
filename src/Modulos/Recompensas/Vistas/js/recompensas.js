// Función para mostrar alertas usando SweetAlert2
function showAlert(message, type = 'error') {
    Swal.fire({
        text: message,
        icon: type,
        timer: 3000,
        showConfirmButton: false
    });
}

document.addEventListener('DOMContentLoaded', function() {
    cargarProyectosDonacion();
    cargarRecompensas();

    const recompensaForm = document.getElementById('recompensaForm');
    if (recompensaForm) {
        recompensaForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const descripcion = document.getElementById('descripcion').value.trim();
            const montoMinimo = parseFloat(document.getElementById('montoMinimo').value) || 0;
            const moneda = document.getElementById('moneda').value;
            const fechaEntregaEstimada = document.getElementById('fechaEntregaEstimada').value;
            const idProyecto = document.getElementById('idProyecto').value;
            
            // Obtener el estado solo si existe el campo (modo administrador)
            const estadoSelect = document.getElementById('estado');
            

            const data = {
                descripcion,
                montoMinimo,
                moneda,
                fechaEntregaEstimada,
                idProyecto,
                estado
            };

            try {
                const url = 'http://localhost/Crowdfunding/public/recompensa/registrar';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();

                if (responseData.status === 'OK') {
                    showAlert('Recompensa creada exitosamente', 'success');
                    recompensaForm.reset();
                    cargarRecompensas();
                } else {
                    showAlert(responseData.message || 'Error al crear la recompensa');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al procesar la solicitud');
            }
        });
    }
});

async function cargarProyectosDonacion() {
    try {
        const response = await fetch('http://localhost/Crowdfunding/public/proyecto/actividades?tipo=Donacion', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
            }
        });

        const data = await response.json();

        if (data.status === 'OK') {
            const selectProyecto = document.getElementById('idProyecto');
            if (!selectProyecto) {
                console.error('No se encontró el elemento select');
                return;
            }

            selectProyecto.innerHTML = '<option value="">Seleccione un proyecto de donación</option>';

            if (data.data && data.data.data && Array.isArray(data.data.data)) {
                const proyectos = data.data.data;
                
                proyectos.forEach(proyecto => {
                    if (proyecto && proyecto.idProyecto && proyecto.titulo) {
                        const option = document.createElement('option');
                        option.value = proyecto.idProyecto;
                        option.textContent = proyecto.titulo;
                        selectProyecto.appendChild(option);
                    }
                });
            }
        } else {
            showAlert('Error al cargar los proyectos de donación');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar los proyectos de donación');
    }
}

async function cargarRecompensas() {
    try {
        const response = await fetch('http://localhost/Crowdfunding/public/recompensa/listar', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
            }
        });

        const data = await response.json();

        if (data.status === 'OK') {
            const tablaRecompensas = document.getElementById('recompensasTable');
            
            if (!tablaRecompensas) {
                console.error('No se encontró la tabla de recompensas');
                return;
            }

            const tbody = tablaRecompensas.getElementsByTagName('tbody')[0];
            if (!tbody) {
                console.error('No se encontró el tbody en la tabla');
                return;
            }

            tbody.innerHTML = '';

            data.data.forEach(recompensa => {
                let estadoHTML = '';
                switch(recompensa.aprobada) {
                    case 'Aprobada':
                        estadoHTML = '<span class="badge bg-success">Aprobada</span>';
                        break;
                    case 'Rechazada':
                        estadoHTML = '<span class="badge bg-danger">Rechazada</span>';
                        break;
                    default:
                        estadoHTML = '<span class="badge bg-warning">Pendiente</span>';
                }

                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${recompensa.idRecompensa}</td>
                    <td>${recompensa.descripcion}</td>
                    <td>${recompensa.montoMinimo}</td>
                    <td>${recompensa.moneda}</td>
                    <td>${recompensa.fechaEntregaEstimada}</td>
                    <td>${recompensa.nombre_proyecto || 'No asignado'}</td>
                    <td>${estadoHTML}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="editarRecompensa(${recompensa.idRecompensa})">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                `;
            });
        } else {
            showAlert('Error al cargar las recompensas');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar las recompensas');
    }
}

async function editarRecompensa(idRecompensa) {
    try {
        console.log('Editando recompensa:', idRecompensa);
        const response = await fetch(`http://localhost/Crowdfunding/public/recompensa/listar/${idRecompensa}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
            }
        });

        const data = await response.json();
        console.log('Datos recibidos:', data);

        if (data.status === 'OK') {
            const recompensa = Array.isArray(data.data) ? data.data[0] : data.data;
            console.log('Recompensa a editar:', recompensa);

            if (!recompensa) {
                showAlert('No se encontró la recompensa');
                return;
            }

            // Primero cargar los proyectos
            await cargarProyectosDonacion();

            // Luego cargar los datos en el formulario
            document.getElementById('idRecompensa').value = recompensa.idRecompensa || '';
            document.getElementById('descripcion').value = recompensa.descripcion || '';
            document.getElementById('montoMinimo').value = recompensa.montoMinimo || '';
            document.getElementById('moneda').value = recompensa.moneda || '';
            document.getElementById('fechaEntregaEstimada').value = recompensa.fechaEntregaEstimada || '';
            
            // Seleccionar el proyecto después de que se haya cargado la lista
            const selectProyecto = document.getElementById('idProyecto');
            if (selectProyecto) {
                selectProyecto.value = recompensa.idProyecto || '';
            }
            
            // Actualizar el estado si existe el campo (modo administrador)
            const estadoSelect = document.getElementById('estado');
            if (estadoSelect) {
                estadoSelect.value = recompensa.aprobada || 'Pendiente';
            }

            // Cambiar el texto del botón de guardar
            const submitButton = document.querySelector('#recompensaForm button[type="submit"]');
            if (submitButton) {
                submitButton.textContent = 'Actualizar Estado';
            }

            // Mostrar botón de cancelar
            const cancelButton = document.querySelector('#recompensaForm button.btn-secondary');
            if (cancelButton) {
                cancelButton.style.display = 'inline-block';
                cancelButton.onclick = function() {
                    window.location.reload();
                };
            }

            // Remover todos los event listeners anteriores del formulario
            const form = document.getElementById('recompensaForm');
            if (form) {
                const nuevoForm = form.cloneNode(true);
                form.parentNode.replaceChild(nuevoForm, form);
                
                // Agregar el nuevo event listener
                nuevoForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    actualizarRecompensa(e, idRecompensa);
                });
            }

            // Deshabilitar campos que no se deben editar
            document.getElementById('descripcion').disabled = true;
            document.getElementById('montoMinimo').disabled = true;
            document.getElementById('moneda').disabled = true;
            document.getElementById('fechaEntregaEstimada').disabled = true;
            document.getElementById('idProyecto').disabled = true;

        } else {
            showAlert('Error al cargar la recompensa');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar la recompensa');
    }
}

async function actualizarRecompensa(e, idRecompensa) {
    e.preventDefault();
    
    // Solo enviamos el ID de la recompensa y el estado de aprobación
    const data = {
        idRecompensa: idRecompensa
    };

    // Obtener el estado del select (modo administrador)
    const estadoSelect = document.getElementById('estado');
    if (estadoSelect) {
        data.aprobada = estadoSelect.value;
    } else {
        showAlert('No tienes permisos para cambiar el estado');
        return;
    }

    try {
        console.log('Datos a actualizar:', data);
        const response = await fetch('http://localhost/Crowdfunding/public/recompensa/aprobar', {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        console.log('Respuesta del servidor:', response);
        const responseData = await response.json();
        console.log('Datos de respuesta:', responseData);

        if (responseData.status === 'OK') {
            showAlert('Estado de recompensa actualizado exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert(responseData.message || 'Error al actualizar el estado de la recompensa');
        }
    } catch (error) {
        console.error('Error completo:', error);
        showAlert('Error al procesar la solicitud');
    }
}
