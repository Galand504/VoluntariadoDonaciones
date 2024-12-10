<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/img/favicon-16x16.png" type="image/x-icon">
    <title>Registro de Usuario</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="d-grind gap-2">
                <a href="crud.php" class="btn btn-primary">Volver atras</a>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Registro de Usuario</h5>
                        
                        <!-- Selección de Tipo de Usuario -->
                        <div class="mb-3 text-center">
                            <label for="Tipo">Tipo de Usuario</label>
                            <select id="Tipo" class="form-select" onchange="toggleForm()">
                                <option value="Persona" selected>Persona</option>
                                <option value="Empresa">Empresa</option>
                            </select>
                        </div>
    
                        <!-- Formulario Dinámico -->
                        <form id="formulario">
                            <!-- Campos Específicos para Persona -->
                            <div id="form_persona" style="display: block;">
                                <h6>Datos Personales</h6>
                                <div class="mb-3">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="apellido">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="dni">DNI</label>
                                    <input type="text" id="dni" name="dni" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="edad">Edad</label>
                                    <input type="number" id="edad" name="edad" class="form-control" min="1" max="120" >
                                </div>
                                <div class="mb-3">
                                    <label for="telefono">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono" class="form-control" >
                                </div>
                            </div>
    
                            <!-- Campos Específicos para Empresa -->
                            <div id="form_empresa" style="display: none;">
                                <h6>Datos de Empresa</h6>
                                <div class="mb-3">
                                    <label for="nombreEmpresa">Nombre de la Empresa</label>
                                    <input type="text" id="nombreEmpresa" name="nombreEmpresa" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="razonSocial">Razón Social</label>
                                    <input type="text" id="razonSocial" name="razonSocial" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="registroFiscal">Registro Fiscal</label>
                                    <input type="text" id="registroFiscal" name="registroFiscal" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" id="direccion" name="direccion" class="form-control" >
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoEmpresa">Teléfono</label>
                                    <input type="tel" id="telefonoEmpresa" name="telefonoEmpresa" class="form-control" >
                                </div>
                            </div>
    
                            <!-- Campos Comunes -->
                            <h6>Datos de Usuario</h6>
                            <div class="mb-3">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="contraseña">Contraseña</label>
                                <input type="password" id="contraseña" name="contraseña" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="Rol">Rol</label>
                                <select id="Rol" name="Rol" class="form-select" required>
                                    <option value="">Seleccione un rol</option>
                                    <option value="Voluntario">Voluntario</option>
                                    <option value="Donante">Donador</option>
                                    <option value="Organizador">Organizador</option>
                                </select>
                            </div>
    
                            <!-- Botón de Envío -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </div>
                        </form>
    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/addusuario.js"></script>
</body>
</html>