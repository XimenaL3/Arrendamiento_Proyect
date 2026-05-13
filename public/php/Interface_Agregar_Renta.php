<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Arrendamiento</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-badge{

            display: inline-block;
            padding: 8px 14px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #111827;
            font-size: 13px;
            font-weight: 600;

        }

        .section-subtitle{

            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 18px;
            color: #1f2937;
            font-weight: 700;

        }

        .upload-box{

            border: 2px dashed #d1d5db;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            background: #f9fafb;
            transition: .3s;

        }

        .upload-box:hover{

            border-color: #111827;
            background: #f3f4f6;

        }

        .upload-box p{

            color: #6b7280;
            margin-top: 12px;
            font-size: 14px;

        }

        .info-card{

            margin-top: 30px;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            padding: 22px;
            box-shadow: 0 10px 25px rgba(0,0,0,.05);

        }

        .info-card h3{

            margin-bottom: 18px;
            color: #111827;

        }

        .info-grid{

            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;

        }

        .info-item{

            background: #f9fafb;
            border-radius: 14px;
            padding: 16px;
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
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 15px;

        }

        .detail-card{

            flex: 1;
            min-width: 180px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;

        }

        .detail-card h4{

            margin-bottom: 10px;
            color: #111827;
            font-size: 15px;

        }

        .detail-card p{

            color: #6b7280;
            font-size: 14px;

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

                <p class="section-title">
                    General
                </p>

                <a href="Interface_Trabajadores.php" class="active">

                    <img 
                        src="../images/icons/Trabajadores_Oscuro.png"
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

                <a href="Interface_Productos_Limpieza.php">

                    <img 
                        src="../images/icons/Mantenimiento_Claro.png"
                        alt="Almacen Limpieza"
                        class="menu-icon"
                    >

                    <span>Almacen Limpieza</span>

                </a>

                <a href="Interface_Abonos.php">

                    <img 
                        src="../images/icons/Pago_Claro.png"
                        alt="Abonos"
                        class="menu-icon"
                    >

                    <span>Abonos</span>

                </a>

                <p class="section-title">
                    Herramientas
                </p>

                <a href="Interface_Arrendamientos.php">

                    <img 
                        src="../images/icons/Arrendamiento_Claro.png"
                        alt="Arrendamiento"
                        class="menu-icon"
                    >

                    <span>Arrendamiento</span>

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
                        Registrar Nueva Renta
                    </h1>

                    <p class="subtitle">
                        Registra un nuevo contrato de renta para locales, casas o edificios.
                    </p>

                </div>

                <div class="user-profile">

                    <!-- NOTIFICACIONES -->
                    <div class="notification-wrapper">

                        <img 
                            src="../img/icons/bell.png"
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
                            src="../img/admin.jpg"
                            alt="Admin"
                            class="avatar-admin"
                        >

                        <div class="user-info">

                            <small>
                                Administrador
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
                            src="../img/local1.jpg"
                            alt="Arrendamiento"
                            class="edit-avatar"
                        >

                        <button class="change-photo-btn">
                            Subir Contrato
                        </button>

                        <div style="margin-top: 15px;">

                            <span class="status-badge">
                                Nuevo Contrato
                            </span>

                        </div>

                    </div>

                    <!-- FORMULARIO -->
                    <form class="edit-form">

                        <h3 class="section-subtitle">
                            Información del Cliente
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

                            <!-- HISTORIAL -->
                            <div class="input-group">

                                <label>
                                    Historial del Cliente
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
                            Información de la Propiedad
                        </h3>

                        <div class="form-grid">

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

                            <!-- PROPIEDAD -->
                            <div class="input-group">

                                <label>
                                    Propiedad Disponible
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

                            <!-- DIRECCION -->
                            <div class="input-group full-width">

                                <label>
                                    Dirección
                                </label>

                                <input 
                                    type="text"
                                    placeholder="Dirección completa del inmueble"
                                >

                            </div>

                            <!-- ESTADO -->
                            <div class="input-group">

                                <label>
                                    Estado del Lugar
                                </label>

                                <select>

                                    <option>
                                        Excelente Condición
                                    </option>

                                    <option>
                                        Disponible
                                    </option>

                                    <option>
                                        Mantenimiento
                                    </option>

                                </select>

                            </div>

                            <!-- SERVICIOS -->
                            <div class="input-group">

                                <label>
                                    Servicios Incluidos
                                </label>

                                <select>

                                    <option>
                                        Servicios Básicos
                                    </option>

                                    <option>
                                        Luz por porcentajes
                                    </option>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Información de la Renta
                        </h3>

                        <div class="form-grid">

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

                            <!-- FECHA -->
                            <div class="input-group">

                                <label>
                                    Fecha de Inicio
                                </label>

                                <input 
                                    type="date"
                                >

                            </div>

                            <!-- DURACION -->
                            <div class="input-group">

                                <label>
                                    Duración del Contrato
                                </label>

                                <select>

                                    <option>
                                        6 Meses
                                    </option>

                                    <option>
                                        1 Año
                                    </option>

                                    <option>
                                        2 Años
                                    </option>

                                </select>

                            </div>

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

                        </div>

                        <h3 class="section-subtitle">
                            Observaciones
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Detalles del Contrato
                                </label>

                                <textarea 
                                    rows="5"
                                    placeholder="Escribe acuerdos, observaciones o detalles importantes del arrendamiento..."
                                ></textarea>

                            </div>

                            <div class="input-group full-width">

                                <label>
                                    Subir Contrato o Evidencias
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

                        <!-- RESUMEN -->
                        <div class="info-card">

                            <h3>
                                Resumen del Arrendamiento
                            </h3>

                            <div class="info-grid">

                                <div class="info-item">

                                    <span>
                                        Tipo de Contrato
                                    </span>

                                    <strong>
                                        Arrendamiento Comercial
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Depósito Requerido
                                    </span>

                                    <strong>
                                        $15,000 MXN
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Estado del Cliente
                                    </span>

                                    <strong>
                                        Buen Historial
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Estatus
                                    </span>

                                    <strong>
                                        Pendiente de Firma
                                    </strong>

                                </div>

                            </div>

                            <!-- DETALLES -->
                            <div class="details-box">

                                <div class="detail-card">

                                    <h4>
                                        Método de Cobro
                                    </h4>

                                    <p>
                                        Pago mensual en tienda autorizada.
                                    </p>

                                </div>

                                <div class="detail-card">

                                    <h4>
                                        Servicios
                                    </h4>

                                    <p>
                                        Servicios básicos incluidos en la renta.
                                    </p>

                                </div>

                                <div class="detail-card">

                                    <h4>
                                        Condición del Lugar
                                    </h4>

                                    <p>
                                        Inmueble disponible y en excelentes condiciones.
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
                                Registrar Arrendamiento
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