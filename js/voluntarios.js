document.getElementById("donacionRopaBtn").onclick = function() {
    document.getElementById("donacionRopaModal").style.display = "block";
};
document.getElementById("recoleccionAlimentosBtn").onclick = function() {
    document.getElementById("recoleccionAlimentosModal").style.display = "block";
};
document.getElementById("voluntariadoRefugioBtn").onclick = function() {
    document.getElementById("voluntariadoRefugioModal").style.display = "block";
};

// Cerrar los modales
document.getElementById("closeDonacionRopa").onclick = function() {
    document.getElementById("donacionRopaModal").style.display = "none";
};
document.getElementById("closeRecoleccionAlimentos").onclick = function() {
    document.getElementById("recoleccionAlimentosModal").style.display = "none";
};
document.getElementById("closeVoluntariadoRefugio").onclick = function() {
    document.getElementById("voluntariadoRefugioModal").style.display = "none";
};

// Cerrar modal si se hace clic fuera de él
window.onclick = function(event) {
    if (event.target == document.getElementById("donacionRopaModal")) {
        document.getElementById("donacionRopaModal").style.display = "none";
    } else if (event.target == document.getElementById("recoleccionAlimentosModal")) {
        document.getElementById("recoleccionAlimentosModal").style.display = "none";
    } else if (event.target == document.getElementById("voluntariadoRefugioModal")) {
        document.getElementById("voluntariadoRefugioModal").style.display = "none";
    }
};