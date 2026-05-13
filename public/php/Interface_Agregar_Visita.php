<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar Visita</title>

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

                <a href="Interface_Visitas.php" class="active">

                    <img 
                        src="../images/icons/Visitas_Oscuro.png"
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
                        Agregar Nueva Visita
                    </h1>

                    <p class="subtitle">
                        Registra una nueva visita dentro del sistema.
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

            <!-- FORM SECTION -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="../images/icons/Usuario.png"
                            alt="Visitante"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Subir Foto
                        </button>

                    </div>

                    <!-- FORM -->
                    <form class="edit-form">

                        <div class="form-grid">

                            <!-- NOMBRE VISITANTE -->
                            <div class="input-group">

                                <label>
                                    Nombre del Visitante
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ejemplo: Alejandro Ruiz"
                                >

                            </div>

                            <!-- TELEFONO -->
                            <div class="input-group">

                                <label>
                                    Número Telefónico
                                </label>

                                <input 
                                    type="text"
                                    placeholder="+52 418 000 0000"
                                >

                            </div>

                            <!-- CORREO -->
                            <div class="input-group">

                                <label>
                                    Correo Electrónico
                                </label>

                                <input 
                                    type="email"
                                    placeholder="correo@ejemplo.com"
                                >

                            </div>

                            <!-- CLIENTE -->
                            <div class="input-group">

                                <label>
                                    Cliente Relacionado
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona un cliente
                                    </option>

                                    <option>
                                        Carlos Mendoza
                                    </option>

                                    <option>
                                        Roberto Sánchez
                                    </option>

                                    <option>
                                        Jorge Ramírez
                                    </option>

                                    <option>
                                        Laura Torres
                                    </option>

                                </select>

                            </div>

                            <!-- FECHA -->
                            <div class="input-group">

                                <label>
                                    Fecha de la Visita
                                </label>

                                <input 
                                    type="date"
                                >

                            </div>

                            <!-- HORA -->
                            <div class="input-group">

                                <label>
                                    Hora de la Visita
                                </label>

                                <input 
                                    type="time"
                                >

                            </div>

                            <!-- ESTATUS -->
                            <div class="input-group">

                                <label>
                                    Estado de la Visita
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona un estado
                                    </option>

                                    <option>
                                        Pendiente
                                    </option>

                                    <option>
                                        En Atención
                                    </option>

                                    <option>
                                        Atendida
                                    </option>

                                    <option>
                                        Cancelada
                                    </option>

                                </select>

                            </div>

                            <!-- IDENTIFICACION -->
                            <div class="input-group">

                                <label>
                                    Tipo de Identificación
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona una opción
                                    </option>

                                    <option>
                                        INE
                                    </option>

                                    <option>
                                        Pasaporte
                                    </option>

                                    <option>
                                        Licencia de Conducir
                                    </option>

                                </select>

                            </div>

                            <!-- MOTIVO -->
                            <div class="input-group full-width">

                                <label>
                                    Motivo de la Visita
                                </label>

                                <textarea 
                                    rows="4"
                                    placeholder="Describe el motivo de la visita..."
                                ></textarea>

                            </div>

                            <!-- OBSERVACIONES -->
                            <div class="input-group full-width">

                                <label>
                                    Observaciones
                                </label>

                                <textarea 
                                    rows="5"
                                    placeholder="Agrega observaciones o información importante..."
                                ></textarea>

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="form-buttons">

                            <button type="reset" class="btn-cancel">
                                Limpiar
                            </button>

                            <button type="submit" class="btn-save">
                                Registrar Visita
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

                    <!-- NOTIFICACION -->
                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Nueva visita agendada
                            </h4>

                            <p>
                                Se registró una nueva visita para el cliente Carlos Mendoza.
                            </p>

                            <span>
                                Hace 5 minutos
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
                                Visita pendiente
                            </h4>

                            <p>
                                Existe una visita pendiente de confirmación.
                            </p>

                            <span>
                                Hace 18 minutos
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