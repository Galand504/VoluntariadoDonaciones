document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const contraseña = document.getElementById('contraseña').value;

    const loginData = {
        email: email,
        contraseña: contraseña
    };

    try {
        const response = await fetch('http://localhost/Crowdfunding/public/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(loginData)
        });

        const data = await response.json();
        console.log('Respuesta del servidor:', data);

        if (data.status === 200) {
            await Promise.all([
                localStorage.setItem('jwt_token', data.token),
                localStorage.setItem('user_rol', data.user.rol)
            ]);

            await new Promise(resolve => setTimeout(resolve, 100));

            const rol = data.user.rol;
            if (rol === 'Administrador') {
                window.location.replace('../html/dashboard.html');
            } else if (['Donante', 'Voluntario', 'Organizador'].includes(rol)) {
                window.location.replace('../html/dashboard-user.html');
            } else {
                console.error('Rol no reconocido:', rol);
                alert('Error: Rol de usuario no definido');
            }
        } else {
            alert(data.message || 'Error en el inicio de sesión');
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
        alert('Hubo un problema al intentar iniciar sesión. Inténtalo nuevamente.');
    }
});

