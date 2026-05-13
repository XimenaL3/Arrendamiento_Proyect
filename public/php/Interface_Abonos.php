<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Control de Abonos
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* =====================================
           ABONOS
        ===================================== */

        .abonos-grid {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 25px;

        }

        .abono-card {

            background: white;

            border-radius: 26px;

            padding: 25px;

            box-shadow: var(--shadow);

            transition: 0.3s ease;

        }

        .abono-card:hover {

            transform: translateY(-6px);

        }

        .abono-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;

        }

        .tenant {

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .tenant img {

            width: 68px;
            height: 68px;

            border-radius: 18px;

            object-fit: cover;

        }

        .tenant-info h3 {

            font-size: 18px;

            margin-bottom: 4px;

        }

        .tenant-info p {

            color: var(--text-muted);

            font-size: 14px;

        }

        /* =====================================
           STATUS
        ===================================== */

        .status {

            padding: 8px 14px;

            border-radius: 30px;

            font-size: 12px;

            font-weight: 700;

        }

        .pending {

            background: #f3f4f6;

            color: #4b5563;

        }

        .approved {

            background: #dcfce7;

            color: #166534;

        }

        .rejected {

            background: #fee2e2;

            color: #991b1b;

        }

        /* =====================================
           INFO
        ===================================== */

        .abono-info {

            margin-bottom: 22px;

        }

        .abono-info p {

            margin-bottom: 14px;

            display: flex;

            align-items: center;

            gap: 12px;

            color: var(--text-muted);

            font-size: 14px;

        }

        /* =====================================
           PROGRESS
        ===================================== */

        .progress-box {

            margin-bottom: 22px;

        }

        .progress-top {

            display: flex;

            justify-content: space-between;

            margin-bottom: 10px;

            font-size: 13px;

            color: var(--text-muted);

        }

        .progress-bar {

            width: 100%;

            height: 10px;

            background: #ececec;

            border-radius: 30px;

            overflow: hidden;

        }

        .progress {

            height: 100%;

            border-radius: 30px;

            background: linear-gradient(
                90deg,
                #111111,
                #444444
            );

        }

        /* =====================================
           BUTTONS
        ===================================== */

        .card-actions {

            display: flex;

            gap: 12px;

        }

        .btn-custom {

            flex: 1;

            height: 46px;

            border: none;

            border-radius: 14px;

            cursor: pointer;

            font-size: 14px;

            font-weight: 600;

            transition: 0.3s ease;

        }

        .btn-primary {

            background: black;

            color: white;

        }

        .btn-primary:hover {

            background: #1f1f1f;

        }

        .btn-secondary {

            background: #f3f4f6;

            color: black;

        }

        .btn-secondary:hover {

            background: #e5e7eb;

        }

        /* =====================================
           TABLE
        ===================================== */

        .history-table {

            width: 100%;

            border-collapse: collapse;

            background: white;

            border-radius: 24px;

            overflow: hidden;

            box-shadow: var(--shadow);

        }

        .history-table th {

            background: black;

            color: white;

            text-align: left;

            padding: 18px;

            font-size: 14px;

        }

        .history-table td {

            padding: 18px;

            border-bottom: 1px solid var(--border);

            font-size: 14px;

            color: var(--text-muted);

        }

        .history-table tr:hover {

            background: #fafafa;

        }

        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 1200px) {

            .abonos-grid {

                grid-template-columns: repeat(2, 1fr);

            }

        }

        @media (max-width: 900px) {

            .abonos-grid {

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

                <a href="Interface_Abonos.php" class="active">

                    <img 
                        src="../images/icons/Pago_Oscuro.png"
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
                        Control de Abonos
                    </h1>

                    <p class="subtitle">
                        Gestiona pagos parciales y revisa el historial de abonos.
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
                            6
                        </div>

                    </div>

                    <!-- USER -->
                    <div class="logged-user">

                        <img 
                            src="../images/icons/Usuario.png"
                            alt="Administrador"
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
                            Tipo de propiedad
                        </label>

                        <select>

                            <option>
                                Todas
                            </option>

                            <option>
                                Casas
                            </option>

                            <option>
                                Locales
                            </option>

                            <option>
                                Edificios
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
                                Pendientes
                            </option>

                            <option>
                                Aprobados
                            </option>

                            <option>
                                Rechazados
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar propiedad..."
                        >

                        <a 
                            href="Interface_Agregar_Abonos.php"
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

            <!-- ABONOS -->
            <section>

                <div class="section-header">

                    <h2>

                        Solicitudes de Abono

                        <span class="badge">
                            18
                        </span>

                    </h2>

                </div>

                <div class="abonos-grid">

                    <!-- CARD -->
                    <div class="abono-card">

                        <div class="abono-header">

                            <div class="tenant">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Usuario"
                                >

                                <div class="tenant-info">

                                    <h3>
                                        Carlos Mendoza
                                    </h3>

                                    <p>
                                        Edificio Central
                                    </p>

                                </div>

                            </div>

                            <span class="status pending">
                                Pendiente
                            </span>

                        </div>

                        <div class="abono-info">

                            <p>
                                Abono solicitado: $3,500
                            </p>

                            <p>
                                Renta total: $12,000
                            </p>

                            <p>
                                Fecha límite: 10 Mayo 2026
                            </p>

                        </div>

                        <div class="progress-box">

                            <div class="progress-top">

                                <span>
                                    Pago completado
                                </span>

                                <span>
                                    45%
                                </span>

                            </div>

                            <div class="progress-bar">

                                <div class="progress" style="width: 45%;"></div>

                            </div>

                        </div>

                        <div class="card-actions">

                            <button class="btn-custom btn-primary">
                                Aprobar
                            </button>

                            <button class="btn-custom btn-secondary">
                                Historial
                            </button>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="abono-card">

                        <div class="abono-header">

                            <div class="tenant">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Usuario"
                                >

                                <div class="tenant-info">

                                    <h3>
                                        Andrea López
                                    </h3>

                                    <p>
                                        Casa Residencial #4
                                    </p>

                                </div>

                            </div>

                            <span class="status approved">
                                Aprobado
                            </span>

                        </div>

                        <div class="abono-info">

                            <p>
                                Abono realizado: $6,000
                            </p>

                            <p>
                                Renta total: $9,500
                            </p>

                            <p>
                                Autorizado por administración
                            </p>

                        </div>

                        <div class="progress-box">

                            <div class="progress-top">

                                <span>
                                    Pago completado
                                </span>

                                <span>
                                    75%
                                </span>

                            </div>

                            <div class="progress-bar">

                                <div class="progress" style="width: 75%;"></div>

                            </div>

                        </div>

                        <div class="card-actions">

                            <button class="btn-custom btn-primary">
                                Ver Pago
                            </button>

                            <button class="btn-custom btn-secondary">
                                Historial
                            </button>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="abono-card">

                        <div class="abono-header">

                            <div class="tenant">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Usuario"
                                >

                                <div class="tenant-info">

                                    <h3>
                                        Miguel Torres
                                    </h3>

                                    <p>
                                        Local Comercial #7
                                    </p>

                                </div>

                            </div>

                            <span class="status rejected">
                                Rechazado
                            </span>

                        </div>

                        <div class="abono-info">

                            <p>
                                Solicitud rechazada
                            </p>

                            <p>
                                3 meses de adeudo
                            </p>

                            <p>
                                Historial negativo activo
                            </p>

                        </div>

                        <div class="progress-box">

                            <div class="progress-top">

                                <span>
                                    Pago completado
                                </span>

                                <span>
                                    20%
                                </span>

                            </div>

                            <div class="progress-bar">

                                <div class="progress" style="width: 20%;"></div>

                            </div>

                        </div>

                        <div class="card-actions">

                            <button class="btn-custom btn-primary">
                                Revisar
                            </button>

                            <button class="btn-custom btn-secondary">
                                Historial
                            </button>

                        </div>

                    </div>

                </div>

            </section>

            <!-- HISTORIAL -->
            <section class="history-section">

                <div class="section-header">

                    <h2>

                        Historial de Abonos

                        <span class="badge">
                            18
                        </span>

                    </h2>

                </div>

                <table class="history-table">

                    <thead>

                        <tr>

                            <th>
                                Inquilino
                            </th>

                            <th>
                                Propiedad
                            </th>

                            <th>
                                Cantidad
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Fecha
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr>

                            <td>
                                Carlos Mendoza
                            </td>

                            <td>
                                Edificio Central
                            </td>

                            <td>
                                $3,500
                            </td>

                            <td>
                                Pendiente
                            </td>

                            <td>
                                08 Mayo 2026
                            </td>

                        </tr>

                        <tr>

                            <td>
                                Andrea López
                            </td>

                            <td>
                                Casa Residencial #4
                            </td>

                            <td>
                                $6,000
                            </td>

                            <td>
                                Aprobado
                            </td>

                            <td>
                                05 Mayo 2026
                            </td>

                        </tr>

                        <tr>

                            <td>
                                Miguel Torres
                            </td>

                            <td>
                                Local Comercial #7
                            </td>

                            <td>
                                $2,000
                            </td>

                            <td>
                                Rechazado
                            </td>

                            <td>
                                01 Mayo 2026
                            </td>

                        </tr>

                    </tbody>

                </table>

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

    /* =========================
       SIDEBAR
    ========================= */

    function toggleSidebar() {

        sidebar.classList.toggle('collapsed');

        overlay.classList.toggle('active');

    }

    brandToggle.addEventListener('click', toggleSidebar);

    /* =========================
       MODAL NOTIFICACIONES
    ========================= */

    notificationWrapper.addEventListener('click', () => {

        notificationsModal.classList.add('active');

        overlay.classList.add('active');

    });

    closeModal.addEventListener('click', () => {

        notificationsModal.classList.remove('active');

        overlay.classList.remove('active');

    });

    overlay.addEventListener('click', () => {

        notificationsModal.classList.remove('active');

        overlay.classList.remove('active');

    });

    /* =========================
       MARCAR COMO VISTA
    ========================= */

    checkButtons.forEach(button => {

        button.addEventListener('click', () => {

            const notification = button.parentElement;

            notification.classList.add('completed');

        });

    });

    </script>

</body>

</html>