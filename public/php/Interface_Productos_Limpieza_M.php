<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta 
        name="viewport" 
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Solicitud de Productos | Mantenimiento
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* =========================================
        EXTRA CSS PRODUCTOS MANTENIMIENTO
        ========================================= */

        .product-status{

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 8px 14px;

            border-radius: 30px;

            font-size: 12px;

            font-weight: 700;

            margin-top: 12px;

        }

        .available{

            background: #dcfce7;

            color: #166534;

        }

        .low-stock{

            background: #fef3c7;

            color: #92400e;

        }

        .out-stock{

            background: #fee2e2;

            color: #991b1b;

        }

        .card-footer{

            justify-content: space-between;

            align-items: center;

        }

        .product-quantity{

            display: flex;

            align-items: center;

            gap: 10px;

        }

        .qty-btn{

            width: 38px;

            height: 38px;

            border: none;

            border-radius: 12px;

            background: #f3f4f6;

            cursor: pointer;

            font-size: 18px;

            transition: .3s ease;

        }

        .qty-btn:hover{

            background: #e5e7eb;

        }

        .qty-number{

            min-width: 20px;

            text-align: center;

            font-weight: 700;

        }

        .btn-add-cart{

            border: none;

            background: black;

            color: white;

            padding: 12px 18px;

            border-radius: 14px;

            cursor: pointer;

            font-weight: 600;

            transition: .3s ease;

        }

        .btn-add-cart:hover{

            transform: translateY(-2px);

        }

        /* =========================================
        TOP ACTIONS
        ========================================= */

        .top-actions{

            display: flex;

            align-items: center;

            gap: 18px;

        }

        /* =========================================
        CARRITO
        ========================================= */

        .cart-button{

            position: relative;

            width: 58px;

            height: 58px;

            border-radius: 18px;

            background: white;

            display: flex;

            align-items: center;

            justify-content: center;

            cursor: pointer;

            box-shadow: var(--shadow);

        }

        .cart-icon{

            width: 24px;

            height: 24px;

        }

        .cart-badge{

            position: absolute;

            top: -4px;

            right: -4px;

            width: 22px;

            height: 22px;

            border-radius: 50%;

            background: black;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 11px;

            font-weight: 700;

        }

        /* =========================================
        MODAL CARRITO
        ========================================= */

        .cart-modal{

            position: fixed;

            top: 50%;

            left: 50%;

            transform: translate(-50%, -50%) scale(.9);

            width: 700px;

            max-width: 95%;

            background: white;

            border-radius: 28px;

            padding: 28px;

            z-index: 100;

            opacity: 0;

            visibility: hidden;

            transition: .3s ease;

            box-shadow: 0 25px 60px rgba(0,0,0,.25);

        }

        .cart-modal.active{

            opacity: 1;

            visibility: visible;

            transform: translate(-50%, -50%) scale(1);

        }

        .cart-header{

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }

        .cart-header h2{

            font-size: 28px;

        }

        .cart-products{

            display: flex;

            flex-direction: column;

            gap: 18px;

            max-height: 300px;

            overflow-y: auto;

            margin-bottom: 25px;

        }

        .cart-item{

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 18px;

            border-radius: 18px;

            background: #f9fafb;

            border: 1px solid var(--border);

        }

        .cart-item-info{

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .cart-item-info img{

            width: 60px;

            height: 60px;

            border-radius: 16px;

            object-fit: cover;

        }

        .cart-item-info h4{

            font-size: 16px;

            margin-bottom: 4px;

        }

        .cart-item-info p{

            color: var(--text-muted);

            font-size: 13px;

        }

        .cart-qty{

            font-weight: 700;

            font-size: 15px;

        }

        /* =========================================
        FORMULARIO SOLICITUD
        ========================================= */

        .request-form{

            display: grid;

            grid-template-columns: repeat(2,1fr);

            gap: 20px;

            margin-bottom: 25px;

        }

        .request-group{

            display: flex;

            flex-direction: column;

            gap: 8px;

        }

        .request-group label{

            font-size: 13px;

            color: var(--text-muted);

        }

        .request-group input,
        .request-group select,
        .request-group textarea{

            border: 1px solid var(--border);

            background: #fafafa;

            border-radius: 14px;

            padding: 14px 16px;

            outline: none;

            resize: none;

        }

        .request-group.full{

            grid-column: span 2;

        }

        .cart-footer{

            display: flex;

            justify-content: flex-end;

            gap: 14px;

        }

        .btn-cart{

            border: none;

            padding: 14px 20px;

            border-radius: 14px;

            cursor: pointer;

            font-weight: 600;

            transition: .3s ease;

        }

        .btn-cancel{

            background: #f3f4f6;

            color: black;

        }

        .btn-confirm{

            background: black;

            color: white;

        }

        .btn-cart:hover{

            transform: translateY(-2px);

        }

        /* =========================================
        NOTIFICACIONES
        ========================================= */

        .notifications-modal{

            position: fixed;

            top: 50%;

            left: 50%;

            transform: translate(-50%, -50%) scale(.9);

            width: 420px;

            max-width: 90%;

            background: white;

            border-radius: 26px;

            padding: 24px;

            box-shadow: 0 25px 60px rgba(0,0,0,.25);

            z-index: 100;

            opacity: 0;

            visibility: hidden;

            transition: .3s ease;

        }

        .notifications-modal.active{

            opacity: 1;

            visibility: visible;

            transform: translate(-50%, -50%) scale(1);

        }

        .modal-header{

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }

        .modal-header h2{

            font-size: 22px;

            color: #111111;

        }

        .close-modal{

            width: 40px;

            height: 40px;

            border: none;

            border-radius: 12px;

            background: #f3f4f6;

            cursor: pointer;

            font-size: 18px;

            transition: .3s ease;

        }

        .close-modal:hover{

            background: #e5e7eb;

        }

        .notification-list{

            display: flex;

            flex-direction: column;

            gap: 16px;

            max-height: 450px;

            overflow-y: auto;

        }

        .notification-item{

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 15px;

            padding: 18px;

            border-radius: 18px;

            background: #f9fafb;

            border: 1px solid #ececec;

            transition: .3s ease;

        }

        .notification-item:hover{

            transform: translateY(-2px);

        }

        .notification-info h4{

            font-size: 15px;

            margin-bottom: 6px;

            color: #111111;

        }

        .notification-info p{

            font-size: 13px;

            color: #7a7a7a;

            line-height: 1.5;

        }

        .notification-info span{

            display: block;

            margin-top: 10px;

            font-size: 12px;

            color: #9ca3af;

        }

        .btn-check{

            min-width: 45px;

            height: 45px;

            border: none;

            border-radius: 14px;

            background: black;

            color: white;

            font-size: 18px;

            cursor: pointer;

            transition: .3s ease;

        }

        .btn-check:hover{

            transform: scale(1.05);

        }

        .notification-item.completed{

            opacity: .6;

            background: #f3f4f6;

        }

        .notification-item.completed .btn-check{

            background: #10b981;

        }

        @media (max-width: 768px){

            .request-form{

                grid-template-columns: 1fr;

            }

            .request-group.full{

                grid-column: span 1;

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

                    <span>Panel Mantenimiento</span>

                </div>

            </div>

            <!-- NAV -->
            <nav class="sidebar-nav">

                <a href="Interface_Reportes_M.php">

                    <img 
                        src="../images/icons/Reportes_Claro.png"
                        alt="Reportes"
                        class="menu-icon"
                    >

                    <span>Reportes</span>

                </a>

                <a href="Interface_Productos_Limpieza_M.php" class="active">

                    <img 
                        src="../images/icons/Mantenimiento_Oscuro.png"
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
                        Productos de Limpieza
                    </h1>

                    <p class="subtitle">
                        Solicita productos para reportes y departamentos de mantenimiento.
                    </p>

                </div>

                <div class="user-profile">

                    <div class="top-actions">

                        <!-- NOTIFICACIONES -->
                        <div class="notification-wrapper" id="openNotifications">

                            <img 
                                src="../images/icons/Notificaciones.png"
                                alt="Notificaciones"
                                class="top-icon"
                            >

                            <div class="notification-badge">
                                3
                            </div>

                        </div>

                        <!-- CARRITO -->
                        <div class="cart-button" id="openCart">

                            <img 
                                src="../images/icons/Canasta.png"
                                alt="Carrito"
                                class="cart-icon"
                            >

                            <div class="cart-badge">
                                3
                            </div>

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
                                Michael Torres
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
                            Categoría
                        </label>

                        <select>

                            <option>
                                Todos los productos
                            </option>

                            <option>
                                Desinfectantes
                            </option>

                            <option>
                                Jabones
                            </option>

                            <option>
                                Aromatizantes
                            </option>

                            <option>
                                Multiusos
                            </option>

                        </select>

                    </div>

                    <div class="filter-group">

                        <label>
                            Disponibilidad
                        </label>

                        <select>

                            <option>
                                Todos
                            </option>

                            <option>
                                Disponibles
                            </option>

                            <option>
                                Stock Bajo
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar producto..."
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

            <!-- PRODUCTS -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Inventario Disponible

                        <span class="badge">
                            24
                        </span>

                    </h2>

                </div>

                <div class="workers-grid">

                    <!-- PRODUCTO -->
                    <div class="worker-card">

                        <div class="card-header">

                            <div class="worker-meta">

                                <img 
                                    src="../images/icons/Usuario.png"
                                    alt="Producto"
                                >

                                <div class="worker-title">

                                    <h3>
                                        Cloro Industrial
                                    </h3>

                                    <p>
                                        Desinfectante
                                    </p>

                                </div>

                            </div>

                            <div class="product-status available">
                                Disponible
                            </div>

                        </div>

                        <div class="card-body">

                            <p>

                                <img 
                                    src="../images/icons/Cantidad.png"
                                    alt="Cantidad"
                                    class="info-icon"
                                >

                                Stock disponible: 25 unidades

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Informacion.png"
                                    alt="Uso"
                                    class="info-icon"
                                >

                                Uso para baños y áreas comunes

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Precio.png"
                                    alt="Código"
                                    class="info-icon"
                                >

                                Código: LIM-001

                            </p>

                        </div>

                        <div class="card-footer">

                            <div class="product-quantity">

                                <button class="qty-btn">
                                    -
                                </button>

                                <span class="qty-number">
                                    1
                                </span>

                                <button class="qty-btn">
                                    +
                                </button>

                            </div>

                            <button class="btn-add-cart">
                                Agregar
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

        </main>

    </div>

    <!-- =========================================
    MODAL CARRITO
    ========================================= -->

    <div class="cart-modal" id="cartModal">

        <div class="cart-header">

            <h2>
                Solicitud de Productos
            </h2>

            <button class="close-modal" id="closeCart">
                ✕
            </button>

        </div>

        <div class="cart-products">

            <div class="cart-item">

                <div class="cart-item-info">

                    <img 
                        src="../images/icons/Usuario.png"
                        alt="Producto"
                    >

                    <div>

                        <h4>
                            Cloro Industrial
                        </h4>

                        <p>
                            Desinfectante
                        </p>

                    </div>

                </div>

                <div class="cart-qty">
                    x2
                </div>

            </div>

        </div>

        <!-- FORM -->
        <div class="request-form">

            <div class="request-group">

                <label>
                    Tipo de Solicitud
                </label>

                <select>

                    <option>
                        Seleccionar
                    </option>

                    <option>
                        Reporte de Mantenimiento
                    </option>

                    <option>
                        Departamento
                    </option>

                </select>

            </div>

            <div class="request-group">

                <label>
                    Número de Reporte
                </label>

                <input 
                    type="text"
                    placeholder="Ej. REP-204"
                >

            </div>

            <div class="request-group full">

                <label>
                    Departamento / Área
                </label>

                <select>

                    <option>
                        Seleccionar departamento
                    </option>

                    <option>
                        Jardinería
                    </option>

                    <option>
                        Limpieza General
                    </option>

                    <option>
                        Área Administrativa
                    </option>

                </select>

            </div>

            <div class="request-group full">

                <label>
                    Motivo de la Solicitud
                </label>

                <textarea 
                    rows="4"
                    placeholder="Describe para qué serán utilizados los productos..."
                ></textarea>

            </div>

        </div>

        <div class="cart-footer">

            <button class="btn-cart btn-cancel" id="cancelCart">
                Cancelar
            </button>

            <button class="btn-cart btn-confirm">
                Confirmar Solicitud
            </button>

        </div>

    </div>

    <!-- =========================================
    MODAL NOTIFICACIONES
    ========================================= -->

    <div class="notifications-modal" id="notificationsModal">

        <div class="modal-header">

            <h2>
                Notificaciones
            </h2>

            <button class="close-modal" id="closeNotifications">
                ✕
            </button>

        </div>

        <div class="notification-list">

            <div class="notification-item">

                <div class="notification-info">

                    <h4>
                        Solicitud aprobada
                    </h4>

                    <p>
                        La solicitud REP-204 fue aprobada correctamente.
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
                        Stock Bajo
                    </h4>

                    <p>
                        El producto Limpiador Multiusos tiene pocas existencias.
                    </p>

                    <span>
                        Hace 15 minutos
                    </span>

                </div>

                <button class="btn-check">
                    ✓
                </button>

            </div>

            <div class="notification-item">

                <div class="notification-info">

                    <h4>
                        Nuevo producto agregado
                    </h4>

                    <p>
                        Se agregó nuevo inventario al almacén de limpieza.
                    </p>

                    <span>
                        Hace 1 hora
                    </span>

                </div>

                <button class="btn-check">
                    ✓
                </button>

            </div>

        </div>

    </div>

    <!-- SCRIPT -->
    <script>

        const sidebar = document.getElementById('sidebar');

        const brandToggle = document.getElementById('brandToggle');

        const overlay = document.getElementById('overlay');

        /* =========================================
        CARRITO
        ========================================= */

        const cartModal = document.getElementById('cartModal');

        const openCart = document.getElementById('openCart');

        const closeCart = document.getElementById('closeCart');

        const cancelCart = document.getElementById('cancelCart');

        /* =========================================
        NOTIFICACIONES
        ========================================= */

        const notificationsModal = document.getElementById('notificationsModal');

        const openNotifications = document.getElementById('openNotifications');

        const closeNotifications = document.getElementById('closeNotifications');

        const checkButtons = document.querySelectorAll('.btn-check');

        /* =========================================
        SIDEBAR
        ========================================= */

        function toggleSidebar(){

            sidebar.classList.toggle('collapsed');

            overlay.classList.toggle('active');

        }

        brandToggle.addEventListener('click', toggleSidebar);

        /* =========================================
        ABRIR CARRITO
        ========================================= */

        openCart.addEventListener('click', () => {

            closeAllModals();

            cartModal.classList.add('active');

            overlay.classList.add('active');

        });

        /* =========================================
        CERRAR CARRITO
        ========================================= */

        closeCart.addEventListener('click', closeCartModal);

        cancelCart.addEventListener('click', closeCartModal);

        function closeCartModal(){

            cartModal.classList.remove('active');

            overlay.classList.remove('active');

        }

        /* =========================================
        ABRIR NOTIFICACIONES
        ========================================= */

        openNotifications.addEventListener('click', () => {

            closeAllModals();

            notificationsModal.classList.add('active');

            overlay.classList.add('active');

        });

        /* =========================================
        CERRAR NOTIFICACIONES
        ========================================= */

        closeNotifications.addEventListener('click', closeNotificationsModal);

        function closeNotificationsModal(){

            notificationsModal.classList.remove('active');

            overlay.classList.remove('active');

        }

        /* =========================================
        OVERLAY
        ========================================= */

        overlay.addEventListener('click', () => {

            overlay.classList.remove('active');

            cartModal.classList.remove('active');

            notificationsModal.classList.remove('active');

            sidebar.classList.remove('collapsed');

        });

        /* =========================================
        CERRAR TODO
        ========================================= */

        function closeAllModals(){

            cartModal.classList.remove('active');

            notificationsModal.classList.remove('active');

        }

        /* =========================================
        MARCAR NOTIFICACION
        ========================================= */

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