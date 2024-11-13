
function actualizarFormulario(tipoUsuario) {
    // Mostrar/ocultar las secciones de acuerdo al tipo de usuario
    const personaForm = document.getElementById('persona_form');
    const empresaForm = document.getElementById('empresa_form');

    // Si el tipo de usuario es 'persona', mostrar los campos de persona y ocultar los de empresa
    if (tipoUsuario === 'persona') {
        personaForm.style.display = 'block';
        empresaForm.style.display = 'none';
    } 
    // Si el tipo de usuario es 'empresa', mostrar los campos de empresa y ocultar los de persona
    else if (tipoUsuario === 'empresa') {
        personaForm.style.display = 'none';
        empresaForm.style.display = 'block';
    }
}

// Escuchar el cambio en el tipo de usuario
document.getElementById('tipo_usuario').addEventListener('change', function() {
    // Obtener el tipo de usuario seleccionado
    const tipoUsuario = this.value;

    // Actualizar el formulario mostrando los campos correspondientes
    actualizarFormulario(tipoUsuario);
});

// Mostrar el formulario correcto al cargar la página
window.onload = function() {
    // Obtener el valor del tipo de usuario (en caso de que ya haya sido seleccionado)
    const tipoUsuario = document.getElementById('tipo_usuario').value;

    // Mostrar el formulario correcto según el valor inicial
    actualizarFormulario(tipoUsuario);
};

