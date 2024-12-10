// Variables globales
let tablaPagos;
let pagoActual;

// Constantes para rutas
const RUTAS = {
    ESTADISTICAS: 'http://localhost/Crowdfunding/public/pago/estadisticas',
    LISTAR: 'http://localhost/Crowdfunding/public/pago/listar',
    POR_FECHA: 'http://localhost/Crowdfunding/public/pago/por-fecha',
    POR_USUARIO: 'http://localhost/Crowdfunding/public/pago/por-usuario',
    ACTUALIZAR_ESTADO: 'http://localhost/Crowdfunding/public/pago/actualizar-estado',
    CANCELAR: 'http://localhost/Crowdfunding/public/pago/cancelar',
    DETALLES: 'http://localhost/Crowdfunding/public/pago/detalles',
    TOTALES: 'http://localhost/Crowdfunding/public/pago/totales'
};

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Cargado');
    inicializarAplicacion();
});

// Separar la inicialización en una función específica
async function inicializarAplicacion() {
    try {
        const tabla = document.getElementById('tablaPagos');
        if (!tabla) {
            console.error('Tabla de pagos no encontrada');
            return;
        }

        // Inicializar componentes en orden
        inicializarTabla();
        inicializarEventListeners();
        await cargarDatosIniciales();
        
    } catch (error) {
        console.error('Error en inicializarAplicacion:', error);
        mostrarAlerta('Error al inicializar la página', 'error');
    }
}

// Funciones de inicialización
function inicializarEventListeners() {
    console.log('Inicializando event listeners');
    
    // Filtros por fecha
    const btnFiltroFecha = document.getElementById('filtrarPorFecha');
    if (btnFiltroFecha) {
        console.log('Botón filtro fecha encontrado');
        btnFiltroFecha.onclick = async () => {
            const fechaInicio = document.getElementById('fechaInicio')?.value;
            
            if (!fechaInicio) {
                mostrarAlerta('Seleccione una fecha', 'warning');
                return;
            }
            
            await cargarPagosPorFecha(fechaInicio);
        };
    }

    // Filtro por usuario
    const btnFiltroUsuario = document.getElementById('filtrarPorUsuario');
    if (btnFiltroUsuario) {
        console.log('Botón filtro usuario encontrado');
        btnFiltroUsuario.onclick = async () => {
            const idUsuario = document.getElementById('idUsuario')?.value;
            if (!idUsuario) {
                mostrarAlerta('Ingrese un ID de usuario', 'warning');
                return;
            }
            await cargarPagosPorUsuario(idUsuario);
        };
    }

    // Formulario de filtros general
    const formFiltros = document.getElementById('filtrosPagos');
    if (formFiltros) {
        console.log('Formulario de filtros encontrado');
        formFiltros.onsubmit = async (e) => {
            e.preventDefault();
            await aplicarFiltros();
        };
    }
}

async function cargarDatosIniciales() {
    try {
        console.log('Cargando datos iniciales...');
        await cargarEstadisticas();
        await aplicarFiltros(); // Esto cargará los datos en la tabla
    } catch (error) {
        console.error('Error al cargar datos iniciales:', error);
    }
}

