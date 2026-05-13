<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Visitas</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* =========================
           VISITAS
        ========================= */

        .visit-grid {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 25px;

        }

        .visit-card {

            background: white;

            border-radius: 26px;

            padding: 25px;

            box-shadow: var(--shadow);

            transition: 0.3s ease;

        }

        .visit-card:hover {

            transform: translateY(-6px);

        }

        .visit-header {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 15px;

            margin-bottom: 20px;

        }

        .visit-user {

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .visit-user img {

            width: 68px;
            height: 68px;

            border-radius: 18px;

            object-fit: cover;

        }

        .visit-user h3 {

            font-size: 18px;

            margin-bottom: 4px;

            color: var(--text);

        }

        .visit-user p {

            color: var(--text-muted);

            font-size: 14px;

        }

        .visit-info {

            margin-bottom: 22px;

            color: var(--text-muted);

            font-size: 14px;

        }

        .visit-info p {

            margin-bottom: 14px;

            display: flex;

            align-items: center;

            gap: 12px;

        }

        .visit-info img {

            width: 18px;
            height: 18px;

        }

        /* =========================
           ESTATUS
        ========================= */

        .status {

            padding: 7px 14px;

            border-radius: 12px;

            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

        }

        .pending {

            background: #fef3c7;

            color: #92400e;

        }

        .progress {

            background: #dbeafe;

            color: #1d4ed8;

        }

        .completed {

            background: #dcfce7;

            color: #166534;

        }

        .cancelled {

            background: #fee2e2;

            color: #991b1b;

        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1200px) {

            .visit-grid {

                grid-template-columns: repeat(2, 1fr);

            }

        }

        @media (max-width: 900px) {

            .visit-grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

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
                        Gestión de Visitas
                    </h1>

                    <p class="subtitle">
                        Administra las visitas agendadas y su estado actual.
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
                            4
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

                    <!-- FILTRO ESTATUS -->
                    <div class="filter-group">

                        <label>
                            Estatus
                        </label>

                        <select>

                            <option>
                                Todos los estatus
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

                    <!-- FILTRO PERSONA -->
                    <div class="filter-group">

                        <label>
                            Persona que Visita
                        </label>

                        <select>

                            <option>
                                Todas las personas
                            </option>

                            <option>
                                Alejandro Ruiz
                            </option>

                            <option>
                                María González
                            </option>

                            <option>
                                Fernanda López
                            </option>

                            <option>
                                Daniel Herrera
                            </option>

                        </select>

                    </div>

                    <!-- BUSCADOR -->
                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar cliente..."
                        >

                        <a 
                            href="Interface_Agregar_Visita.php"
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

            <!-- VISITAS -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Visitas Agendadas

                        <span class="badge">
                            12
                        </span>

                    </h2>

                </div>

                <div class="visit-grid">

                    <!-- CARD -->
                    <div class="visit-card">

                        <div class="visit-header">

                            <div class="visit-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Visitante"
                                >

                                <div>

                                    <h3>
                                        Alejandro Ruiz
                                    </h3>

                                    <p>
                                        Cliente: Carlos Mendoza
                                    </p>

                                </div>

                            </div>

                            <span class="status pending">
                                Pendiente
                            </span>

                        </div>

                        <div class="visit-info">

                            <p>

                                <img 
                                    src="../images/icons/Correo.png"
                                    alt=""
                                >

                                alejandro@gmail.com

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt=""
                                >

                                +52 418 223 9981

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Reportes_Claro.png"
                                    alt=""
                                >

                                10 Mayo 2026 - 4:00 PM

                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Visita.php" class="btn-action edit">

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

                    <!-- CARD -->
                    <div class="visit-card">

                        <div class="visit-header">

                            <div class="visit-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Visitante"
                                >

                                <div>

                                    <h3>
                                        María González
                                    </h3>

                                    <p>
                                        Cliente: Roberto Sánchez
                                    </p>

                                </div>

                            </div>

                            <span class="status progress">
                                En Atención
                            </span>

                        </div>

                        <div class="visit-info">

                            <p>

                                <img 
                                    src="../images/icons/Correo.png"
                                    alt=""
                                >

                                maria@gmail.com

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt=""
                                >

                                +52 477 881 0099

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Reportes_Claro.png"
                                    alt=""
                                >

                                11 Mayo 2026 - 1:30 PM

                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Visita.php" class="btn-action edit">

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

                    <!-- CARD -->
                    <div class="visit-card">

                        <div class="visit-header">

                            <div class="visit-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Visitante"
                                >

                                <div>

                                    <h3>
                                        Fernanda López
                                    </h3>

                                    <p>
                                        Cliente: Jorge Ramírez
                                    </p>

                                </div>

                            </div>

                            <span class="status completed">
                                Atendida
                            </span>

                        </div>

                        <div class="visit-info">

                            <p>

                                <img 
                                    src="../images/icons/Correo.png"
                                    alt=""
                                >

                                fernanda@gmail.com

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt=""
                                >

                                +52 442 123 4455

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Reportes_Claro.png"
                                    alt=""
                                >

                                08 Mayo 2026 - 11:00 AM

                            </p>

                        </div>

                        <div class="card-footer">

                            <a href="Interface_Editar_Visita.php" class="btn-action edit">

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
                                Nueva visita registrada
                            </h4>

                            <p>
                                Alejandro Ruiz agendó una visita para el Local #12.
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
                                Visita cancelada
                            </h4>

                            <p>
                                Daniel Herrera canceló su visita programada.
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

        /* ==============================
        SIDEBAR
        ============================== */

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