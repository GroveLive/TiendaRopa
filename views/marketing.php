<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Marketing - Tienda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/marketing.css">
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
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="user-details">
                    <h3>Laura Fernández</h3>
                    <p>Marketing</p>
                    <span class="status-badge status-active">Activo</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</li>
                <li data-tab="campanas"><i class="fas fa-ad"></i> Campañas</li>
                <li data-tab="promociones"><i class="fas fa-tags"></i> Promociones</li>
                <li data-tab="analiticas"><i class="fas fa-chart-bar"></i> Analíticas</li>
                <li class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</li>
            </ul>
        </aside>

        <!-- Contenido -->
        <main class="content">
            <!-- Dashboard -->
            <div id="dashboard" class="tab-content active">
                <div class="section-header">
                    <h2>Dashboard</h2>
                    <p class="items-count">Bienvenida, Laura</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Campañas Activas</h3>
                        <p>4</p>
                    </div>
                    <div class="stat-card">
                        <h3>Ventas Promocionales</h3>
                        <p>€2,300</p>
                    </div>
                    <div class="stat-card">
                        <h3>Alcance Total</h3>
                        <p>15,000</p>
                    </div>
                </div>
            </div>

            <!-- Campañas -->
            <div id="campanas" class="tab-content">
                <div class="section-header">
                    <h2>Campañas</h2>
                    <p class="items-count">Total: 4</p>
                </div>
                <div class="campaigns-list">
                    <div class="campaign-card">
                        <div class="campaign-header">
                            <span class="campaign-name">Campaña Primavera</span>
                            <span class="campaign-date">01/03/2025 - 31/03/2025</span>
                        </div>
                        <p>Alcance: 5,000 usuarios</p>
                        <div class="campaign-footer">
                            <span class="campaign-status">Activa</span>
                            <div class="campaign-actions">
                                <button class="btn btn-primary btn-sm">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promociones -->
            <div id="promociones" class="tab-content">
                <div class="section-header">
                    <h2>Promociones</h2>
                    <p class="items-count">Total: 3</p>
                </div>
                <div class="promotions-list">
                    <div class="promotion-card">
                        <div class="promotion-header">
                            <span class="promotion-name">Descuento 20%</span>
                            <span class="promotion-date">25/03/2025 - 01/04/2025</span>
                        </div>
                        <p>Ventas: €800</p>
                        <div class="promotion-footer">
                            <span class="promotion-status">Activa</span>
                            <div class="promotion-actions">
                                <button class="btn btn-primary btn-sm">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analíticas -->
            <div id="analiticas" class="tab-content">
                <div class="section-header">
                    <h2>Analíticas</h2>
                    <p class="items-count">Este Mes</p>
                </div>
                <div class="analytics-stats">
                    <div class="stat-card">
                        <h3>Ventas Totales</h3>
                        <p>€10,000</p>
                    </div>
                    <div class="stat-card">
                        <h3>Conversión</h3>
                        <p>3.5%</p>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 70%;"></div>
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

    <script src="../../assets/js/marketing.js"></script>
</body>
</html>