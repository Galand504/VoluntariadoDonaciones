<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/menuadmin.css">
    <title>Menu de Administrador</title>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="nav-links">
            <li><a href="Admin.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="crud.php"><i class="fas fa-database"></i> Gestor de usuarios</a></li>
            <li><a href="/src/Modulos/Proyectos/Vistas/proyectos.php"><i class="fas fa-project-diagram"></i> Gestor de Proyectos</a></li>
            <li><a href="/src/Modulos/Recompensas/Vistas/recompensas.php"><i class="fas fa-trophy"></i> Gestor de Recompensas</a></li>
            <li><a href="/src/Modulos/Donaciones/Vistas/pagos.php"><i class="fas fa-credit-card"></i> Gestor de Pagos</a></li>
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
                                <img src="/src/img/user.png" alt="Profile" class="rounded-circle" width="32">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="#">Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/src/Modulos/Usuarios/rutas/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container-fluid py-4">
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Personas</h5>
                                    <h2 class="mb-0">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Empresas</h5>
                                    <h2 class="mb-0">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-building fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Voluntariados</h5>
                                    <h2 class="mb-0">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-hands-helping fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Beneficencias</h5>
                                    <h2 class="mb-0">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-heart fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Activities -->
            <div class="row">
                <!-- Chart -->
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Gráfico de Registros Recientes</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Donadores Estrella</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush donors-list">
                                <!-- Los donadores se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>





