<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gerente - Tienda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/gerente.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <i class="fas fa-shopping-cart"></i>
                <h1>Tienda</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="?controller=home">Inicio</a></li>
                    <li><a href="?controller=logout">Cerrar Sesión</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <a href="#" class="icon-link"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="main-content container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="user-details">
                    <h3>Pedro Sánchez</h3>
                    <p>Gerente</p>
                    <span class="status-badge status-active">Activo</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</li>
                <li data-tab="equipos"><i class="fas fa-users"></i> Equipos</li>
                <li data-tab="reportes"><i class="fas fa-file-alt"></i> Reportes</li>
                <li data-tab="metas"><i class="fas fa-bullseye"></i> Metas</li>
                <li class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</li>
            </ul>
        </aside>

        <!-- Contenido -->
        <main class="content">
            <!-- Dashboard -->
            <div id="dashboard" class="tab-content active">
                <div class="section-header">
                    <h2>Dashboard</h2>
                    <p class="items-count">Bienvenido, Pedro</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Ventas Totales</h3>
                        <p>€25,000</p>
                    </div>
                    <div class="stat-card">
                        <h3>Pedidos Pendientes</h3>
                        <p>45</p>
                    </div>
                    <div class="stat-card">
                        <h3>Inventario Bajo</h3>
                        <p>10 Productos</p>
                    </div>
                    <div class="stat-card">
                        <h3>Incidencias Abiertas</h3>
                        <p>3</p>
                    </div>
                </div>
            </div>

            <!-- Equipos -->
            <div id="equipos" class="tab-content">
                <div class="section-header">
                    <h2>Equipos</h2>
                    <p class="items-count">Total: 20 Empleados</p>
                </div>
                <table class="teams-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Rendimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Pérez</td>
                            <td>Vendedor</td>
                            <td>85%</td>
                            <td><button class="btn btn-primary btn-sm">Ver Detalles</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Reportes -->
            <div id="reportes" class="tab-content">
                <div class="section-header">
                    <h2>Reportes</h2>
                    <p class="items-count">Últimos 30 días</p>
                </div>
                <div class="reports-grid">
                    <div class="report-card">
                        <h3>Ventas Mensuales</h3>
                        <p>€25,000</p>
                        <button class="btn btn-primary btn-sm">Descargar</button>
                    </div>
                    <div class="report-card">
                        <h3>Inventario</h3>
                        <p>1,500 Productos</p>
                        <button class="btn btn-primary btn-sm">Descargar</button>
                    </div>
                    <div class="report-card">
                        <h3>Incidencias</h3>
                        <p>5 Resueltas</p>
                        <button class="btn btn-primary btn-sm">Descargar</button>
                    </div>
                </div>
            </div>

            <!-- Metas -->
            <div id="metas" class="tab-content">
                <div class="section-header">
                    <h2>Metas</h2>
                    <p class="items-count">Este Mes</p>
                </div>
                <div class="goals-list">
                    <div class="goal-card">
                        <div class="goal-header">
                            <span class="goal-name">Ventas</span>
                            <span class="goal-progress">€25,000 / €30,000</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 83%;"></div>
                        </div>
                        <div class="goal-footer">
                            <button class="btn btn-primary btn-sm">Editar Meta</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Tienda</h3>
                    <p>© 2025 Tienda. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="../assets/js/gerente.js"></script>
</body>
</html>