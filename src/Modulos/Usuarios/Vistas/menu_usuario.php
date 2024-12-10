<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/menuusuario.css">
    <title>Mi Dashboard</title>
</head>
<body>
    <!-- Alerta de Recompensa -->
    <div class="reward-alert" id="rewardAlert">
        <div class="alert-overlay"></div>
        <div class="alert alert-success alert-custom text-center p-4">
            <i class="fas fa-star icon-success"></i>
            <h2 class="mb-3">¡Felicidades!</h2>
            <p class="mb-4">Has ganado una recompensa por tu valiosa donación.</p>
            <p class="mb-4">Tu contribución hace la diferencia.</p>
            <button class="btn btn-success px-4" onclick="closeRewardAlert()">
                Aceptar
            </button>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-details">
            <h1 class="logo-name">Logo</h1>
        </div>
        <ul class="nav-links">
            <li><a href="menu_usuario.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="/src/Modulos/Recompensas/Vistas/recompensas.php"><i class="fas fa-trophy"></i> Recompensas</a></li>
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#activityModal">
                <i class="fas fa-plus-circle"></i> Registrar Actividad</a></li>
            <li><a href="/src/Modulos/Voluntariado/Vistas/voluntariados.php"><i class="fas fa-hands-helping"></i> Voluntariados</a></li>
            <li><a href="/src/Modulos/Donaciones/Vistas/donaciones.php"><i class="fas fa-gift"></i> Donaciones</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <form class="d-flex me-auto">
                        <input class="form-control me-2" type="search" placeholder="Buscar...">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-bs-toggle="dropdown">
                                <img src="/img/user.png" alt="Profile" class="rounded-circle" width="32">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="#">Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Mis Actividades Organizadas -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Mis Actividades Organizadas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Actividad</th>
                                            <th>Estado</th>
                                            <th>Participantes</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="misActividadesList">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actividades en las que Participo -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Actividades en las que Participo</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Actividad</th>
                                            <th>Organizador</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="participacionesList">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro de Actividad -->
    <div class="modal fade" id="activityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Actividad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegistrarActividad">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Actividad</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Actividad</label>
                            <select class="form-select" required>
                                <option value="">Selecciona el tipo de actividad</option>
                                <option value="donacion">Donación</option>
                                <option value="voluntariado">Voluntariado</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/user.js"></script>
</body>
</html> 