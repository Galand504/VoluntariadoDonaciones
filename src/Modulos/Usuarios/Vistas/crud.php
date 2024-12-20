<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/crud.css">
    <title>Lista de Usuarios</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-auto p-0">
                <div class="sidebar">
                    <ul class="nav-links">
                        <li><a href="Admin.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="crud.php"><i class="fas fa-database"></i> Gestor de usuarios</a></li>
                        <li><a href="/src/Modulos/Proyectos/Vistas/proyectos.php"><i class="fas fa-project-diagram"></i> Gestor de Proyectos</a></li>
                        <li><a href="/src/Modulos/Recompensas/Vistas/recompensas.php"><i class="fas fa-trophy"></i> Gestor de Recompensas</a></li>
                        <li><a href="/src/Modulos/Donaciones/Vistas/pagos.php"><i class="fas fa-credit-card"></i> Gestor de Pagos</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col">
                <div class="row justify-content-center mt-5">
                    <div class="col-auto">
                        <a href="AddUsuario.php" class="btn btn-primary">Nuevo Usuario</a>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col">
                        <h3 class="text-center">Gestión de Usuarios</h3>

                        <!-- Tabla de personas -->
                        <div id="tabla-personas">
                            <h4>Personas</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Identificación</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Email</th>
                                            <th>Contraseña</th>
                                            <th>Edad</th>
                                            <th>Teléfono</th>
                                            <th>Rol</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-usuarios-personas">
                                        <!-- Aquí se llenarán las filas dinámicamente para personas -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tabla de empresas -->
                        <div id="tabla-empresas">
                            <h4>Empresas</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre Empresa</th>
                                            <th>Email</th>
                                            <th>Contraseña</th>
                                            <th>Razón Social</th>
                                            <th>Registro Fiscal</th>
                                            <th>Teléfono</th>
                                            <th>Dirección</th>
                                            <th>Rol</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-usuarios-empresas">
                                        <!-- Aquí se llenarán las filas dinámicamente para empresas -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para actualizar usuario -->
    <div class="modal fade" id="modalActualizarUsuario" tabindex="-1" aria-labelledby="modalActualizarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalActualizarUsuarioLabel">Actualizar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formActualizarUsuario">
                        <input type="hidden" id="updateUsuarioId">
                        <input type="hidden" id="updateUsuarioTipo">
                        <input type="hidden" id="updateUsuarioRol">
                        
                        <!-- Campo para email -->
                        <div class="mb-3">
                            <label for="updateEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="updateEmail" required>
                        </div>

                        <!-- Campo para contraseña -->
                        <div class="mb-3">
                            <label for="updateContraseña" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="updateContraseña">
                        </div>

                        <!-- Campos para personas -->
                        <div id="personaFields" style="display: none;">
                            <div class="mb-3">
                                <label for="updateDNI" class="form-label">DNI</label>
                                <input type="text" class="form-control" id="updateDNI">
                            </div>
                            <div class="mb-3">
                                <label for="updateNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="updateNombre">
                            </div>
                            <div class="mb-3">
                                <label for="updateApellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="updateApellido">
                            </div>
                            <div class="mb-3">
                                <label for="updateTelefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="updateTelefono">
                            </div>
                            <div class="mb-3">
                                <label for="updateEdad" class="form-label">Edad</label>
                                <input type="number" class="form-control" id="updateEdad">
                            </div>
                        </div>

                        <!-- Campos para empresas -->
                        <div id="empresaFields" style="display: none;">
                            <div class="mb-3">
                                <label for="updateNombreEmpresa" class="form-label">Nombre Empresa</label>
                                <input type="text" class="form-control" id="updateNombreEmpresa">
                            </div>
                            <div class="mb-3">
                                <label for="updateRazonSocial" class="form-label">Razón Social</label>
                                <input type="text" class="form-control" id="updateRazonSocial">
                            </div>
                            <div class="mb-3">
                                <label for="updateTelefonoEmpresa" class="form-label">Teléfono Empresa</label>
                                <input type="text" class="form-control" id="updateTelefonoEmpresa">
                            </div>
                            <div class="mb-3">
                                <label for="updateDireccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="updateDireccion">
                            </div>
                            <div class="mb-3">
                                <label for="updateRegistroFiscal" class="form-label">Registro Fiscal</label>
                                <input type="text" class="form-control" id="updateRegistroFiscal">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCambios">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/crud.js"></script>
</body>
</html>
