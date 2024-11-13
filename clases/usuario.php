<?php

use GuzzleHttp\Client;

class Usuario {
    private $client;

    public function __construct() {
        $this->client = new Client(['base_uri' => 'http://localhost/Crowdfunding/api/']); 
    }

    // Registro de un nuevo usuario (persona o empresa)
    public function registrar($nombre, $email, $contraseña, $tipoUsuario, $DNI = null, $apellido = null, $edad = null, $telefono = null, $direccion = null) {
        try {
            $data = [
                'nombre' => $nombre,
                'email' => $email,
                'contraseña' => $contraseña,
                'tipoUsuario' => $tipoUsuario,
            ];

            // Agregar campos específicos para tipo de usuario
            if ($tipoUsuario == 'persona') {
                $data['DNI'] = $DNI;
                $data['apellido'] = $apellido;
                $data['edad'] = $edad;
                $data['telefono'] = $telefono;
            } elseif ($tipoUsuario == 'empresa') {
                $data['direccion'] = $direccion;
                $data['telefono'] = $telefono;
            }

            // Enviar la solicitud POST
            $response = $this->client->post('api/usuarios/register', [
                'json' => $data
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return "Error en el registro: " . $e->getMessage();
        }
    }

    // Inicio de sesión
    public function login($email, $contraseña) {
        try {
            $response = $this->client->post('api/usuarios/login', [
                'json' => [
                    'email' => $email,
                    'contraseña' => $contraseña
                ]
            ]);
            $usuario = json_decode($response->getBody(), true);
            // Guardar el token o información de sesión si es necesario
            if (isset($usuario['token'])) {
                session_start();
                $_SESSION['token'] = $usuario['token'];
            }
            return $usuario;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return "Error en el inicio de sesión: " . $e->getMessage();
        }
    }

    // Obtener todos los usuarios
    public function obtenerUsuarios() {
        try {
            $response = $this->client->get('api/usuarios', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['token']
                ]
            ]);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return "Error al obtener usuarios: " . $e->getMessage();
        }
    }

    // Obtener un usuario por ID
    public function obtenerUsuario($id) {
        try {
            $response = $this->client->get("api/usuarios/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['token']
                ]
            ]);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return "Error al obtener el usuario: " . $e->getMessage();
        }
    }

    // Actualizar un usuario
    public function actualizarUsuario($id, $nombre, $email) {
        try {
            $response = $this->client->put("api/usuarios/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['token']
                ],
                'json' => [
                    'nombre' => $nombre,
                    'email' => $email
                ]
            ]);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return "Error al actualizar el usuario: " . $e->getMessage();
        }
    }

    // Eliminar un usuario
    public function eliminarUsuario($id) {
        try {
            $response = $this->client->delete("api/usuarios/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_SESSION['token']
                ]
            ]);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return "Error al eliminar el usuario: " . $e->getMessage();
        }
    }
}

?>


