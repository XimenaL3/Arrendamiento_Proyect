<?php

ob_start();

session_start();

if (!isset($_SESSION['idUsuario']) || ($_SESSION['rol'] ?? 0) != 1) {
    header("Location: Login.php");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$idPersona = $_SESSION['idPersona'];

// CONEXIÓN
require_once "../../includes/Conexion.php";

// VALIDAR CONEXIÓN
if (!$conn) {

    die("Error de conexión a la base de datos");

}

// =============================================
// DATOS DEL USUARIO LOGUEADO
// =============================================

$stmt = $conn->prepare("
    SELECT 
        p.Nombre,
        p.ApellidoP,
        p.ApellidoM,
        p.Imagen,
        r.NombreRol
    FROM Usuarios u
    INNER JOIN Personas p ON p.idPersona = u.idPersona
    INNER JOIN Roles r ON r.idRol = u.idRol
    WHERE u.idUsuario = ?
");

if (!$stmt) {

    $nombre = "Usuario";
    $apellidoP = "";
    $apellidoM = "";
    $imagen = "../images/icons/Usuario.png";
    $rol = "Sin rol";

} else {

    $stmt->bind_param("i", $idUsuario);

    $stmt->execute();

    $stmt->bind_result(
        $nombre,
        $apellidoP,
        $apellidoM,
        $imagen,
        $rol
    );

    $stmt->fetch();

    $stmt->close();

    $nombre = trim($nombre);

    $apellidoP = trim($apellidoP);

    $apellidoM = trim($apellidoM);

    $rol = trim($rol);

    $imagenUsuario = (!empty($imagen))
        ? "../images/person/" . $imagen
        : "../images/icons/Usuario.png";
}

$nombreCompleto = $nombre . " " . $apellidoP . " " . $apellidoM;

/* =========================================
NOTIFICACIONES
========================================= */

$sqlNotificaciones = "
SELECT *
FROM Vista_Notificaciones
WHERE idUsuario = ?
ORDER BY FechaNotificacion DESC
LIMIT 10
";

$stmtNoti = $conn->prepare($sqlNotificaciones);

$stmtNoti->bind_param("i", $idUsuario);

$stmtNoti->execute();

$resultNoti = $stmtNoti->get_result();

/* =========================================
CONTAR NO LEÍDAS
========================================= */

$sqlCount = "
SELECT COUNT(*) AS total
FROM Vista_Notificaciones
WHERE idUsuario = ?
AND Estado = 'No Leida'
";

$stmtCount = $conn->prepare($sqlCount);

$stmtCount->bind_param("i", $idUsuario);

$stmtCount->execute();

$resultCount = $stmtCount->get_result();

$totalNotificaciones = 0;

if($rowCount = $resultCount->fetch_assoc())
{
    $totalNotificaciones = $rowCount['total'];
}

/* =========================================
OBTENER PRODUCTOS
========================================= */

$sql = "SELECT * FROM vw_Productos";

$resultado = $conn->query($sql);

/* =========================================
CONTADORES
========================================= */

$totalProductos = 0;
$disponibles = 0;
$stockBajo = 0;
$sinStock = 0;

if($resultado->num_rows > 0)
{
    while($row = $resultado->fetch_assoc())
    {
        $totalProductos++;

        if($row['EstadoStock'] == 'Disponible')
        {
            $disponibles++;
        }

        if($row['EstadoStock'] == 'Stock bajo')
        {
            $stockBajo++;
        }

        if($row['EstadoStock'] == 'Sin stock')
        {
            $sinStock++;
        }
    }
}

/* =========================================
VOLVER A EJECUTAR CONSULTA
========================================= */

$resultado = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta 
        name="viewport" 
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Productos de Limpieza
    </title>

    <!-- CSS -->
    <link 
        rel="stylesheet" 
        href="../css/style.css"
    >

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
                    src="../images/icons/Logo_Claro.jpeg"
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

                <a href="Interface_Productos_Limpieza.php" class="active">

                    <img 
                        src="../images/icons/Mantenimiento_Oscuro.png"
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
                        Productos de Limpieza
                    </h1>

                    <p class="subtitle">
                        Gestiona los productos disponibles en el inventario.
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

                        <?php if($totalNotificaciones > 0) { ?>

                        <div class="notification-badge">

                            <?php echo $totalNotificaciones; ?>

                        </div>

                        <?php } ?>

                    </div>

                    <!-- USER -->
                    <div class="logged-user">

                        <img 
                            src="<?php echo htmlspecialchars($imagenUsuario); ?>"
                            alt="Usuario"
                            class="avatar-admin"
                        >

                        <div class="user-info">

                            <small>
                                En uso por
                            </small>

                            <strong>
                                <?php echo htmlspecialchars($nombreCompleto); ?>
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
                            Estado
                        </label>

                        <select id="estadoFiltro">

                            <option value="">
                                Todos
                            </option>

                            <option value="Disponible">
                                Disponible
                            </option>

                            <option value="Stock bajo">
                                Stock bajo
                            </option>

                            <option value="Sin stock">
                                Sin stock
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar producto..."
                            id="buscador"
                        >

                        <a 
                            href="Interface_Agregar_Producto_Limpieza.php"
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

                <div class="workers-grid">

                    <!-- DISPONIBLES -->
                    <div class="worker-card">

                        <div class="worker-title">

                            <h3>
                                Disponibles
                            </h3>

                            <p>
                                <?= $disponibles; ?> productos
                            </p>

                        </div>

                    </div>

                    <!-- STOCK BAJO -->
                    <div class="worker-card">

                        <div class="worker-title">

                            <h3>
                                Stock Bajo
                            </h3>

                            <p>
                                <?= $stockBajo; ?> productos
                            </p>

                        </div>

                    </div>

                    <!-- SIN STOCK -->
                    <div class="worker-card">

                        <div class="worker-title">

                            <h3>
                                Sin Stock
                            </h3>

                            <p>
                                <?= $sinStock; ?> productos
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            <!-- PRODUCTS -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Inventario de Productos

                        <span class="badge">
                            <?= $totalProductos; ?>
                        </span>

                    </h2>

                </div>

                <div class="workers-grid" id="contenedorProductos">

                    <?php
                    
                    if($resultado->num_rows > 0)
                    {
                        while($producto = $resultado->fetch_assoc())
                        {

                            $imagen = !empty($producto['Imagen']) 
                                ? "../../" . $producto['Imagen']
                                : "../../images/products/default-product.png";

                            ?>

                            <!-- PRODUCTO -->
                            <div 
                                class="worker-card producto-card"
                                data-estado="<?= $producto['EstadoStock']; ?>"
                            >

                                <div class="card-header">

                                    <div class="worker-meta">

                                        <img 
                                            src="<?= $imagen; ?>"
                                            alt="Producto"
                                        >

                                        <div class="worker-title">

                                            <h3>
                                                <?= $producto['NombreProducto']; ?>
                                            </h3>

                                            <p>
                                                <?= $producto['EstadoStock']; ?>
                                            </p>

                                        </div>

                                    </div>

                                </div>

                                <div class="card-body">

                                    <p>

                                        <img 
                                            src="../images/icons/Cantidad.png"
                                            alt="Stock"
                                            class="info-icon"
                                        >

                                        Stock: <?= $producto['CantidadDisponible']; ?> unidades

                                    </p>

                                    <p>

                                        <img 
                                            src="../images/icons/Informacion.png"
                                            alt="Descripción"
                                            class="info-icon"
                                        >

                                        <?= $producto['Descripcion']; ?>

                                    </p>

                                </div>

                                <div class="card-footer">

                                    <a 
                                        href="Interface_Editar_Producto_Limpieza.php?id=<?= $producto['idProducto']; ?>"
                                        class="btn-action edit"
                                    >

                                        <img 
                                            src="../images/icons/Editar.png"
                                            alt="Editar"
                                            class="action-icon"
                                        >

                                    </a>

                                    <button class="btn-action delete">

                                        <img 
                                            src="../images/icons/Eliminar.png"
                                            alt="Eliminar"
                                            class="action-icon"
                                        >

                                    </button>

                                </div>

                            </div>

                            <?php
                        }
                    }
                    else
                    {
                        echo "
                            <p>
                                No hay productos registrados.
                            </p>
                        ";
                    }

                    ?>

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

<?php

if($resultNoti->num_rows > 0)
{

    while($noti = $resultNoti->fetch_assoc())
    {

?>

    <div class="notification-item <?php echo ($noti['Estado'] == 'Leida') ? 'completed' : ''; ?>">

        <div class="notification-info">

            <h4>

                <?php echo htmlspecialchars($noti['Titulo']); ?>

            </h4>

            <p>

                <?php echo htmlspecialchars($noti['Mensaje']); ?>

            </p>

            <span>

                <?php

                if($noti['MinutosTranscurridos'] < 60)
                {
                    echo "Hace " . $noti['MinutosTranscurridos'] . " minutos";
                }
                else if($noti['MinutosTranscurridos'] < 1440)
                {
                    echo "Hace " . floor($noti['MinutosTranscurridos'] / 60) . " horas";
                }
                else
                {
                    echo "Hace " . floor($noti['MinutosTranscurridos'] / 1440) . " días";
                }

                ?>

            </span>

        </div>

        <?php if($noti['Estado'] == 'No Leida') { ?>

        <button 
            class="btn-check"
            data-id="<?php echo $noti['idNotificacion']; ?>"
        >
            ✓
        </button>

        <?php } ?>

    </div>

<?php

    }

}
else
{

?>

<p>
    No hay notificaciones.
</p>

<?php } ?>

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

        const buscador = document.getElementById('buscador');

        const estadoFiltro = document.getElementById('estadoFiltro');

        const cards = document.querySelectorAll('.producto-card');

        /* ==============================
        SIDEBAR
        ============================== */

        function toggleSidebar()
        {
            sidebar.classList.toggle('collapsed');

            overlay.classList.toggle('active');
        }

        brandToggle.addEventListener('click', toggleSidebar);

        overlay.addEventListener('click', () =>
        {
            overlay.classList.remove('active');

            sidebar.classList.remove('collapsed');

            notificationsModal.classList.remove('active');
        });

        /* ==============================
        MODAL NOTIFICACIONES
        ============================== */

        notificationWrapper.addEventListener('click', () =>
        {
            notificationsModal.classList.add('active');

            overlay.classList.add('active');
        });

        closeModal.addEventListener('click', () =>
        {
            notificationsModal.classList.remove('active');

            overlay.classList.remove('active');
        });

        /* ==============================
        MARCAR COMO VISTA
        ============================== */

        document.querySelectorAll('.btn-check').forEach(button =>
        {

            button.addEventListener('click', () =>
            {

                const id = button.dataset.id;

                fetch('Marcar_Notificacion.php',
                {
                    method: 'POST',

                    headers:
                    {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },

                    body: 'idNotificacion=' + id
                })
                .then(response => response.text())
                .then(data =>
                {

                    if(data === "OK")
                    {

                        const notification = button.parentElement;

                        notification.classList.add('completed');

                        button.remove();

                        const badge = document.querySelector('.notification-badge');

                        if(badge)
                        {

                            let total = parseInt(badge.innerText);

                            total--;

                            if(total <= 0)
                            {
                                badge.remove();
                            }
                            else
                            {
                                badge.innerText = total;
                            }

                        }

                    }

                });

            });

        });

        /* ==============================
        FILTROS
        ============================== */

        function filtrarProductos()
        {
            const texto = buscador.value.toLowerCase();

            const estado = estadoFiltro.value;

            cards.forEach(card =>
            {
                const contenido = card.innerText.toLowerCase();

                const estadoCard = card.dataset.estado;

                let visible = true;

                if(!contenido.includes(texto))
                {
                    visible = false;
                }

                if(estado !== "" && estado !== estadoCard)
                {
                    visible = false;
                }

                card.style.display = visible ? "block" : "none";
            });
        }

        buscador.addEventListener("keyup", filtrarProductos);

        estadoFiltro.addEventListener("change", filtrarProductos);

    </script>

</body>

</html>