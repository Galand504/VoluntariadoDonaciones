document.addEventListener('DOMContentLoaded', function() {
    // Función existente para los conteos
    async function loadCardCounts() {
        try {
            const response = await fetch('http://localhost/Crowdfunding/public/GetDashboardCounts');
            const data = await response.json();
    
            if (data.status === 200) {
                document.querySelector('.row.g-4.mb-4 .col-12:nth-child(1) h2').textContent = data.personas || '0';
                document.querySelector('.row.g-4.mb-4 .col-12:nth-child(2) h2').textContent = data.empresas || '0';
                document.querySelector('.row.g-4.mb-4 .col-12:nth-child(3) h2').textContent = data.voluntariados || '0';
                document.querySelector('.row.g-4.mb-4 .col-12:nth-child(4) h2').textContent = data.donaciones || '0';
            } else {
                console.error('Error al obtener los conteos:', data.message);
            }
        } catch (error) {
            console.error('Error al cargar los conteos:', error);
        }
    }

    // Nueva función para cargar el gráfico
    async function loadChartData() {
        try {
            const response = await fetch('http://localhost/Crowdfunding/public/GetRegistrosPorFecha');
            const data = await response.json();

            if (data.status === 200) {
                const ctx = document.getElementById('userChart').getContext('2d');
                
                // Destruir el gráfico existente si hay uno
                if (window.myChart instanceof Chart) {
                    window.myChart.destroy();
                }

                window.myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.fechas,
                        datasets: [{
                            label: 'Registros de Usuarios',
                            data: data.conteos,
                            fill: true,
                            borderColor: '#2196f3',
                            backgroundColor: 'rgba(33, 150, 243, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: '#333'
                                }
                            },
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#333'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#333'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error al cargar los datos del gráfico:', error);
        }
    }

    // Función para cargar las actividades
    async function loadActivities() {
        try {
            const response = await fetch('http://localhost/Crowdfunding/public/proyecto/actividades');
            const data = await response.json();

            if (data.status === 200) {
                const activityList = document.querySelector('.activity-list');
                activityList.innerHTML = ''; // Limpiar la lista actual

                data.actividades.forEach(actividad => {
                    const activityHTML = `
                        <li class="activity-item" data-type="${actividad.tipo_actividad.toLowerCase()}">
                            <div class="date">${actividad.objetivo}</div>
                            <div class="description">${actividad.titulo}</div>
                        </li>
                    `;
                    activityList.insertAdjacentHTML('beforeend', activityHTML);
                });
            } else {
                console.error('Error al obtener las actividades:', data.message);
            }
        } catch (error) {
            console.error('Error al cargar las actividades:', error);
        }
    }

    // Nueva función para cargar donadores estrella
    async function loadTopDonors() {
        try {
            const response = await fetch('http://localhost/Crowdfunding/public/recompensa/donadores');
            const data = await response.json();
            console.log('Respuesta del servidor:', data); // Para debug
    
            // Verificar si data.data.data existe y es un array
            if (data && data.data && Array.isArray(data.data.data)) {
                const donorsList = document.querySelector('.donors-list');
                donorsList.innerHTML = ''; // Limpiar la lista actual
    
                data.data.data.forEach((donador, index) => {
                    const badgeClass = index === 0 ? 'bg-primary' : 
                                     index === 1 ? 'bg-secondary' : 'bg-bronze';
                    
                    const donorHTML = `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">${donador.nombre_donador}</h6>
                                <small class="text-muted">Total donado: $${donador.monto_total}</small>
                            </div>
                            <span class="badge ${badgeClass} rounded-pill">${index + 1}</span>
                        </li>
                    `;
                    donorsList.insertAdjacentHTML('beforeend', donorHTML);
                });
            } else {
                console.error('Estructura de datos incorrecta:', data);
                const donorsList = document.querySelector('.donors-list');
                donorsList.innerHTML = '<li class="list-group-item">No hay donadores disponibles</li>';
            }
        } catch (error) {
            console.error('Error completo:', error);
            const donorsList = document.querySelector('.donors-list');
            donorsList.innerHTML = '<li class="list-group-item">Error al cargar los donadores</li>';
        }
    }

    // Cargar datos cuando se carga la página
    loadCardCounts();
    loadChartData();
    loadTopDonors();

    // Solo intentar cargar actividades si existe el contenedor
    const activityList = document.querySelector('.activity-list');
    if (activityList) {
        loadActivities();
    }

    // Actualizar datos cada 5 minutos
    setInterval(() => {
        loadCardCounts();
        loadChartData();
        loadTopDonors();
        if (activityList) {
            loadActivities();
        }
    }, 300000);

    // Manejar el menú de perfil solo si los elementos existen
    const profileButton = document.querySelector('.nav-link.dropdown-toggle');
    const profileMenu = document.querySelector('.dropdown-menu');
    
    if (profileButton && profileMenu) {
        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    // Agregar eventos a los botones de filtro
    document.querySelectorAll('.filter-button').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.textContent.toLowerCase();
            filterActivities(type);
        });
    });

});

// Función para filtrar actividades
function filterActivities(type) {
    const activities = document.querySelectorAll('.activity-item');
    activities.forEach(activity => {
        if (type === 'all' || activity.getAttribute('data-type') === type.toLowerCase()) {
            activity.style.display = 'flex';
        } else {
            activity.style.display = 'none';
        }
    });
}


