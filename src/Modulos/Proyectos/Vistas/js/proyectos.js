function showAlert(message, type = 'error') {
    Swal.fire({
        text: message,
        icon: type,
        confirmButtonText: 'Aceptar',
        timer: type === 'success' ? 3000 : undefined
    });
}

function cargarProyectos() {
    fetch('http://localhost/Crowdfunding/public/proyecto/actividades', {
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Datos recibidos:', data);

        if (data.status === 'OK' && data.data && data.data.data) {
            const tbody = document.querySelector('#proyectosTable tbody');
            tbody.innerHTML = '';

            // Obtener el array de proyectos
            const proyectos = data.data.data;
            
            proyectos.forEach(proyecto => {
                console.log('Proyecto individual:', proyecto);
                
                // Acceder a los datos usando la estructura correcta
                const proyectoData = {
                    id: proyecto.idProyecto || '',
                    titulo: proyecto.titulo || '',
                    descripcion: proyecto.descripcion || '',
                    objetivo: proyecto.objetivo || '',
                    meta: proyecto.Meta,
                    estado: proyecto.estado || '',
                    tipoActividad: proyecto.tipo_actividad || ''
                };

                // Formatear la meta solo si tiene un valor
                let metaFormateada = '0.00';
                if (proyectoData.meta !== null && proyectoData.meta !== undefined && proyectoData.meta !== '') {
                    const meta = parseFloat(proyectoData.meta);
                    if (!isNaN(meta)) {
                        metaFormateada = meta.toLocaleString('es-HN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }

                tbody.innerHTML += `
                    <tr>
                        <td>${proyectoData.id}</td>
                        <td>${proyectoData.titulo}</td>
                        <td>${proyectoData.descripcion}</td>
                        <td>${proyectoData.objetivo}</td>
                        <td>${metaFormateada}</td>
                        <td>${proyectoData.estado}</td>
                        <td>${proyectoData.tipoActividad}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editarProyecto(${proyectoData.id})">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarProyecto(${proyectoData.id})">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            console.error('Estructura de datos inválida:', data);
            showAlert('Error al cargar los proyectos');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al cargar los proyectos');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    cargarProyectos();

    const proyectoForm = document.getElementById('proyectoForm');
    if (proyectoForm) {
        const tipoActividadInput = document.getElementById('tipo_actividad');
        const metaInput = document.getElementById('meta');
        
        if (tipoActividadInput && metaInput) {
            tipoActividadInput.addEventListener('change', function() {
                if (this.value === 'Donacion') {
                    metaInput.setAttribute('min', '1');
                    metaInput.required = true;
                    metaInput.closest('.mb-3').querySelector('label').innerHTML = 
                        'Meta <span class="text-danger">*</span> (Requerido para Donaciones)';
                } else {
                    metaInput.setAttribute('min', '0');
                    metaInput.required = false;
                    metaInput.closest('.mb-3').querySelector('label').innerHTML = 'Meta';
                }
            });
        }

        proyectoForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const elementos = {
                titulo: document.getElementById('titulo'),
                descripcion: document.getElementById('descripcion'),
                objetivo: document.getElementById('objetivo'),
                meta: document.getElementById('meta'),
                moneda: document.getElementById('moneda'),
                estado: document.getElementById('estado'),
                tipo_actividad: document.getElementById('tipo_actividad'),
                idProyecto: document.getElementById('idProyecto')
            };

            // Verificar elementos requeridos
            for (const [key, element] of Object.entries(elementos)) {
                if (!element && key !== 'idProyecto') { // idProyecto es opcional
                    showAlert(`Error: Elemento ${key} no encontrado en el formulario`);
                    return;
                }
            }

            const data = {
                titulo: elementos.titulo.value.trim(),
                descripcion: elementos.descripcion.value.trim(),
                objetivo: elementos.objetivo.value.trim(),
                meta: parseFloat(elementos.meta.value) || 0,
                moneda: elementos.moneda.value,
                estado: elementos.estado.value,
                tipo_actividad: elementos.tipo_actividad.value
            };

            // Si hay un ID, es una actualización
            if (elementos.idProyecto && elementos.idProyecto.value) {
                data.idProyecto = elementos.idProyecto.value;
            }

            // Validación específica para proyectos de donación
            if (data.tipo_actividad === 'Donacion' && data.meta <= 0) {
                showAlert('Para proyectos de donación, la meta debe ser mayor que cero');
                elementos.meta.focus();
                return;
            }

            try {
                const url = data.idProyecto 
                    ? 'http://localhost/Crowdfunding/public/proyecto/actualizar'
                    : 'http://localhost/Crowdfunding/public/proyecto/crear';

                const response = await fetch(url, {
                    method: data.idProyecto ? 'PUT' : 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();

                if (responseData.status === 'OK') {
                    showAlert(
                        data.idProyecto ? 'Proyecto actualizado exitosamente' : 'Proyecto creado exitosamente',
                        'success'
                    );
                    proyectoForm.reset();
                    if (elementos.idProyecto) elementos.idProyecto.value = '';
                    document.querySelector('#proyectoForm button[type="submit"]').textContent = 'Guardar Proyecto';
                    await cargarProyectos();
                } else {
                    showAlert(responseData.message || 'Error al procesar el proyecto');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al procesar la solicitud');
            }
        });
    }
});

async function editarProyecto(idProyecto) {
    try {
        console.log('Editando proyecto:', idProyecto);

        const response = await fetch(`http://localhost/Crowdfunding/public/proyecto/obtener/${idProyecto}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        console.log('Respuesta completa:', data);
        
        if (data.status === 'OK') {
            // El proyecto está dentro de data.data.data
            const proyecto = data.data.data;
            console.log('Datos del proyecto:', proyecto);

            // Cargar datos en el formulario
            const form = document.getElementById('proyectoForm');
            if (!form) {
                console.error('Formulario no encontrado');
                return;
            }

            // Asignar valores a los campos
            const campos = {
                idProyecto: form.querySelector('#idProyecto'),
                titulo: form.querySelector('#titulo'),
                descripcion: form.querySelector('#descripcion'),
                objetivo: form.querySelector('#objetivo'),
                meta: form.querySelector('#meta'),
                moneda: form.querySelector('#moneda'),
                estado: form.querySelector('#estado'),
                tipo_actividad: form.querySelector('#tipo_actividad')
            };

            // Verificar que todos los campos existen
            for (const [key, element] of Object.entries(campos)) {
                if (!element) {
                    console.error(`Campo no encontrado: ${key}`);
                    showAlert(`Error: Campo ${key} no encontrado en el formulario`);
                    return;
                }
            }

            // Asignar valores de forma segura
            campos.idProyecto.value = proyecto.idProyecto || '';
            campos.titulo.value = proyecto.titulo || '';
            campos.descripcion.value = proyecto.descripcion || '';
            campos.objetivo.value = proyecto.objetivo || '';
            campos.meta.value = proyecto.meta || 0;
            campos.moneda.value = proyecto.moneda || 'HNL';
            campos.estado.value = proyecto.estado || 'En Proceso';
            campos.tipo_actividad.value = proyecto.tipo_actividad || 'Voluntariado';

            // Actualizar UI
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.textContent = 'Actualizar Proyecto';
                
            }

            // Mostrar botón de cancelar
            const cancelButton = form.querySelector('button.btn-secondary');
            if (cancelButton) {
                cancelButton.style.display = 'inline-block';
                cancelButton.addEventListener('click', function() {
                    window.location.reload();
                });
            }

            // Scroll al formulario
            form.scrollIntoView({ behavior: 'smooth' });
            
        } else {
            console.error('Error en la respuesta:', data);
            showAlert(data.message || 'Error al cargar el proyecto');
        }
    } catch (error) {
        console.error('Error en editarProyecto:', error);
        showAlert('Error al cargar el proyecto');
    }
}

function eliminarProyecto(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "No podrá revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const apiUrl = `http://localhost/Crowdfunding/public/proyecto/eliminar?id=${id}`;
            const token = localStorage.getItem('jwt_token');

            fetch(apiUrl, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor:', data);
                
                // Si el proyecto fue eliminado o marcado como cancelado
                if (data.status === 200 || data.status === 'OK') {
                    Swal.fire(
                        '¡Eliminado!',
                        data.message || 'Proyecto eliminado con éxito',
                        'success'
                    );
                    cargarProyectos();
                } else {
                    // Mostrar el mensaje específico del error
                    throw new Error(data.message || 'Error al eliminar el proyecto');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error',
                    error.message || 'Error al eliminar el proyecto',
                    'error'
                );
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Recargar la página si se presiona "Cancelar"
            window.location.reload();
        }
    });
}