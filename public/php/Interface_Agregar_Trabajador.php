<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar Trabajador</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

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

                <a href="Interface_Trabajadores.php" class="active">

                    <img 
                        src="../images/icons/Trabajadores_Oscuro.png"
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
                        Agregar Trabajador
                    </h1>

                    <p class="subtitle">
                        Registra un nuevo empleado dentro del sistema.
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
                            1
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

            <!-- ADD SECTION -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="../images/icons/Usuario.png"
                            alt="Nuevo trabajador"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Subir Foto
                        </button>

                    </div>

                    <!-- FORM -->
                    <form class="edit-form">

                        <div class="form-grid">

                            <!-- NOMBRE -->
                            <div class="input-group">

                                <label>
                                    Nombre Completo
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ingresa el nombre completo"
                                >

                            </div>

                            <!-- CORREO -->
                            <div class="input-group">

                                <label>
                                    Correo Electrónico
                                </label>

                                <input 
                                    type="email"
                                    placeholder="correo@empresa.com"
                                >

                            </div>

                            <!-- TELEFONO -->
                            <div class="input-group">

                                <label>
                                    Número Telefónico
                                </label>

                                <input 
                                    type="text"
                                    placeholder="+52 000 000 0000"
                                >

                            </div>

                            <!-- ROL -->
                            <div class="input-group">

                                <label>
                                    Rol
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona un rol
                                    </option>

                                    <option>
                                        Administrador
                                    </option>

                                    <option>
                                        Supervisor
                                    </option>

                                    <option>
                                        Cajero
                                    </option>

                                    <option>
                                        Mantenimiento
                                    </option>

                                </select>

                            </div>

                            <!-- DIRECCION -->
                            <div class="input-group full-width">

                                <label>
                                    Dirección
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ingresa la dirección del trabajador"
                                >

                            </div>

                            <!-- DESCRIPCION -->
                            <div class="input-group full-width">

                                <label>
                                    Descripción
                                </label>

                                <textarea 
                                    rows="5"
                                    placeholder="Describe información adicional sobre el trabajador..."
                                ></textarea>

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="form-buttons">

                            <button type="reset" class="btn-cancel">
                                Limpiar
                            </button>

                            <button type="submit" class="btn-save">
                                Agregar Trabajador
                            </button>

                        </div>

                    </form>

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