<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Crear Reporte
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-badge{

            display: inline-block;

            padding: 8px 14px;

            border-radius: 12px;

            background: #fef3c7;

            color: #92400e;

            font-size: 13px;

            font-weight: 600;

        }

        .section-subtitle{

            margin-top: 30px;

            margin-bottom: 20px;

            font-size: 18px;

            color: #111827;

            font-weight: 700;

        }

        .upload-box{

            border: 2px dashed #d1d5db;

            border-radius: 18px;

            padding: 35px;

            text-align: center;

            background: #f9fafb;

            transition: .3s ease;

        }

        .upload-box:hover{

            border-color: #111827;

            background: #f3f4f6;

        }

        .upload-box p{

            margin-top: 12px;

            color: #6b7280;

            font-size: 14px;

        }

        .priority-box{

            display: flex;

            gap: 14px;

            flex-wrap: wrap;

        }

        .priority-card{

            flex: 1;

            min-width: 150px;

            padding: 18px;

            border-radius: 16px;

            border: 1px solid #e5e7eb;

            background: #f9fafb;

            cursor: pointer;

            transition: .3s ease;

        }

        .priority-card:hover{

            transform: translateY(-3px);

        }

        .priority-card h4{

            margin-bottom: 8px;

            font-size: 15px;

            color: #111827;

        }

        .priority-card p{

            font-size: 13px;

            color: #6b7280;

        }

        .priority-high{

            border-left: 5px solid #dc2626;

        }

        .priority-medium{

            border-left: 5px solid #f59e0b;

        }

        .priority-low{

            border-left: 5px solid #10b981;

        }

        .info-card{

            margin-top: 30px;

            background: #ffffff;

            border-radius: 22px;

            border: 1px solid #e5e7eb;

            padding: 24px;

            box-shadow: 0 10px 25px rgba(0,0,0,.05);

        }

        .info-card h3{

            margin-bottom: 20px;

            color: #111827;

        }

        .info-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));

            gap: 18px;

        }

        .info-item{

            background: #f9fafb;

            border-radius: 16px;

            padding: 18px;

            border: 1px solid #e5e7eb;

        }

        .info-item span{

            display: block;

            font-size: 13px;

            color: #6b7280;

            margin-bottom: 8px;

        }

        .info-item strong{

            font-size: 16px;

            color: #111827;

        }

        .details-box{

            display: flex;

            gap: 16px;

            flex-wrap: wrap;

            margin-top: 20px;

        }

        .detail-card{

            flex: 1;

            min-width: 200px;

            background: #f9fafb;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 18px;

        }

        .detail-card h4{

            margin-bottom: 10px;

            color: #111827;

            font-size: 15px;

        }

        .detail-card p{

            color: #6b7280;

            font-size: 14px;

            line-height: 1.6;

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
                    <span>Panel Cajero</span>

                </div>

            </div>

            <!-- NAV -->
            <nav class="sidebar-nav">

                <a href="Interface_Cobros.php">

                    <img 
                        src="../images/icons/Pago_Claro.png"
                        alt="Abonos"
                        class="menu-icon"
                    >

                    <span>Abonos</span>

                </a>

                <a href="Interface_Reportes_C.php" class="active">

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
                        Crear Nuevo Reporte
                    </h1>

                    <p class="subtitle">
                        Registra incidencias, problemas o solicitudes dentro del sistema.
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

            <!-- FORM -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="../images/icons/Reportes_Oscuro.png"
                            alt="Reporte"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Subir Evidencia
                        </button>

                        <div style="margin-top: 15px;">

                            <span class="status-badge">
                                Reporte Pendiente
                            </span>

                        </div>

                    </div>

                    <!-- FORM -->
                    <form class="edit-form">

                        <h3 class="section-subtitle">
                            Información General
                        </h3>

                        <div class="form-grid">

                            <!-- TITULO -->
                            <div class="input-group">

                                <label>
                                    Título del Reporte
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ejemplo: Fuga de agua"
                                >

                            </div>

                            <!-- TIPO -->
                            <div class="input-group">

                                <label>
                                    Tipo de Reporte
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona una opción
                                    </option>

                                    <option>
                                        Mantenimiento
                                    </option>

                                    <option>
                                        Seguridad
                                    </option>

                                    <option>
                                        Legal
                                    </option>

                                    <option>
                                        Limpieza
                                    </option>

                                </select>

                            </div>

                            <!-- UBICACION -->
                            <div class="input-group">

                                <label>
                                    Ubicación
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ejemplo: Local Comercial #12"
                                >

                            </div>

                            <!-- FECHA -->
                            <div class="input-group">

                                <label>
                                    Fecha del Reporte
                                </label>

                                <input type="date">

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Nivel de Prioridad
                        </h3>

                        <div class="priority-box">

                            <div class="priority-card priority-high">

                                <h4>
                                    Alta
                                </h4>

                                <p>
                                    Problemas urgentes o riesgos importantes.
                                </p>

                            </div>

                            <div class="priority-card priority-medium">

                                <h4>
                                    Media
                                </h4>

                                <p>
                                    Situaciones importantes pero no críticas.
                                </p>

                            </div>

                            <div class="priority-card priority-low">

                                <h4>
                                    Baja
                                </h4>

                                <p>
                                    Solicitudes menores o informativas.
                                </p>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Descripción del Problema
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Detalles del Reporte
                                </label>

                                <textarea 
                                    rows="6"
                                    placeholder="Describe el problema, situación o solicitud..."
                                ></textarea>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Evidencias
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Subir Fotografías o Archivos
                                </label>

                                <div class="upload-box">

                                    <img 
                                        src="../images/icons/Agregar.png"
                                        alt="Upload"
                                        width="55"
                                    >

                                    <p>
                                        Arrastra imágenes o documentos aquí
                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- RESUMEN -->
                        <div class="info-card">

                            <h3>
                                Resumen del Reporte
                            </h3>

                            <div class="info-grid">

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
                                        Prioridad
                                    </span>

                                    <strong>
                                        Alta
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Responsable
                                    </span>

                                    <strong>
                                        Área de Mantenimiento
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Fecha de Registro
                                    </span>

                                    <strong>
                                        09 Mayo 2026
                                    </strong>

                                </div>

                            </div>

                            <!-- DETALLES -->
                            <div class="details-box">

                                <div class="detail-card">

                                    <h4>
                                        Observaciones
                                    </h4>

                                    <p>
                                        El reporte será revisado por el administrador encargado.
                                    </p>

                                </div>

                                <div class="detail-card">

                                    <h4>
                                        Tiempo Estimado
                                    </h4>

                                    <p>
                                        Aproximadamente 24 a 48 horas para respuesta inicial.
                                    </p>

                                </div>

                                <div class="detail-card">

                                    <h4>
                                        Seguimiento
                                    </h4>

                                    <p>
                                        El estado del reporte podrá actualizarse desde el panel principal.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- BOTONES -->
                        <div class="form-buttons">

                            <button type="reset" class="btn-cancel">
                                Limpiar
                            </button>

                            <button type="submit" class="btn-save">
                                Registrar Reporte
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