<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar Abono</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-badge {

            display: inline-block;
            padding: 8px 14px;
            border-radius: 10px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 600;

        }

        .section-subtitle {

            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 18px;
            color: #374151;
            font-weight: 600;

        }

        .upload-box {

            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            background: #f8fafc;
            transition: 0.3s;

        }

        .upload-box:hover {

            border-color: #2563eb;
            background: #eff6ff;

        }

        .upload-box p {

            color: #64748b;
            margin-top: 10px;

        }

        .info-card {

            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            margin-top: 25px;

        }

        .info-card h4 {

            margin-bottom: 15px;
            color: #1e293b;

        }

        .info-grid {

            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;

        }

        .info-item {

            background: white;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;

        }

        .info-item span {

            display: block;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;

        }

        .info-item strong {

            color: #111827;
            font-size: 15px;

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
                        Registrar Abono
                    </h1>

                    <p class="subtitle">
                        Registra pagos parciales y solicitudes de autorización.
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

            <!-- FORM -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="../images/icons/Usuario.png"
                            alt="Abono"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Subir Comprobante
                        </button>

                        <div style="margin-top: 15px;">

                            <span class="status-badge">
                                Pendiente de Aprobación
                            </span>

                        </div>

                    </div>

                    <!-- FORMULARIO -->
                    <form class="edit-form">

                        <h3 class="section-subtitle">
                            Información del Inquilino
                        </h3>

                        <div class="form-grid">

                            <!-- NOMBRE -->
                            <div class="input-group">

                                <label>
                                    Nombre del Inquilino
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ejemplo: Carlos Mendoza"
                                >

                            </div>

                            <!-- PROPIEDAD -->
                            <div class="input-group">

                                <label>
                                    Propiedad
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona una propiedad
                                    </option>

                                    <option>
                                        Local Comercial #12
                                    </option>

                                    <option>
                                        Casa Residencial #4
                                    </option>

                                    <option>
                                        Edificio Central
                                    </option>

                                </select>

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

                            <!-- ESTATUS -->
                            <div class="input-group">

                                <label>
                                    Estatus del Cliente
                                </label>

                                <select>

                                    <option>
                                        Buen Historial
                                    </option>

                                    <option>
                                        Cliente Nuevo
                                    </option>

                                    <option>
                                        Historial Negativo
                                    </option>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Información del Abono
                        </h3>

                        <div class="form-grid">

                            <!-- MONTO -->
                            <div class="input-group">

                                <label>
                                    Monto del Abono
                                </label>

                                <input 
                                    type="number"
                                    placeholder="$0.00"
                                >

                            </div>

                            <!-- FECHA -->
                            <div class="input-group">

                                <label>
                                    Fecha del Pago
                                </label>

                                <input 
                                    type="date"
                                >

                            </div>

                            <!-- METODO -->
                            <div class="input-group">

                                <label>
                                    Método de Pago
                                </label>

                                <select>

                                    <option>
                                        Efectivo
                                    </option>

                                    <option>
                                        Transferencia
                                    </option>

                                    <option>
                                        Tarjeta
                                    </option>

                                </select>

                            </div>

                            <!-- AUTORIZACION -->
                            <div class="input-group">

                                <label>
                                    Requiere Autorización
                                </label>

                                <select>

                                    <option>
                                        Sí
                                    </option>

                                    <option>
                                        No
                                    </option>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Información Adicional
                        </h3>

                        <div class="form-grid">

                            <!-- OBSERVACIONES -->
                            <div class="input-group full-width">

                                <label>
                                    Observaciones
                                </label>

                                <textarea 
                                    rows="5"
                                    placeholder="Describe información adicional sobre el abono, acuerdos, autorizaciones o incidencias..."
                                ></textarea>

                            </div>

                            <!-- COMPROBANTE -->
                            <div class="input-group full-width">

                                <label>
                                    Subir Comprobante
                                </label>

                                <div class="upload-box">

                                    <img 
                                        src="../img/icons/upload.png"
                                        alt="Upload"
                                        width="50"
                                    >

                                    <p>
                                        Arrastra imágenes o comprobantes aquí
                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- RESUMEN -->
                        <div class="info-card">

                            <h4>
                                Resumen del Abono
                            </h4>

                            <div class="info-grid">

                                <div class="info-item">

                                    <span>
                                        Adeudo Actual
                                    </span>

                                    <strong>
                                        $18,000 MXN
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Último Pago
                                    </span>

                                    <strong>
                                        15 Abril 2026
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Estado
                                    </span>

                                    <strong>
                                        Pendiente
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Historial
                                    </span>

                                    <strong>
                                        3 Abonos Registrados
                                    </strong>

                                </div>

                            </div>

                        </div>

                        <!-- BOTONES -->
                        <div class="form-buttons">

                            <button type="reset" class="btn-cancel">
                                Limpiar
                            </button>

                            <button type="submit" class="btn-save">
                                Registrar Abono
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