<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Gestión de Reportes
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

        <style>

            /* =========================================
            SOLO CSS EXTRA PARA REPORTES
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

            .date-input::-webkit-calendar-picker-indicator{

                cursor: pointer;

                opacity: 0.7;

                transition: 0.3s ease;

            }

            .date-input::-webkit-calendar-picker-indicator:hover{

                opacity: 1;

            }

            /* =========================================
            REPORTS GRID
            ========================================= */

            .reports-grid{

                display: grid;

                grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));

                gap: 25px;

            }

            /* =========================================
            REPORT CARD
            ========================================= */

            .report-card{

                background: white;

                border-radius: 26px;

                padding: 25px;

                box-shadow: var(--shadow);

                transition: 0.3s ease;

            }

            .report-card:hover{

                transform: translateY(-6px);

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

                width: 68px;

                height: 68px;

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

                background: #e5e7eb;

                color: #111827;

            }

            .cancelled{

                background: #f3f4f6;

                color: #6b7280;

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

                margin-bottom: 12px;

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

                color: var(--text);

            }

            /* =========================================
            ACTIONS
            ========================================= */

            .report-actions{

                display: flex;

                gap: 12px;

                margin-top: 24px;

            }

            .btn-report{

                flex: 1;

                border: none;

                padding: 13px;

                border-radius: 14px;

                cursor: pointer;

                font-weight: 600;

                transition: 0.3s ease;

            }

            .btn-download{

                background: black;

                color: white;

            }

            .btn-download:hover{

                background: #000000;

            }

            .btn-view{

                background: #f3f3f3;

                color: black;

            }

            .btn-view:hover{

                background: #e5e7eb;

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

                <a href="Interface_Reportes.php" class="active">

                    <img 
                        src="../images/icons/Reportes_Oscuro.png"
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
                        Gestión de Reportes
                    </h1>

                    <p class="subtitle">
                        Consulta, filtra y descarga reportes administrativos.
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
                            placeholder="Buscar Reporte..."
                        >

                        <a 
                            href="Interface_Agregar_Reporte.php"
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

                <div class="stats-grid">

                    <div class="stat-card">

                        <h3>
                            Reportes Pendientes
                        </h3>

                        <strong>
                            12
                        </strong>

                    </div>

                    <div class="stat-card">

                        <h3>
                            Reportes Atendidos
                        </h3>

                        <strong>
                            38
                        </strong>

                    </div>

                    <div class="stat-card">

                        <h3>
                            Reportes Cancelados
                        </h3>

                        <strong>
                            5
                        </strong>

                    </div>

                    <div class="stat-card">

                        <h3>
                            Reportes Totales
                        </h3>

                        <strong>
                            55
                        </strong>

                    </div>

                </div>

            </section>

            <!-- REPORTES -->
            <section class="workers-section">

                <div class="reports-grid">

                    <!-- REPORTE -->
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
                                Se reportó una fuga de agua en el área del baño principal del local.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha
                                </span>

                                <strong>
                                    08 Mayo 2026
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
                                Ver Reporte
                            </button>

                            <button class="btn-report btn-download">
                                Descargar
                            </button>

                        </div>

                    </div>

                    <!-- REPORTE -->
                    <div class="report-card">

                        <div class="report-header">

                            <div class="report-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Reporte"
                                >

                                <div>

                                    <h3>
                                        Reparación Eléctrica
                                    </h3>

                                    <p>
                                        Casa Residencial #4
                                    </p>

                                </div>

                            </div>

                            <div class="status completed">
                                Atendido
                            </div>

                        </div>

                        <div class="report-info">

                            <p>
                                El problema eléctrico fue solucionado y validado por mantenimiento.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha
                                </span>

                                <strong>
                                    05 Mayo 2026
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
                                Ver Reporte
                            </button>

                            <button class="btn-report btn-download">
                                Descargar
                            </button>

                        </div>

                    </div>

                    <!-- REPORTE -->
                    <div class="report-card">

                        <div class="report-header">

                            <div class="report-user">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Reporte"
                                >

                                <div>

                                    <h3>
                                        Problema Legal
                                    </h3>

                                    <p>
                                        Edificio Central
                                    </p>

                                </div>

                            </div>

                            <div class="status cancelled">
                                Cancelado
                            </div>

                        </div>

                        <div class="report-info">

                            <p>
                                El caso fue cerrado después de llegar a un acuerdo con el inquilino.
                            </p>

                        </div>

                        <div class="report-details">

                            <div class="detail-box">

                                <span>
                                    Fecha
                                </span>

                                <strong>
                                    02 Mayo 2026
                                </strong>

                            </div>

                            <div class="detail-box">

                                <span>
                                    Prioridad
                                </span>

                                <strong>
                                    Baja
                                </strong>

                            </div>

                        </div>

                        <div class="report-actions">

                            <button class="btn-report btn-view">
                                Ver Reporte
                            </button>

                            <button class="btn-report btn-download">
                                Descargar
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