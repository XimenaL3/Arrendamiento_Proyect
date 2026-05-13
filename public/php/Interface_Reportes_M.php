<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Panel de Mantenimiento
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* =========================================
        EXTRA CSS REPORTES MANTENIMIENTO
        ========================================= */

        .date-wrapper{

            position: relative;

        }

        .date-input{

            min-width: 220px;

            height: 52px;

            padding: 0 16px;

            border-radius: 14px;

            border: 1px solid var(--border);

            background: #fafafa;

            color: var(--text);

            font-size: 14px;

            outline: none;

            transition: 0.3s ease;

            cursor: pointer;

        }

        .date-input:focus{

            border-color: black;

            background: white;

        }

        /* =========================================
        STATS
        ========================================= */

        .stats-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));

            gap: 20px;

            margin-bottom: 35px;

        }

        .stat-card{

            background: white;

            border-radius: 24px;

            padding: 24px;

            box-shadow: var(--shadow);

        }

        .stat-card h3{

            color: var(--text-muted);

            font-size: 14px;

            margin-bottom: 10px;

        }

        .stat-card strong{

            font-size: 30px;

            color: var(--text);

        }

        /* =========================================
        REPORTES GRID
        ========================================= */

        .reports-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));

            gap: 25px;

        }

        /* =========================================
        CARD
        ========================================= */

        .report-card{

            background: white;

            border-radius: 26px;

            padding: 25px;

            box-shadow: var(--shadow);

            transition: .3s ease;

        }

        .report-card:hover{

            transform: translateY(-5px);

        }

        .report-header{

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            margin-bottom: 20px;

        }

        .report-user{

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .report-user img{

            width: 70px;

            height: 70px;

            border-radius: 18px;

            object-fit: cover;

        }

        .report-user h3{

            font-size: 18px;

            margin-bottom: 5px;

        }

        .report-user p{

            color: var(--text-muted);

            font-size: 14px;

        }

        /* =========================================
        STATUS
        ========================================= */

        .status{

            padding: 8px 14px;

            border-radius: 30px;

            font-size: 12px;

            font-weight: 700;

        }

        .pending{

            background: #fef3c7;

            color: #92400e;

        }

        .completed{

            background: #dcfce7;

            color: #166534;

        }

        .cancelled{

            background: #fee2e2;

            color: #991b1b;

        }

        /* =========================================
        INFO
        ========================================= */

        .report-info{

            margin-bottom: 20px;

        }

        .report-info p{

            color: var(--text-muted);

            line-height: 1.6;

        }

        /* =========================================
        DETAILS
        ========================================= */

        .report-details{

            display: grid;

            grid-template-columns: repeat(2,1fr);

            gap: 15px;

            margin-top: 20px;

        }

        .detail-box{

            background: #f9fafb;

            border-radius: 16px;

            padding: 14px;

            border: 1px solid var(--border);

        }

        .detail-box span{

            display: block;

            font-size: 12px;

            color: var(--text-muted);

            margin-bottom: 6px;

        }

        .detail-box strong{

            font-size: 15px;

        }

        /* =========================================
        BUTTONS
        ========================================= */

        .report-actions{

            display: flex;

            gap: 10px;

            margin-top: 24px;

            flex-wrap: wrap;

        }

        .btn-report{

            flex: 1;

            border: none;

            padding: 13px;

            border-radius: 14px;

            cursor: pointer;

            font-weight: 600;

            transition: .3s ease;

        }

        .btn-view{

            background: #f3f4f6;

            color: black;

        }

        .btn-view:hover{

            background: #e5e7eb;

        }

        .btn-attend{

            background: black;

            color: white;

        }

        .btn-attend:hover{

            background: #1f1f1f;

        }

        .btn-cancel{

            background: #ef4444;

            color: white;

        }

        .btn-cancel:hover{

            background: #dc2626;

        }

        .btn-history{

            background: #2563eb;

            color: white;

        }

        .btn-history:hover{

            background: #1d4ed8;

        }

        /* =========================================
        SECTION
        ========================================= */

        .section-title-maintenance{

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 25px;

        }

        .section-title-maintenance h2{

            font-size: 28px;

        }

        .report-counter{

            background: black;

            color: white;

            padding: 6px 12px;

            border-radius: 12px;

            font-size: 13px;

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

                    <span>Panel Mantenimiento</span>

                </div>

            </div>

            <!-- NAV -->
            <nav class="sidebar-nav">

                <a href="Interface_Reportes_M.php" class="active">

                    <img 
                        src="../images/icons/Reportes_Oscuro.png"
                        alt="Reportes"
                        class="menu-icon"
                    >

                    <span>Reportes</span>

                </a>

                <a href="Interface_Productos_Limpieza_M.php">

                    <img 
                        src="../images/icons/Mantenimiento_Claro.png"
                        alt="Productos"
                        class="menu-icon"
                    >

                    <span>Productos Limpieza</span>

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
                        Gestión de Reportes
                    </h1>

                    <p class="subtitle">
                        Administra, atiende y da seguimiento a los reportes de mantenimiento.
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
                            alt="Usuario"
                            class="avatar-admin"
                        >

                        <div class="user-info">

                            <small>
                                En uso por
                            </small>

                            <strong>
                                Carlos Mendoza
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
                                Atendidos
                            </option>

                            <option>
                                Cancelados
                            </option>

                        </select>

                    </div>

                    <div class="filter-group">

                        <label>
                            Prioridad
                        </label>

                        <select>

                            <option>
                                Todas
                            </option>

                            <option>
                                Alta
                            </option>

                            <option>
                                Media
                            </option>

                            <option>
                                Baja
                            </option>

                        </select>

                    </div>

                    <div class="filter-group">

                        <label>
                            Fecha
                        </label>

                        <div class="date-wrapper">

                            <input 
                                type="date"
                                class="date-input"
                            >

                        </div>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar reporte..."
                        >

                        <button class="btn-search">

                            <img 
                                src="../images/icons/Buscar.png"
                                alt="Buscar"
                                class="button-icon"
                            >

                        </button>

                    </div>

                </div>

            </section>

            <!-- ESTADISTICAS -->
            <section class="workers-section">

                <div class="stats-grid">

                    <div class="stat-card">

                        <h3>
                            Reportes Pendientes
                        </h3>

                        <strong>
                            18
                        </strong>

                    </div>

                    <div class="stat-card">

                        <h3>
                            Reportes Atendidos
                        </h3>

                        <strong>
                            42
                        </strong>

                    </div>

                    <div class="stat-card">

                        <h3>
                            Reportes Cancelados
                        </h3>

                        <strong>
                            6
                        </strong>

                    </div>

                    <div class="stat-card">

                        <h3>
                            Mis Reportes Atendidos
                        </h3>

                        <strong>
                            27
                        </strong>

                    </div>

                </div>

            </section>

            <!-- REPORTES -->
            <section class="workers-section">

                <div class="section-title-maintenance">

                    <h2>
                        Reportes Activos
                    </h2>

                    <span class="report-counter">
                        18 pendientes
                    </span>

                </div>

                <div class="reports-grid">

                    <!-- CARD -->
                    <div class="report-card">

                        <div class="report-header">

                            <div class="report-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Reporte"
                                >

                                <div>

                                    <h3>
                                        Fuga de Agua
                                    </h3>

                                    <p>
                                        Local Comercial #12
                                    </p>

                                </div>

                            </div>

                            <div class="status pending">
                                Pendiente
                            </div>

                        </div>

                        <div class="report-info">

                            <p>
                                Se detectó una fuga en el baño principal. El agua comienza a salir hacia el pasillo.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha
                                </span>

                                <strong>
                                    10 Mayo 2026
                                </strong>

                            </div>

                            <div class="detail-box">

                                <span>
                                    Prioridad
                                </span>

                                <strong>
                                    Alta
                                </strong>

                            </div>

                        </div>

                        <div class="report-actions">

                            <button class="btn-report btn-view">
                                Ver
                            </button>

                            <button class="btn-report btn-attend">
                                Atender
                            </button>

                            <button class="btn-report btn-cancel">
                                Cancelar
                            </button>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="report-card">

                        <div class="report-header">

                            <div class="report-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Reporte"
                                >

                                <div>

                                    <h3>
                                        Corto Eléctrico
                                    </h3>

                                    <p>
                                        Torre Residencial B
                                    </p>

                                </div>

                            </div>

                            <div class="status pending">
                                Pendiente
                            </div>

                        </div>

                        <div class="report-info">

                            <p>
                                Las luces del segundo piso presentan fallas y apagones constantes.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha
                                </span>

                                <strong>
                                    09 Mayo 2026
                                </strong>

                            </div>

                            <div class="detail-box">

                                <span>
                                    Prioridad
                                </span>

                                <strong>
                                    Media
                                </strong>

                            </div>

                        </div>

                        <div class="report-actions">

                            <button class="btn-report btn-view">
                                Ver
                            </button>

                            <button class="btn-report btn-attend">
                                Atender
                            </button>

                            <button class="btn-report btn-cancel">
                                Cancelar
                            </button>

                        </div>

                    </div>

                </div>

            </section>

            <!-- REPORTES ATENDIDOS -->
            <section class="workers-section" style="margin-top: 45px;">

                <div class="section-title-maintenance">

                    <h2>
                        Reportes Atendidos por Mí
                    </h2>

                    <span class="report-counter">
                        27 completados
                    </span>

                </div>

                <div class="reports-grid">

                    <!-- CARD -->
                    <div class="report-card">

                        <div class="report-header">

                            <div class="report-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Reporte"
                                >

                                <div>

                                    <h3>
                                        Reparación de Puerta
                                    </h3>

                                    <p>
                                        Entrada Principal
                                    </p>

                                </div>

                            </div>

                            <div class="status completed">
                                Atendido
                            </div>

                        </div>

                        <div class="report-info">

                            <p>
                                Se realizó el cambio de bisagras y ajuste completo de la puerta principal.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha de atención
                                </span>

                                <strong>
                                    06 Mayo 2026
                                </strong>

                            </div>

                            <div class="detail-box">

                                <span>
                                    Técnico
                                </span>

                                <strong>
                                    Carlos Mendoza
                                </strong>

                            </div>

                        </div>

                        <div class="report-actions">

                            <button class="btn-report btn-history">
                                Ver Historial
                            </button>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="report-card">

                        <div class="report-header">

                            <div class="report-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Reporte"
                                >

                                <div>

                                    <h3>
                                        Mantenimiento de Tuberías
                                    </h3>

                                    <p>
                                        Área de Jardines
                                    </p>

                                </div>

                            </div>

                            <div class="status completed">
                                Atendido
                            </div>

                        </div>

                        <div class="report-info">

                            <p>
                                Se reemplazó la tubería dañada y se realizaron pruebas de presión.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha de atención
                                </span>

                                <strong>
                                    03 Mayo 2026
                                </strong>

                            </div>

                            <div class="detail-box">

                                <span>
                                    Técnico
                                </span>

                                <strong>
                                    Carlos Mendoza
                                </strong>

                            </div>

                        </div>

                        <div class="report-actions">

                            <button class="btn-report btn-history">
                                Ver Historial
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

                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Nuevo reporte asignado
                            </h4>

                            <p>
                                Se asignó un nuevo reporte de fuga de agua.
                            </p>

                            <span>
                                Hace 4 minutos
                            </span>

                        </div>

                        <button class="btn-check">
                            ✓
                        </button>

                    </div>

                    <div class="notification-item">

                        <div class="notification-info">

                            <h4>
                                Reporte cancelado
                            </h4>

                            <p>
                                Un administrador canceló un reporte pendiente.
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