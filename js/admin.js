document.addEventListener('DOMContentLoaded', function() {
    // Función existente para los conteos
    async function loadCardCounts() {
        try {
            const response = await fetch('http://localhost/Crowdfunding/public/GetDashboardCounts');
            const data = await response.json();

            if (data.status === 200) {
                document.querySelector('.card:nth-child(1) .box h1').textContent = data.personas || '0';
                document.querySelector('.card:nth-child(2) .box h1').textContent = data.empresas || '0';
                document.querySelector('.card:nth-child(3) .box h1').textContent = data.voluntariados || '0';
                document.querySelector('.card:nth-child(4) .box h1').textContent = data.donaciones || '0';
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
            const response = await fetch('http://localhost/Crowdfunding/public/GetActividades');
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

    // Cargar datos cuando se carga la página
    loadCardCounts();
    loadChartData();
    loadActivities();

    // Agregar eventos a los botones de filtro
    document.querySelectorAll('.filter-button').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.textContent.toLowerCase();
            filterActivities(type);
        });
    });

    // Actualizar datos cada 5 minutos
    setInterval(() => {
        loadCardCounts();
        loadChartData();
        loadActivities();
    }, 300000); // 5 minutos en milisegundos

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

// Obtener los elementos del perfil y el menú
const profileButton = document.querySelector('.container .header .nav .user .img-case');
const profileMenu = document.getElementById('profile-menu');

// Alternar visibilidad del menú de perfil al hacer clic en la imagen
profileButton.addEventListener('click', function(event) {
    event.stopPropagation(); // Evitar que el evento se propague
    profileMenu.classList.toggle('profile-menu-visible');
});

// Cerrar el menú de perfil al hacer clic fuera de él
document.addEventListener('click', function(event) {
    if (!profileMenu.contains(event.target) && !profileButton.contains(event.target)) {
        profileMenu.classList.remove('profile-menu-visible');
    }
});


