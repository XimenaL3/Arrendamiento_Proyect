<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Arrendamientos</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <!-- OVERLAY -->
    <div class="overlay" id="overlay"></div>

    <div class="container">

        <!-- SIDEBAR -->
        <aside class="sidebar collapsed" id="sidebar">

            <!-- LOGO -->
            <div class="brand" id="brandToggle">

                <img 
                    src="../images/icons/Usuario.png"
                    alt="Logo"
                    class="brand-logo"
                >

                <div class="brand-text">

                    <h2>Sunlight Gardens</h2>
                    <span>Panel Administrativo</span>

                </div>

            </div>

            <!-- NAV -->
            <nav class="sidebar-nav">

                <a href="Interface_Trabajadores.php">

                    <img 
                        src="../images/icons/Trabajadores_Claro.png"
                        alt="Trabajadores"
                        class="menu-icon"
                    >

                    <span>Trabajadores</span>

                </a>

                <a href="Interface_Clientes.php">

                    <img 
                        src="../images/icons/Clientes_Claro.png"
                        alt="Clientes"
                        class="menu-icon"
                    >

                    <span>Clientes</span>

                </a>

                <a href="Interface_Visitas.php">

                    <img 
                        src="../images/icons/Visitas_Claro.png"
                        alt="Visitas"
                        class="menu-icon"
                    >

                    <span>Visitas</span>

                </a>

                <a href="Interface_Arrendamientos.php" class="active">

                    <img 
                        src="../images/icons/Arrendamiento_Oscuro.png"
                        alt="Arrendamiento"
                        class="menu-icon"
                    >

                    <span>Arrendamientos</span>

                </a>

                <a href="Interface_Abonos.php">

                    <img 
                        src="../images/icons/Pago_Claro.png"
                        alt="Abonos"
                        class="menu-icon"
                    >

                    <span>Abonos</span>

                </a>

                <a href="Interface_Productos_Limpieza.php">

                    <img 
                        src="../images/icons/Mantenimiento_Claro.png"
                        alt="Almacen Limpieza"
                        class="menu-icon"
                    >

                    <span>Almacén Limpieza</span>

                </a>

                <a href="Interface_Reportes.php">

                    <img 
                        src="../images/icons/Reportes_Claro.png"
                        alt="Reportes"
                        class="menu-icon"
                    >

                    <span>Reportes</span>

                </a>

            </nav>

            <!-- LOGOUT -->
            <div class="logout">

                <a href="#">

                    <img 
                        src="../images/icons/Cerrar_Claro.png"
                        alt="Cerrar sesión"
                        class="menu-icon"
                    >

                    <span>Cerrar Sesión</span>

                </a>

            </div>

        </aside>

        <!-- MAIN -->
        <main class="main-content">

            <!-- TOPBAR -->
            <header class="top-bar">

                <div>

                    <h1>
                        Gestión de Arrendamientos
                    </h1>

                    <p class="subtitle">
                        Control de locales, edificios, casas, pagos y mantenimiento.
                    </p>

                </div>

                <div class="user-profile">

                    <!-- NOTIFICACIONES -->
                    <div class="notification-wrapper">

                        <img 
                            src="../images/icons/Notificaciones.png"
                            alt="Notificaciones"
                            class="top-icon"
                        >

                        <div class="notification-badge">
                            5
                        </div>

                    </div>

                    <!-- USER -->
                    <div class="logged-user">

                        <img 
                            src="../images/icons/Usuario.png"
                            alt="Admin"
                            class="avatar-admin"
                        >

                        <div class="user-info">

                            <small>
                                En uso por
                            </small>

                            <strong>
                                Sarah Johnson
                            </strong>

                        </div>

                    </div>

                </div>

            </header>

            <!-- FILTROS -->
            <section class="search-section">

                <div class="filters">

                    <div class="filter-group">

                        <label>
                            Tipo de Propiedad
                        </label>

                        <select>

                            <option>
                                Todas
                            </option>

                            <option>
                                Local Comercial
                            </option>

                            <option>
                                Casa
                            </option>

                            <option>
                                Edificio
                            </option>

                            <option>
                                Bodega
                            </option>

                        </select>

                    </div>

                    <div class="filter-group">

                        <label>
                            Estado
                        </label>

                        <select>

                            <option>
                                Todos
                            </option>

                            <option>
                                Disponible
                            </option>

                            <option>
                                Ocupado
                            </option>

                            <option>
                                Mantenimiento
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar propiedad..."
                        >

                        <a 
                            href="Interface_Agregar_Arrendamientos.php"
                            class="btn-search"
                        >

                            <img 
                                src="../images/icons/Agregar.png"
                                alt="Agregar"
                                class="button-icon"
                            >

                        </a>

                    </div>

                </div>

            </section>

            <!-- ESTADISTICAS -->
            <section class="workers-section">

                <div class="workers-grid">

                    <!-- CARD -->
                    <div class="worker-card">

                        <div class="worker-title">

                            <h3>
                                Locales Disponibles
                            </h3>

                            <p>
                                12 espacios libres
                            </p>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="worker-card">

                        <div class="worker-title">

                            <h3>
                                En Mantenimiento
                            </h3>

                            <p>
                                4 propiedades
                            </p>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="worker-card">

                        <div class="worker-title">

                            <h3>
                                Aspectos Legales
                            </h3>

                            <p>
                                2 casos pendientes
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            <!-- LISTA -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Propiedades Registradas

                        <span class="badge">
                            24
                        </span>

                    </h2>

                </div>

                <div class="workers-grid">

                    <!-- LOCAL -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../img/local1.jpg"
                                    alt="Local"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Local Comercial #12
                                    </h3>

                                    <p>
                                        Disponible
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>
                                Tipo: Local Comercial
                            </p>

                            <p>
                                Servicios básicos incluidos
                            </p>

                            <p>
                                Depósito requerido: $12,000
                            </p>

                            <p>
                                Estatus: Excelente condición
                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Agregar_Renta.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Vender.png"
                                    alt="Vender"
                                    class="action-icon"
                                >

                            </a>

                            <a href="Interface_Editar_Arrendamientos.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>

                        </div>

                    </div>

                    <!-- EDIFICIO -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../img/edificio1.jpg"
                                    alt="Edificio"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Edificio Central
                                    </h3>

                                    <p>
                                        Ocupado
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>
                                Tipo: Edificio
                            </p>

                            <p>
                                Luz manejada por porcentajes
                            </p>

                            <p>
                                Tienda de pago en planta baja
                            </p>

                            <p>
                                3 habitaciones con consumo alto
                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Arrendamientos.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Vendido.png"
                                    alt="Vendido"
                                    class="action-icon"
                                >

                            </a>

                            <a href="Interface_Editar_Arrendamientos.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>

                        </div>

                    </div>

                    <!-- CASA -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../img/casa1.jpg"
                                    alt="Casa"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Casa Residencial #4
                                    </h3>

                                    <p>
                                        Mantenimiento
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>
                                Tipo: Casa
                            </p>

                            <p>
                                Reparación de tuberías
                            </p>

                            <p>
                                Evidencias registradas
                            </p>

                            <p>
                                Disponible en 2 semanas
                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Arrendamientos.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>

                        </div>

                    </div>

                    <!-- BODEGA -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../img/bodega.jpg"
                                    alt="Bodega"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Bodega Principal
                                    </h3>

                                    <p>
                                        Productos de Limpieza
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>
                                Cloro: 40 unidades
                            </p>

                            <p>
                                Desinfectante: 18 unidades
                            </p>

                            <p>
                                Escobas: 12 unidades
                            </p>

                            <p>
                                Estatus: Stock estable
                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Arrendamientos.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>

                        </div>

                    </div>

                    <!-- ABONOS -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../img/pagos.jpg"
                                    alt="Pagos"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Control de Abonos
                                    </h3>

                                    <p>
                                        Autorización requerida
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>
                                Abonos parciales habilitados
                            </p>

                            <p>
                                Requiere aprobación del admin
                            </p>

                            <p>
                                Historial de pagos activo
                            </p>

                            <p>
                                Clientes nuevos pagan más depósito
                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Arrendamientos.php" class="btn-action edit">

                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>

                        </div>

                    </div>

                </div>

            </section>

            <!-- FOOTER -->
            <footer class="footer">

                <p>
                    © 2026 DiamondsCorporation.
                    Todos los derechos reservados.
                </p>

            </footer>

            <!-- MODAL NOTIFICACIONES -->
            <div class="notifications-modal" id="notificationsModal">

                <div class="modal-header">

                    <h2>
                        Notificaciones
                    </h2>

                    <button class="close-modal" id="closeModal">
                        ✕
                    </button>

                </div>

                <div class="notification-list">

                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Nuevo reporte registrado
                            </h4>

                            <p>
                                Se registró un nuevo reporte pendiente.
                            </p>

                            <span>
                                Hace 5 minutos
                            </span>

                        </div>

                        <button class="btn-check">
                            ✓
                        </button>

                    </div>

                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Pago pendiente
                            </h4>

                            <p>
                                Existe un arrendamiento con retraso de pago.
                            </p>

                            <span>
                                Hace 20 minutos
                            </span>

                        </div>

                        <button class="btn-check">
                            ✓
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

    <!-- SCRIPT -->
    <script>

        const sidebar = document.getElementById('sidebar');

        const brandToggle = document.getElementById('brandToggle');

        const overlay = document.getElementById('overlay');

        const notificationWrapper = document.querySelector('.notification-wrapper');

        const notificationsModal = document.getElementById('notificationsModal');

        const closeModal = document.getElementById('closeModal');

        const checkButtons = document.querySelectorAll('.btn-check');

        function toggleSidebar() {

            sidebar.classList.toggle('collapsed');

            overlay.classList.toggle('active');

        }

        brandToggle.addEventListener('click', toggleSidebar);

        overlay.addEventListener('click', () => {

            overlay.classList.remove('active');

            sidebar.classList.remove('collapsed');

            notificationsModal.classList.remove('active');

        });

        /* ==============================
        MODAL NOTIFICACIONES
        ============================== */

        notificationWrapper.addEventListener('click', () => {

            notificationsModal.classList.add('active');

            overlay.classList.add('active');

        });

        closeModal.addEventListener('click', () => {

            notificationsModal.classList.remove('active');

            overlay.classList.remove('active');

        });

        /* ==============================
        MARCAR COMO VISTA
        ============================== */

        checkButtons.forEach(button => {

            button.addEventListener('click', () => {

                const notification = button.parentElement;

                notification.classList.add('completed');

                button.innerHTML = '✓';

            });

        });

    </script>

</body>

</html>