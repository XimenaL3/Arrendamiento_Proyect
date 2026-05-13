<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Clientes</title>

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

                <a href="Interface_Clientes.php" class="active">

                    <img 
                        src="../images/icons/Clientes_Oscuro.png"
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

                <a href="Interface_Arrendamientos.php">

                    <img 
                        src="../images/icons/Arrendamiento_Claro.png"
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
                        Gestión de Clientes
                    </h1>

                    <p class="subtitle">
                        Administra clientes, contratos y estados de renta.
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
                            3
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

            <!-- SEARCH -->
            <section class="search-section">

                <div class="filters">

                    <div class="filter-group">

                        <label>
                            Tipo de Cliente
                        </label>

                        <select>

                            <option>
                                Todos
                            </option>

                            <option>
                                Comercial
                            </option>

                            <option>
                                Residencial
                            </option>

                        </select>

                    </div>

                    <div class="filter-group">

                        <label>
                            Estado
                        </label>

                        <select>

                            <option>
                                Activos
                            </option>

                            <option>
                                Pendientes
                            </option>

                            <option>
                                Inactivos
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar cliente..."
                        >

                        <a 
                            href="Interface_Agregar_Cliente.php"
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

            <!-- CLIENTES -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Clientes Registrados

                        <span class="badge">
                            18
                        </span>

                    </h2>

                </div>

                <div class="workers-grid">

                    <!-- CLIENTE -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Cliente"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Carlos Mendoza
                                    </h3>

                                    <p>
                                        Cliente Comercial
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>

                                <img 
                                    src="../images/icons/Correo.png"
                                    alt="Correo"
                                    class="info-icon"
                                >

                                carlos@gmail.com

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt="Teléfono"
                                    class="info-icon"
                                >

                                +52 418 120 4455

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Arrendamiento_Claro.png"
                                    alt="Propiedad"
                                    class="info-icon"
                                >

                                Local Comercial #12

                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Cliente.php" class="btn-action edit">
                                    
                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>
                            
                            <button class="btn-action delete">

                                <img 
                                    src="../images/icons/Eliminar.png"
                                    alt="Eliminar"
                                    class="action-icon"
                                >

                            </button>

                        </div>

                    </div>

                    <!-- CLIENTE -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Cliente"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Fernanda López
                                    </h3>

                                    <p>
                                        Cliente Residencial
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>

                                <img 
                                    src="../images/icons/Correo.png"
                                    alt="Correo"
                                    class="info-icon"
                                >

                                fernanda@gmail.com

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt="Teléfono"
                                    class="info-icon"
                                >

                                +52 477 224 8833

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Arrendamiento_Claro.png"
                                    alt="Propiedad"
                                    class="info-icon"
                                >

                                Casa Residencial #4

                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Cliente.php" class="btn-action edit">
                                    
                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>
                            
                            <button class="btn-action delete">

                                <img 
                                    src="../images/icons/Eliminar.png"
                                    alt="Eliminar"
                                    class="action-icon"
                                >

                            </button>

                        </div>

                    </div>

                    <!-- CLIENTE -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Cliente"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Alejandro Ruiz
                                    </h3>

                                    <p>
                                        Cliente Empresarial
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-body">

                            <p>

                                <img 
                                    src="../images/icons/Correo.png"
                                    alt="Correo"
                                    class="info-icon"
                                >

                                alejandro@gmail.com

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt="Teléfono"
                                    class="info-icon"
                                >

                                +52 442 991 3344

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Arrendamiento_Claro.png"
                                    alt="Propiedad"
                                    class="info-icon"
                                >

                                Edificio Central

                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Cliente.php" class="btn-action edit">
                                    
                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>
                            
                            <button class="btn-action delete">

                                <img 
                                    src="../images/icons/Eliminar.png"
                                    alt="Eliminar"
                                    class="action-icon"
                                >

                            </button>

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

                    <!-- NOTIFICACION -->
                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Nuevo cliente registrado
                            </h4>

                            <p>
                                Se registró un nuevo cliente comercial.
                            </p>

                            <span>
                                Hace 10 minutos
                            </span>

                        </div>

                        <button class="btn-check">
                            ✓
                        </button>

                    </div>

                    <!-- NOTIFICACION -->
                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Pago recibido
                            </h4>

                            <p>
                                El cliente Carlos Mendoza realizó su pago mensual.
                            </p>

                            <span>
                                Hace 30 minutos
                            </span>

                        </div>

                        <button class="btn-check">
                            ✓
                        </button>

                    </div>

                    <!-- NOTIFICACION -->
                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Contrato pendiente
                            </h4>

                            <p>
                                Falta firma de contrato para Casa Residencial #4.
                            </p>

                            <span>
                                Hace 1 hora
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