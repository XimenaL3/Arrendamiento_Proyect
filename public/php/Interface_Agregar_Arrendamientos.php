<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar Arrendamiento</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        /* CAMPOS EXTRA */
        .status-badge {

            display: inline-block;
            padding: 8px 14px;
            border-radius: 10px;
            background: #dcfce7;
            color: #166534;
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

            border-color: #3b82f6;
            background: #eff6ff;

        }

        .upload-box p {

            color: #64748b;
            margin-top: 10px;

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

                <a href="Interface_Arrendamientos.php" class="active">

                    <img 
                        src="../images/icons/Arrendamiento_Oscuro.png"
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
                        Agregar Arrendamiento
                    </h1>

                    <p class="subtitle">
                        Registra nuevos locales comerciales, casas o edificios.
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
                            src="../img/default-building.jpg"
                            alt="Nuevo arrendamiento"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Subir Imagen
                        </button>

                        <div style="margin-top: 15px;">

                            <span class="status-badge">
                                Nuevo Registro
                            </span>

                        </div>

                    </div>

                    <!-- FORMULARIO -->
                    <form class="edit-form">

                        <h3 class="section-subtitle">
                            Información General
                        </h3>

                        <div class="form-grid">

                            <!-- NOMBRE -->
                            <div class="input-group">

                                <label>
                                    Nombre del Arrendamiento
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Ejemplo: Local Comercial #12"
                                >

                            </div>

                            <!-- TIPO -->
                            <div class="input-group">

                                <label>
                                    Tipo de Propiedad
                                </label>

                                <select>

                                    <option selected disabled>
                                        Selecciona una opción
                                    </option>

                                    <option>
                                        Local Comercial
                                    </option>

                                    <option>
                                        Casa
                                    </option>

                                    <option>
                                        Edificio
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
                                    placeholder="Ingresa la dirección completa"
                                >

                            </div>

                            <!-- ESTADO -->
                            <div class="input-group">

                                <label>
                                    Estado
                                </label>

                                <select>

                                    <option>
                                        Disponible
                                    </option>

                                    <option>
                                        Ocupado
                                    </option>

                                    <option>
                                        Mantenimiento
                                    </option>

                                    <option>
                                        Aspecto Legal
                                    </option>

                                </select>

                            </div>

                            <!-- DEPOSITO -->
                            <div class="input-group">

                                <label>
                                    Depósito Inicial
                                </label>

                                <input 
                                    type="number"
                                    placeholder="$0.00"
                                >

                            </div>

                            <!-- RENTA -->
                            <div class="input-group">

                                <label>
                                    Renta Mensual
                                </label>

                                <input 
                                    type="number"
                                    placeholder="$0.00"
                                >

                            </div>

                            <!-- SERVICIOS -->
                            <div class="input-group">

                                <label>
                                    Servicios Incluidos
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
                            Información de Pago
                        </h3>

                        <div class="form-grid">

                            <!-- ABONOS -->
                            <div class="input-group">

                                <label>
                                    Permitir Abonos
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

                            <!-- LUZ -->
                            <div class="input-group">

                                <label>
                                    Manejo de Luz
                                </label>

                                <select>

                                    <option>
                                        Servicios Básicos
                                    </option>

                                    <option>
                                        Porcentajes de Consumo
                                    </option>

                                </select>

                            </div>

                            <!-- TIENDA -->
                            <div class="input-group">

                                <label>
                                    Tienda de Pago
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Nombre de la tienda"
                                >

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Evidencias y Mantenimiento
                        </h3>

                        <div class="form-grid">

                            <!-- EVIDENCIAS -->
                            <div class="input-group full-width">

                                <label>
                                    Evidencias / Reportes
                                </label>

                                <textarea 
                                    rows="5"
                                    placeholder="Describe daños, mantenimiento, problemas legales o información relevante..."
                                ></textarea>

                            </div>

                            <!-- SUBIR ARCHIVOS -->
                            <div class="input-group full-width">

                                <label>
                                    Subir Evidencias
                                </label>

                                <div class="upload-box">

                                    <img 
                                        src="../img/icons/upload.png"
                                        alt="Upload"
                                        width="50"
                                    >

                                    <p>
                                        Arrastra imágenes o documentos aquí
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
                                Guardar Arrendamiento
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