document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('userChart').getContext('2d');
    
    const data = {
        labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
        datasets: [{
            label: 'Registros de Usuarios',
            data: [30, 45, 60, 90, 75, 120, 150],
            fill: false,
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Días de la Semana'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Cantidad de Registros'
                    },
                    beginAtZero: true
                }
            }
        }
    };

    new Chart(ctx, config);
});

function filterActivities(type) {
    const activities = document.querySelectorAll('.activity-item');
    activities.forEach(activity => {
        if (activity.getAttribute('data-type') === type) {
            activity.style.display = 'flex';  // Mostrar actividades del tipo seleccionado
        } else {
            activity.style.display = 'none';  // Ocultar las actividades de otro tipo
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
    const modal = document.getElementById("modal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const formRegistrarActividad = document.getElementById("formRegistrarActividad");

    // Abrir el modal
    openModalBtn.onclick = function() {
        modal.style.display = "block";
    };

    // Cerrar el modal
    closeModalBtn.onclick = function() {
        modal.style.display = "none";
    };

    // Cerrar el modal si se hace clic fuera de él
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Manejar el envío del formulario
    formRegistrarActividad.addEventListener("submit", function(event) {
        event.preventDefault();
        const nombreActividad = document.getElementById("nombreActividad").value;
        const descripcionActividad = document.getElementById("descripcionActividad").value;
        const tipoActividad = document.getElementById("tipoActividad").value;

        if (!nombreActividad || !descripcionActividad || !tipoActividad) {
            alert("Por favor, completa todos los campos.");
            return;
        }

        console.log("Actividad registrada:");
        console.log("Nombre:", nombreActividad);
        console.log("Descripción:", descripcionActividad);
        console.log("Tipo de Actividad:", tipoActividad);

        formRegistrarActividad.reset();
        modal.style.display = "none";
    });