// Configuración de DataTables
function inicializarTabla() {
    try {
        console.log('Inicializando DataTable');
        if (!$.fn.DataTable) {
            console.error('DataTables no está cargado');
            return;
        }

        tablaPagos = $('#tablaPagos').DataTable({
            processing: true,
            language: {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ning��n dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":           "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            columns: [
                { data: 'idPago' },
                { 
                    data: 'fecha',
                    render: function(data) {
                        return moment(data).format('DD/MM/YYYY HH:mm');
                    }
                },
                { 
                    data: 'monto',
                    render: function(data, type, row) {
                        return formatearMontosTabla(data, type, row);
                    }
                },
                { data: 'moneda' },
                { 
                    data: 'estado',
                    render: function(data) {
                        return generarBadgeEstado(data);
                    }
                },
                { data: 'idDonacion' },
                { data: 'id_usuario' },
                { data: 'id_metodopago' },
                {
                    data: null,
                    render: function(data) {
                        return generarBotonesAcciones(data);
                    }
                }
            ]
        });

        console.log('DataTable inicializado correctamente');
    } catch (error) {
        console.error('Error al inicializar DataTable:', error);
    }
}

// Funciones de carga de datos
async function cargarEstadisticas() {
    try {
        console.log('Intentando cargar estadísticas...');
        const data = await fetchData(RUTAS.ESTADISTICAS);
        console.log('Respuesta estadísticas:', data);
        
        if (data && data.status === 'OK') {
            console.log('Actualizando estadísticas con:', data.data);
            actualizarEstadisticas(data.data.data);
        } else {
            console.error('Datos de estadísticas inválidos:', data);
        }
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    }
}

async function cargarPagosPorFecha(fechaInicio, fechaFin) {
    try {
        console.log('Cargando pagos por fecha:', { fechaInicio, fechaFin });
        const data = await fetchData(`${RUTAS.POR_FECHA}?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
        console.log('Respuesta pagos por fecha:', data);
        
        if (data && data.status === 'OK') {
            if (tablaPagos) {
                tablaPagos.clear().rows.add(data.data).draw();
                mostrarAlerta('Datos filtrados por fecha', 'success');
            }
        }
    } catch (error) {
        console.error('Error al cargar pagos por fecha:', error);
        mostrarAlerta('Error al filtrar por fecha', 'error');
    }
}

async function cargarPagosPorUsuario(idUsuario) {
    try {
        const data = await fetchData(`${RUTAS.POR_USUARIO}?id_usuario=${idUsuario}`);
        if (data.status === 'OK') {
            tablaPagos.clear().rows.add(data.data).draw();
        }
    } catch (error) {
        manejarError('Error al cargar pagos del usuario', error);
    }
}

// Funciones de actualización
async function actualizarEstadoPago(idPago, nuevoEstado) {
    try {
        const data = await fetchData(RUTAS.ACTUALIZAR_ESTADO, {
            method: 'POST',
            body: JSON.stringify({
                idPago: idPago,
                estado: nuevoEstado
            })
        });
        
        if (data.status === 'OK') {
            mostrarAlerta('Estado actualizado correctamente', 'success');
            await cargarEstadisticas();
            await aplicarFiltros();
        }
    } catch (error) {
        manejarError('Error al actualizar estado', error);
    }
}

async function cancelarPago(idPago, motivo) {
    try {
        const data = await fetchData(RUTAS.CANCELAR, {
            method: 'POST',
            body: JSON.stringify({
                idPago: idPago,
                motivo: motivo
            })
        });
        
        if (data.status === 'OK') {
            mostrarAlerta('Pago cancelado correctamente', 'success');
            await cargarEstadisticas();
            await aplicarFiltros();
        }
    } catch (error) {
        manejarError('Error al cancelar pago', error);
    }
}

// Funciones auxiliares
function generarBadgeEstado(estado) {
    const clases = {
        'Pendiente': 'bg-warning',
        'Completado': 'bg-success',
        'Fallido': 'bg-danger',
        'Cancelado': 'bg-secondary'
    };
    return `<span class="badge ${clases[estado] || 'bg-info'}">${estado}</span>`;
}

function generarBotonesAcciones(data) {
    try {
        console.log('Generando botones para pago:', data.idPago);
        
        // Crear el HTML de los botones usando onclick con window
        return `
            <div class="btn-group" role="group">
                <button type="button" 
                        class="btn btn-info btn-sm" 
                        title="Ver detalles"
                        onclick="window.verDetallesPago(${data.idPago})">
                    <i class="fas fa-eye"></i>
                </button>
                ${data.estado === 'Pendiente' ? `
                    <button type="button"
                            class="btn btn-success btn-sm" 
                            title="Marcar como completado"
                            onclick="window.actualizarEstadoPago(${data.idPago}, 'Completado')">
                        <i class="fas fa-check"></i>
                    </button>
                    <button type="button"
                            class="btn btn-danger btn-sm" 
                            title="Cancelar pago"
                            onclick="window.mostrarModalCancelacion(${data.idPago})">
                        <i class="fas fa-times"></i>
                    </button>
                ` : ''}
            </div>
        `;
    } catch (error) {
        console.error('Error al generar botones:', error);
        return '<div class="text-danger">Error en botones</div>';
    }
}

// Función para formatear montos en la tabla
function formatearMontosTabla(data, type, row) {
    if (type === 'display') {
        return formatearMoneda(data, row.moneda);
    }
    return data;
}

// Función para formatear moneda según el tipo
function formatearMoneda(monto, moneda = 'HNL') {
    const configuracionMoneda = {
        'HNL': {
            locale: 'es-HN',
            currency: 'HNL',
            symbol: 'L'
        },
        'USD': {
            locale: 'en-US',
            currency: 'USD',
            symbol: '$'
        }
    };

    const config = configuracionMoneda[moneda] || configuracionMoneda['HNL'];

    return new Intl.NumberFormat(config.locale, {
        style: 'currency',
        currency: config.currency,
        minimumFractionDigits: 2
    }).format(monto);
}

// Función para actualizar estadísticas
async function actualizarEstadisticas(data) {
    console.log('Iniciando actualización de estadísticas');
    
    // Obtener elementos del DOM
    const elementos = {
        totalPagos: document.getElementById('totalPagos'),
        pagosCompletados: document.getElementById('pagosCompletados'),
        pagosPendientes: document.getElementById('pagosPendientes'),
        pagosCancelados: document.getElementById('pagosCancelados'),
        montoTotal: document.getElementById('montoTotal')
    };

    // Actualizar contadores
    if (data) {
        console.log('Actualizando contadores con:', data);
        elementos.totalPagos.textContent = data.total_pagos || 0;
        elementos.pagosCompletados.textContent = data.pagos_completados || 0;
        elementos.pagosPendientes.textContent = data.pagos_pendientes || 0;
        elementos.pagosCancelados.textContent = data.pagos_cancelados || 0;
    }

    try {
        // Obtener el total convertido del procedimiento almacenado
        const response = await fetchData(RUTAS.TOTALES);
        console.log('Respuesta de totales:', response);

        if (response && response.status === 'OK' && response.data && response.data.data) {
            const montoTotal = response.data.data.total_HNL || 0;
            console.log('Monto total en HNL:', montoTotal);
            elementos.montoTotal.textContent = formatearMoneda(montoTotal, 'HNL');
        }
    } catch (error) {
        console.error('Error al obtener el monto total:', error);
        elementos.montoTotal.textContent = formatearMoneda(0, 'HNL');
    }
}

async function fetchData(url, options = {}) {
    try {
        console.log('Intentando fetch a:', url);
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            console.error('No hay token JWT');
            mostrarAlerta('No hay token de autenticación', 'error');
            return null;
        }

        const defaultOptions = {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        console.log('Opciones de fetch:', finalOptions);
        
        const response = await fetch(url, finalOptions);
        console.log('Respuesta del servidor:', response.status);
        
        const data = await response.json();
        console.log('Datos recibidos:', data);
        
        if (!response.ok) {
            throw new Error(data.message || 'Error en la petición');
        }
        
        return data;
    } catch (error) {
        console.error('Error en fetchData:', error);
        throw error;
    }
}

function manejarError(mensaje, error) {
    console.error(mensaje, error);
    mostrarAlerta(mensaje, 'error');
}

function mostrarAlerta(mensaje, tipo) {
    Swal.fire({
        text: mensaje,
        icon: tipo,
        timer: 3000,
        showConfirmButton: false
    });
}

async function aplicarFiltros() {
    try {
        console.log('Aplicando filtros...');
        const filtros = {
            fecha_inicio: document.getElementById('fechaInicio')?.value,
            fecha_fin: document.getElementById('fechaFin')?.value,
            id_usuario: document.getElementById('idUsuario')?.value,
            estado: document.getElementById('filtroEstado')?.value,
            metodo_pago: document.getElementById('filtroMetodoPago')?.value
        };

        const params = new URLSearchParams();
        Object.entries(filtros).forEach(([key, value]) => {
            if (value) params.append(key, value);
        });

        const response = await fetchData(`${RUTAS.LISTAR}?${params}`);
        const datos = response.data.data;
        
        console.log('Estructura del primer registro:', JSON.stringify(datos[0], null, 2));

        if (tablaPagos && Array.isArray(datos)) {
            tablaPagos.clear();
            tablaPagos.rows.add(datos).draw();
        }
    } catch (error) {
        console.error('Error al aplicar filtros:', error);
        mostrarAlerta('Error al cargar los datos', 'error');
    }
}

// Asegurarnos que la función esté disponible globalmente
window.verDetallesPago = async function(idPago) {
    try {
        console.log('Viendo detalles del pago:', idPago);
        const response = await fetchData(`${RUTAS.DETALLES}?id=${idPago}`);
        
        if (response && response.status === 'OK' && response.data) {
            const pago = response.data.data;
            console.log('Detalles recibidos:', pago);

            // Clonar el template
            const template = document.getElementById('detallesPagoTemplate');
            const contenido = template.content.cloneNode(true);

            // Llenar los datos
            contenido.querySelector('[data-detalle="idPago"]').textContent = pago.idPago;
            contenido.querySelector('[data-detalle="fecha"]').textContent = moment(pago.fecha).format('DD/MM/YYYY HH:mm');
            contenido.querySelector('[data-detalle="monto"]').textContent = formatearMoneda(pago.monto, pago.moneda);
            contenido.querySelector('[data-detalle="estado"]').innerHTML = generarBadgeEstado(pago.estado);
            contenido.querySelector('[data-detalle="referencia"]').textContent = pago.referencia_externa || 'N/A';
            contenido.querySelector('[data-detalle="donante"]').textContent = `${pago.nombre_donante} (${pago.tipo})`;
            contenido.querySelector('[data-detalle="email"]').textContent = pago.email_usuario;
            contenido.querySelector('[data-detalle="proyecto"]').textContent = pago.nombre_proyecto;
            contenido.querySelector('[data-detalle="metodoPago"]').textContent = pago.nombre_metodo_pago;

            // Si hay motivo de cancelación, agregar la fila
            if (pago.motivo) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <th>Motivo Cancelación</th>
                    <td>${pago.motivo}</td>
                `;
                contenido.querySelector('table').appendChild(tr);
            }

            // Crear un div temporal para convertir el DocumentFragment a HTML
            const tempDiv = document.createElement('div');
            tempDiv.appendChild(contenido);

            Swal.fire({
                title: 'Detalles del Pago',
                html: tempDiv.innerHTML,
                width: '600px',
                confirmButtonText: 'Cerrar',
                customClass: {
                    popup: 'swal-wide',
                    table: 'table-detail'
                }
            });
        } else {
            throw new Error('No se pudieron obtener los detalles del pago');
        }
    } catch (error) {
        console.error('Error al ver detalles:', error);
        mostrarAlerta('Error al cargar los detalles del pago', 'error');
    }
}
window.mostrarModalCancelacion = function(idPago) {
    Swal.fire({
        title: 'Cancelar Pago',
        text: '¿Está seguro de que desea cancelar este pago?',
        icon: 'warning',
        input: 'text',
        inputLabel: 'Motivo de cancelación',
        inputPlaceholder: 'Ingrese el motivo de la cancelación',
        inputValidator: (value) => {
            if (!value) {
                return 'Debe ingresar un motivo para la cancelación';
            }
        },
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, volver',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
    }).then((result) => {
        if (result.isConfirmed) {
            cancelarPago(idPago, result.value);
        }
    });
}

// También hacer globales las otras funciones
window.actualizarEstadoPago = actualizarEstadoPago;
window.mostrarModalCancelacion = mostrarModalCancelacion;
window.cancelarPago = cancelarPago;
