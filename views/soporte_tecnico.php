<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Soporte Técnico - Tienda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/soporte_tecnico.css">
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
                    <i class="fas fa-tools"></i>
                </div>
                <div class="user-details">
                    <h3>Ana Martínez</h3>
                    <p>Soporte Técnico</p>
                    <span class="status-badge status-active">Activo</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</li>
                <li data-tab="incidencias"><i class="fas fa-exclamation-triangle"></i> Incidencias</li>
                <li data-tab="equipos"><i class="fas fa-laptop"></i> Equipos</li>
                <li data-tab="mantenimiento"><i class="fas fa-wrench"></i> Mantenimiento</li>
                <li class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</li>
            </ul>
        </aside>

        <!-- Contenido -->
        <main class="content">
            <!-- Dashboard -->
            <div id="dashboard" class="tab-content active">
                <div class="section-header">
                    <h2>Dashboard</h2>
                    <p class="items-count">Bienvenida, Ana</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Incidencias Abiertas</h3>
                        <p>3</p>
                    </div>
                    <div class="stat-card">
                        <h3>Equipos Activos</h3>
                        <p>25</p>
                    </div>
                    <div class="stat-card">
                        <h3>Mantenimientos Hoy</h3>
                        <p>2</p>
                    </div>
                </div>
            </div>

            <!-- Incidencias -->
            <div id="incidencias" class="tab-content">
                <div class="section-header">
                    <h2>Incidencias</h2>
                    <p class="items-count">Total: 3</p>
                </div>
                <div class="issues-list">
                    <div class="issue-card">
                        <div class="issue-header">
                            <span class="issue-number">Incidencia #901</span>
                            <span class="issue-date">25/03/2025</span>
                        </div>
                        <p>Error en el sistema de inventario</p>
                        <div class="issue-footer">
                            <span class="issue-status pendiente">Pendiente</span>
                            <div class="issue-actions">
                                <button class="btn btn-primary btn-sm">Resolver</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipos -->
            <div id="equipos" class="tab-content">
                <div class="section-header">
                    <h2>Equipos</h2>
                    <p class="items-count">Total: 25</p>
                </div>
                <table class="equipment-table">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>Estado</th>
                            <th>Última Revisión</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Servidor Principal</td>
                            <td>Activo</td>
                            <td>20/03/2025</td>
                            <td><button class="btn btn-primary btn-sm">Revisar</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mantenimiento -->
            <div id="mantenimiento" class="tab-content">
                <div class="section-header">
                    <h2>Mantenimiento</h2>
                    <p class="items-count">Hoy: 2</p>
                </div>
                <div class="maintenance-list">
                    <div class="maintenance-card">
                        <div class="maintenance-header">
                            <span class="maintenance-number">Mantenimiento #234</span>
                            <span class="maintenance-date">25/03/2025</span>
                        </div>
                        <p>Revisión de servidores</p>
                        <div class="maintenance-footer">
                            <span class="maintenance-status">Pendiente</span>
                            <div class="maintenance-actions">
                                <button class="btn btn-primary btn-sm">Completar</button>
                            </div>
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

    <script src="../../assets/js/soporte_tecnico.js"></script>
</body>
</html>