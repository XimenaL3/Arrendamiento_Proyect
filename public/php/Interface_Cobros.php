<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Panel de Cajero
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* =====================================
           CAJAS
        ===================================== */

        .cajas-grid {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 25px;

        }

        .caja-card {

            background: white;

            border-radius: 26px;

            padding: 25px;

            box-shadow: var(--shadow);

            transition: 0.3s ease;

        }

        .caja-card:hover {

            transform: translateY(-6px);

        }

        .caja-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;

        }

        .cliente {

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .cliente img {

            width: 68px;
            height: 68px;

            border-radius: 18px;

            object-fit: cover;

        }

        .cliente-info h3 {

            font-size: 18px;

            margin-bottom: 4px;

        }

        .cliente-info p {

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

            background: #fef3c7;

            color: #92400e;

        }

        .paid {

            background: #dcfce7;

            color: #166534;

        }

        .cancelled {

            background: #fee2e2;

            color: #991b1b;

        }

        /* =====================================
           INFO
        ===================================== */

        .caja-info {

            margin-bottom: 22px;

        }

        .caja-info p {

            margin-bottom: 14px;

            display: flex;

            align-items: center;

            gap: 12px;

            color: var(--text-muted);

            font-size: 14px;

        }

        .info-icon {

            width: 18px;
            height: 18px;

        }

        /* =====================================
           TOTAL
        ===================================== */

        .total-box {

            background: #f9fafb;

            border-radius: 18px;

            padding: 18px;

            margin-bottom: 22px;

            border: 1px solid var(--border);

        }

        .total-box h4 {

            font-size: 14px;

            color: var(--text-muted);

            margin-bottom: 8px;

        }

        .total-box h2 {

            font-size: 30px;

            color: black;

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

        .ventas-table {

            width: 100%;

            border-collapse: collapse;

            background: white;

            border-radius: 24px;

            overflow: hidden;

            box-shadow: var(--shadow);

        }

        .ventas-table th {

            background: black;

            color: white;

            text-align: left;

            padding: 18px;

            font-size: 14px;

        }

        .ventas-table td {

            padding: 18px;

            border-bottom: 1px solid var(--border);

            font-size: 14px;

            color: var(--text-muted);

        }

        .ventas-table tr:hover {

            background: #fafafa;

        }

        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 1200px) {

            .cajas-grid {

                grid-template-columns: repeat(2, 1fr);

            }

        }

        @media (max-width: 900px) {

            .cajas-grid {

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
                    <span>Panel Cajeros</span>

                </div>

            </div>

            <!-- NAV -->
            <nav class="sidebar-nav">

                <a href="Interface_Cobros.php" class="active">

                    <img 
                        src="../images/icons/Pago_Oscuro.png"
                        alt="Abonos"
                        class="menu-icon"
                    >

                    <span>Abonos</span>

                </a>

                <a href="Interface_Reportes_C.php">

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
                        Panel de Caja
                    </h1>

                    <p class="subtitle">
                        Gestiona pagos, cobros y movimientos de caja.
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
                            alt="Cajero"
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
                            Tipo de Pago
                        </label>

                        <select>

                            <option>
                                Todos
                            </option>

                            <option>
                                Efectivo
                            </option>

                            <option>
                                Tarjeta
                            </option>

                            <option>
                                Transferencia
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
                                Pendiente
                            </option>

                            <option>
                                Pagado
                            </option>

                            <option>
                                Cancelado
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar cliente..."
                        >

                        <a 
                            href="Interface_Agregar_Cobros.php"
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

            <!-- CAJAS -->
            <section>

                <div class="section-header">

                    <h2>

                        Cobros Recientes

                        <span class="badge">
                            12
                        </span>

                    </h2>

                </div>

                <div class="cajas-grid">

                    <!-- CARD -->
                    <div class="caja-card">

                        <div class="caja-header">

                            <div class="cliente">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Cliente"
                                >

                                <div class="cliente-info">

                                    <h3>
                                        Carlos Mendoza
                                    </h3>

                                    <p>
                                        Local Comercial #4
                                    </p>

                                </div>

                            </div>

                            <span class="status pending">
                                Pendiente
                            </span>

                        </div>

                        <div class="caja-info">

                            <p>

                                <img 
                                    src="../images/icons/Pago_Claro.png"
                                    alt=""
                                    class="info-icon"
                                >

                                Pago mensual pendiente

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt=""
                                    class="info-icon"
                                >

                                +52 418 222 1144

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Reportes_Claro.png"
                                    alt=""
                                    class="info-icon"
                                >

                                Fecha límite: 12 Mayo 2026

                            </p>

                        </div>

                        <div class="total-box">

                            <h4>
                                Total a pagar
                            </h4>

                            <h2>
                                $12,500
                            </h2>

                        </div>

                        <div class="card-actions">

                            <button class="btn-custom btn-primary">
                                Cobrar
                            </button>

                            <button class="btn-custom btn-secondary">
                                Ticket
                            </button>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="caja-card">

                        <div class="caja-header">

                            <div class="cliente">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Cliente"
                                >

                                <div class="cliente-info">

                                    <h3>
                                        Andrea López
                                    </h3>

                                    <p>
                                        Casa Residencial #8
                                    </p>

                                </div>

                            </div>

                            <span class="status paid">
                                Pagado
                            </span>

                        </div>

                        <div class="caja-info">

                            <p>

                                <img 
                                    src="../images/icons/Pago_Claro.png"
                                    alt=""
                                    class="info-icon"
                                >

                                Pago realizado correctamente

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt=""
                                    class="info-icon"
                                >

                                +52 477 112 8844

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Reportes_Claro.png"
                                    alt=""
                                    class="info-icon"
                                >

                                08 Mayo 2026

                            </p>

                        </div>

                        <div class="total-box">

                            <h4>
                                Total pagado
                            </h4>

                            <h2>
                                $9,800
                            </h2>

                        </div>

                        <div class="card-actions">

                            <button class="btn-custom btn-primary">
                                Ver Pago
                            </button>

                            <button class="btn-custom btn-secondary">
                                Recibo
                            </button>

                        </div>

                    </div>

                    <!-- CARD -->
                    <div class="caja-card">

                        <div class="caja-header">

                            <div class="cliente">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Cliente"
                                >

                                <div class="cliente-info">

                                    <h3>
                                        Miguel Torres
                                    </h3>

                                    <p>
                                        Edificio Central
                                    </p>

                                </div>

                            </div>

                            <span class="status cancelled">
                                Cancelado
                            </span>

                        </div>

                        <div class="caja-info">

                            <p>

                                <img 
                                    src="../images/icons/Pago_Claro.png"
                                    alt=""
                                    class="info-icon"
                                >

                                Pago cancelado por sistema

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt=""
                                    class="info-icon"
                                >

                                +52 442 118 9944

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Reportes_Claro.png"
                                    alt=""
                                    class="info-icon"
                                >

                                05 Mayo 2026

                            </p>

                        </div>

                        <div class="total-box">

                            <h4>
                                Monto cancelado
                            </h4>

                            <h2>
                                $4,500
                            </h2>

                        </div>

                        <div class="card-actions">

                            <button class="btn-custom btn-primary">
                                Revisar
                            </button>

                            <button class="btn-custom btn-secondary">
                                Detalles
                            </button>

                        </div>

                    </div>

                </div>

            </section>

            <!-- HISTORIAL -->
            <section class="history-section">

                <div class="section-header">

                    <h2>

                        Historial de Cobros

                        <span class="badge">
                            24
                        </span>

                    </h2>

                </div>

                <table class="ventas-table">

                    <thead>

                        <tr>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Propiedad
                            </th>

                            <th>
                                Monto
                            </th>

                            <th>
                                Método
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
                                Local Comercial #4
                            </td>

                            <td>
                                $12,500
                            </td>

                            <td>
                                Efectivo
                            </td>

                            <td>
                                10 Mayo 2026
                            </td>

                        </tr>

                        <tr>

                            <td>
                                Andrea López
                            </td>

                            <td>
                                Casa Residencial #8
                            </td>

                            <td>
                                $9,800
                            </td>

                            <td>
                                Tarjeta
                            </td>

                            <td>
                                08 Mayo 2026
                            </td>

                        </tr>

                        <tr>

                            <td>
                                Miguel Torres
                            </td>

                            <td>
                                Edificio Central
                            </td>

                            <td>
                                $4,500
                            </td>

                            <td>
                                Transferencia
                            </td>

                            <td>
                                05 Mayo 2026
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
                                Nuevo pago registrado
                            </h4>

                            <p>
                                Se registró un nuevo cobro exitosamente.
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
                                Cobro pendiente
                            </h4>

                            <p>
                                Existe un pago pendiente por validar.
                            </p>

                            <span>
                                Hace 15 minutos
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