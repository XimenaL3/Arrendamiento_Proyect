<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Arrendamiento</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-select{
            background: #f5f7fb;
            border: 1px solid #dcdfe6;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
            font-size: 15px;
        }

        .extra-info{
            margin-top: 25px;
            padding: 20px;
            border-radius: 16px;
            background: #f8f9fc;
            border: 1px solid #e5e7eb;
        }

        .extra-info h3{
            margin-bottom: 18px;
            color: #1f2937;
        }

        .evidence-box{
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .evidence-item{
            width: 120px;
            height: 90px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #dbe3ef;
        }

        .evidence-item img{
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-badge{
            display: inline-block;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-maintenance{
            background: #fff4d6;
            color: #c98a00;
        }

        .btn-delete{
            background: #ff4d4f;
            color: white;
            border: none;
            padding: 14px 22px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: .3s;
        }

        .btn-delete:hover{
            background: #e63b3d;
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
                        Editar Arrendamiento
                    </h1>

                    <p class="subtitle">
                        Modifica la información del inmueble, pagos y estatus.
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

            <!-- EDIT SECTION -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="../img/local1.jpg"
                            alt="Arrendamiento"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Cambiar Imagen
                        </button>

                        <div class="status-badge status-maintenance">
                            En mantenimiento
                        </div>

                    </div>

                    <!-- FORM -->
                    <form class="edit-form">

                        <div class="form-grid">

                            <!-- NOMBRE -->
                            <div class="input-group">

                                <label>
                                    Nombre del Inmueble
                                </label>

                                <input 
                                    type="text"
                                    value="Local Comercial #12"
                                >

                            </div>

                            <!-- TIPO -->
                            <div class="input-group">

                                <label>
                                    Tipo de Arrendamiento
                                </label>

                                <select class="status-select">

                                    <option>
                                        Local Comercial
                                    </option>

                                    <option>
                                        Casa
                                    </option>

                                    <option selected>
                                        Edificio
                                    </option>

                                </select>

                            </div>

                            <!-- INQUILINO -->
                            <div class="input-group">

                                <label>
                                    Nombre del Inquilino
                                </label>

                                <input 
                                    type="text"
                                    value="Carlos Mendoza"
                                >

                            </div>

                            <!-- TELEFONO -->
                            <div class="input-group">

                                <label>
                                    Teléfono
                                </label>

                                <input 
                                    type="text"
                                    value="+52 418 123 4567"
                                >

                            </div>

                            <!-- MONTO -->
                            <div class="input-group">

                                <label>
                                    Renta Mensual
                                </label>

                                <input 
                                    type="text"
                                    value="$12,000"
                                >

                            </div>

                            <!-- DEPOSITO -->
                            <div class="input-group">

                                <label>
                                    Depósito
                                </label>

                                <input 
                                    type="text"
                                    value="$15,000"
                                >

                            </div>

                            <!-- ESTATUS -->
                            <div class="input-group">

                                <label>
                                    Estatus
                                </label>

                                <select class="status-select">

                                    <option>
                                        Disponible
                                    </option>

                                    <option selected>
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

                            <!-- PAGO -->
                            <div class="input-group">

                                <label>
                                    Estado de Pago
                                </label>

                                <select class="status-select">

                                    <option>
                                        Pagado
                                    </option>

                                    <option selected>
                                        Pendiente
                                    </option>

                                    <option>
                                        Adeudo
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
                                    value="Av. Principal #45, Zona Centro"
                                >

                            </div>

                            <!-- DESCRIPCION -->
                            <div class="input-group full-width">

                                <label>
                                    Descripción
                                </label>

                                <textarea rows="5">

Edificio con locales comerciales y habitaciones superiores. El servicio de luz se divide por porcentajes dependiendo del consumo de cada habitación.

                                </textarea>

                            </div>

                        </div>

                        <!-- INFORMACION EXTRA -->
                        <div class="extra-info">

                            <h3>
                                Evidencias y Mantenimiento
                            </h3>

                            <p>
                                Reparación de instalaciones eléctricas en proceso.
                            </p>

                            <div class="evidence-box">

                                <div class="evidence-item">

                                    <img 
                                        src="../img/evidencia1.jpg"
                                        alt="Evidencia"
                                    >

                                </div>

                                <div class="evidence-item">

                                    <img 
                                        src="../img/evidencia2.jpg"
                                        alt="Evidencia"
                                    >

                                </div>

                                <div class="evidence-item">

                                    <img 
                                        src="../img/evidencia3.jpg"
                                        alt="Evidencia"
                                    >

                                </div>

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="form-buttons">

                            <button type="button" class="btn-delete">
                                Eliminar
                            </button>

                            <button type="reset" class="btn-cancel">
                                Cancelar
                            </button>

                            <button type="submit" class="btn-save">
                                Guardar Cambios
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