    document.getElementById('loginForm').addEventListener('submit', async function(event) {
        event.preventDefault();  // Prevenir que el formulario se envíe de la manera tradicional

        // Obtener los valores del formulario
        const email = document.getElementById('email').value;
        const contraseña = document.getElementById('contraseña').value;

        const loginData = {
            email: email,
            contraseña: contraseña
        };

        try {
            // Realizar la solicitud POST a la API de login
            const response = await fetch('http://localhost/Crowdfunding/public/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(loginData)
            });

            // Convertir la respuesta a JSON
            const data = await response.json();

            // Verificar si el login fue exitoso
            if (data.status === 'success') {
                // Guardar el JWT en el almacenamiento local si es necesario
                localStorage.setItem('jwt', data.jwt);

                // Redirigir al usuario según el rol (ajusta esta lógica según el rol)
                if (data.rol === 'Administrador') {
                    window.location.href = '../html/dashboard.html';  // Redirigir al dashboard de administrador
                } else if (data.rol === 'usuario') {
                    window.location.href = '/dashboard-user';  // Redirigir al dashboard de usuario
                } else {
                    console.error('Rol no reconocido');
                }
            } else {
                // Si hay un error, mostrar el mensaje al usuario
                alert(data.message);
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            alert('Hubo un problema al intentar iniciar sesión. Inténtalo nuevamente.');
        }
    });

