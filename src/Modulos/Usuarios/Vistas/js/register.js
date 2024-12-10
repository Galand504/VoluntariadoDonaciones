document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-register');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const tipoUsuario = document.getElementById('tipo_usuario').value;
            
            // Construir el objeto de datos según el tipo de usuario
            let data = {
                tipo_usuario: tipoUsuario
            };
            
            // Datos comunes para ambos tipos
            data.email = document.getElementById('email').value;
            data.contraseña = document.getElementById('contraseña').value;
            data.rol = document.getElementById('rol').value;
            
            if (tipoUsuario === 'persona') {
                data = {
                    ...data,
                    nombre: document.getElementById('nombre').value,
                    apellido: document.getElementById('apellido').value,
                    dni: document.getElementById('dni').value,
                    edad: document.getElementById('edad').value,
                    telefono: document.getElementById('telefono').value
                };
            } else {
                data = {
                    ...data,
                    nombreEmpresa: document.getElementById('nombreEmpresa').value,
                    razonSocial: document.getElementById('razonSocial').value,
                    registroFiscal: document.getElementById('registroFiscal').value,
                    direccion: document.getElementById('direccion').value,
                    telefonoEmpresa: document.getElementById('telefonoEmpresa').value
                };
            }

            // Validar campos requeridos según el tipo
            if (!validarCampos(tipoUsuario)) {
                return;
            }

            // Enviar datos al API
            const response = await fetch('http://localhost/Crowdfunding/public/usuario/registrar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                alert('Registro exitoso');
                window.location.href = 'login.php';
            } else {
                alert(result.message || 'Error en el registro');
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Error en el registro');
        }
    });
});

// Función para validar campos según tipo de usuario
function validarCampos(tipoUsuario) {
    const camposComunes = ['email', 'contraseña', 'rol'];
    const camposPersona = ['nombre', 'apellido', 'dni', 'edad', 'telefono'];
    const camposEmpresa = ['nombreEmpresa', 'razonSocial', 'registroFiscal', 'direccion', 'telefonoEmpresa'];
    
    let camposAValidar = [...camposComunes];
    
    if (tipoUsuario === 'persona') {
        camposAValidar = [...camposAValidar, ...camposPersona];
    } else {
        camposAValidar = [...camposAValidar, ...camposEmpresa];
    }
    
    for (const campo of camposAValidar) {
        const elemento = document.getElementById(campo);
        if (!elemento.value.trim()) {
            alert(`Por favor, complete el campo ${campo.replace('_', ' ')}`);
            elemento.focus();
            return false;
        }
    }
    
    return true;
}

// Función para cambiar entre formularios
function toggleForm() {
    const tipoUsuario = document.getElementById('tipo_usuario').value;
    const personaForm = document.getElementById('persona_form');
    const empresaForm = document.getElementById('empresa_form');
    
    if (tipoUsuario === 'persona') {
        personaForm.style.display = 'block';
        empresaForm.style.display = 'none';
        // Habilitar/deshabilitar campos requeridos
        toggleRequired('persona_form', true);
        toggleRequired('empresa_form', false);
    } else {
        personaForm.style.display = 'none';
        empresaForm.style.display = 'block';
        // Habilitar/deshabilitar campos requeridos
        toggleRequired('persona_form', false);
        toggleRequired('empresa_form', true);
    }
}

// Función auxiliar para toggle de required
function toggleRequired(formId, required) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (required) {
            input.setAttribute('required', '');
        } else {
            input.removeAttribute('required');
        }
    });
}

// Inicializar el formulario
document.addEventListener('DOMContentLoaded', function() {
    toggleForm();
});

