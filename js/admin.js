// Funcionalidad de navegación
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
const content = document.querySelector('.content');
const profileButton = document.querySelector('.profile');
const profileMenu = document.getElementById('profile-menu');

// Expandir/colapsar sidebar
menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('expanded');
});

// Mostrar/ocultar menú de perfil
profileButton.addEventListener('click', () => {
    profileMenu.classList.toggle('profile-menu-visible');
});

// Cerrar menú de perfil al hacer clic fuera de él
document.addEventListener('click', (event) => {
    if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
        profileMenu.classList.remove('profile-menu-visible');
    }
});

// Crear gráfico de registros de usuarios y empresas
document.addEventListener('DOMContentLoaded', function () {
    // Obtener el contexto del canvas
    const ctx = document.getElementById('userChart').getContext('2d');
    
    // Crear el gráfico
    const userChart = new Chart(ctx, {
        type: 'line', // Tipo de gráfico
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'], // Días de la semana
            datasets: [
                {
                    label: 'Usuarios Registrados', // Título de la primera línea
                    data: [10, 15, 8, 12, 18, 10, 20], // Datos de usuarios registrados
                    borderColor: 'rgba(75, 192, 192, 1)', // Color de la línea
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color del área debajo de la línea
                    fill: true, // Rellenar el área debajo de la línea
                    tension: 0.4 // Curvatura de la línea
                },
                {
                    label: 'Empresas Registradas', // Título de la segunda línea
                    data: [5, 10, 15, 10, 5, 20, 25], // Datos de empresas registradas
                    borderColor: 'rgba(255, 99, 132, 1)', // Color de la línea
                    backgroundColor: 'rgba(255, 99, 132, 0.2)', // Color del área debajo de la línea
                    fill: true, // Rellenar el área debajo de la línea
                    tension: 0.4 // Curvatura de la línea
                }
            ]
        },
        options: {
            responsive: true, // El gráfico se ajusta a diferentes tamaños de pantalla
            plugins: {
                legend: {
                    position: 'top' // Posición de la leyenda (arriba)
                },
                tooltip: {
                    mode: 'index', // Modo de los tooltips
                    intersect: false // El tooltip se muestra cuando no hay intersección
                }
            },
            scales: {
                x: {
                    beginAtZero: true, // Iniciar el eje X desde cero
                },
                y: {
                    beginAtZero: true // Iniciar el eje Y desde cero
                }
            }
        }
    });
});


