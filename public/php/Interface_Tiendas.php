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

// =============================================
// NOTIFICACIONES
// =============================================

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

// =============================================
// CONTAR NOTIFICACIONES
// =============================================

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

// =============================================
// OBTENER TIENDAS
// =============================================

$sqlTiendas = "
SELECT *
FROM vw_TiendasCobro
ORDER BY idTienda DESC
";

$resultadoTiendas = mysqli_query($conn, $sqlTiendas);

if(!$resultadoTiendas)
{
    die("Error en la consulta: " . mysqli_error($conn));
}

// =============================================
// CONTAR TIENDAS
// =============================================

$totalTiendas = mysqli_num_rows($resultadoTiendas);

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
        Gestión de Tiendas
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

    .tienda-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    }

    .tienda-card .worker-title{
        width: 100%;
    }

    .tienda-card .worker-title h3{
        margin-bottom: 5px;
    }

    .tienda-card .worker-title p{
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .tienda-header{

    display: flex;

    align-items: center;

    gap: 15px;

    width: 100%;

    }

    .tienda-logo{

        width: 95px;

        height: 95px;

        object-fit: cover;

        border-radius: 24px;

        border: 2px solid #f3f4f6;

        box-shadow: 0 8px 18px rgba(0,0,0,0.10);

        flex-shrink: 0;

    }

    .tienda-card{

        transition: .3s ease;

    }

    .tienda-card:hover{

        transform: translateY(-5px);

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
                        Gestión de Tiendas
                    </h1>

                    <p class="subtitle">
                        Administra tiendas de cobro registradas.
                    </p>

                </div>

                <div class="user-profile">

                    <!-- NOTIFICACIONES -->

                    <div
                        class="notification-wrapper"
                        id="notificationWrapper"
                    >

                        <img
                            src="../images/icons/Notificaciones.png"
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
                            class="avatar-admin"
                        >

                        <div class="user-info">

                            <small>
                                En uso por
                            </small>

                            <strong>

                                <?php
                                    echo htmlspecialchars(
                                        $nombreCompleto
                                    );
                                ?>

                            </strong>

                        </div>

                    </div>

                </div>

            </header>

            <!-- FILTROS -->

            <section class="search-section">

                <div class="filters">

                    <!-- BUSCADOR -->

                    <div class="search-input-wrapper">

                        <input
                            type="text"
                            placeholder="Buscar tienda..."
                            id="buscador"
                        >

                        <a
                            href="Interface_Agregar_Tiendas.php"
                            class="btn-search"
                        >

                            <img
                                src="../images/icons/Agregar.png"
                                class="button-icon"
                            >

                        </a>

                    </div>

                </div>

            </section>

            <!-- TIENDAS -->

            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Tiendas Registradas

                        <span class="badge">

                            <?php echo $totalTiendas; ?>

                        </span>

                    </h2>

                </div>

                <div
                    class="workers-grid"
                    id="contenedorTiendas"
                >

                <?php while($tienda = mysqli_fetch_assoc($resultadoTiendas)) { ?>

                    <div
                        class="worker-card tienda-card"
                        data-nombre="<?php echo strtolower($tienda['NombreTienda']); ?>"
                    >

                        <div class="card-header">

                            <div class="tienda-header">

                                <img 
                                    src="../images/icons/Logo_Oscuro.jpeg"
                                    alt="Tienda"
                                    class="tienda-logo"
                                >

                                <div class="worker-title">

                                    <h3>
                                        <?php
                                            echo htmlspecialchars(
                                                $tienda['NombreTienda']
                                            );
                                        ?>
                                    </h3>

                                    <p>
                                        Tienda de Cobro
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="card-footer">

                            <a
                                href="Interface_Editar_Tienda.php?id=<?php echo $tienda['idTienda']; ?>"
                                class="btn-action edit"
                            >

                                <img
                                    src="../images/icons/Editar.png"
                                    class="action-icon"
                                >

                            </a>

                            <a
                                href="Eliminar_Tienda.php?id=<?php echo $tienda['idTienda']; ?>"
                                class="btn-action delete"
                                onclick="return confirm('¿Deseas eliminar esta tienda?')"
                            >

                                <img
                                    src="../images/icons/Eliminar.png"
                                    class="action-icon"
                                >

                            </a>

                        </div>

                    </div>

                <?php } ?>

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

            <div
                class="notifications-modal"
                id="notificationsModal"
            >

                <div class="modal-header">

                    <h2>
                        Notificaciones
                    </h2>

                    <button
                        class="close-modal"
                        id="closeModal"
                    >
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

            <?php
                echo htmlspecialchars(
                    $noti['Titulo']
                );
            ?>

        </h4>

        <p>

            <?php
                echo htmlspecialchars(
                    $noti['Mensaje']
                );
            ?>

        </p>

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

        // =========================================
        // ELEMENTOS
        // =========================================

        const sidebar =
            document.getElementById('sidebar');

        const brandToggle =
            document.getElementById('brandToggle');

        const overlay =
            document.getElementById('overlay');

        const notificationWrapper =
            document.getElementById(
                'notificationWrapper'
            );

        const notificationsModal =
            document.getElementById(
                'notificationsModal'
            );

        const closeModal =
            document.getElementById(
                'closeModal'
            );

        // FILTROS

        const buscador =
            document.getElementById(
                'buscador'
            );


        const cards =
            document.querySelectorAll(
                '.tienda-card'
            );

        // =========================================
        // SIDEBAR
        // =========================================

        function toggleSidebar()
        {

            sidebar.classList.toggle(
                'collapsed'
            );

            overlay.classList.toggle(
                'active'
            );

        }

        brandToggle.addEventListener(
            'click',
            toggleSidebar
        );

        // =========================================
        // MODAL
        // =========================================

        notificationWrapper.addEventListener(
            'click',
            () =>
        {

            notificationsModal.classList.add(
                'active'
            );

            overlay.classList.add(
                'active'
            );

        });

        closeModal.addEventListener(
            'click',
            () =>
        {

            notificationsModal.classList.remove(
                'active'
            );

            overlay.classList.remove(
                'active'
            );

        });

        overlay.addEventListener(
            'click',
            () =>
        {

            notificationsModal.classList.remove(
                'active'
            );

            overlay.classList.remove(
                'active'
            );

        });

        // =========================================
        // FILTROS
        // =========================================

        function filtrarTiendas()
        {
            const texto =
                buscador.value.toLowerCase().trim();

            cards.forEach(card =>
            {

                const nombre =
                    card.dataset.nombre;

                if(nombre.includes(texto))
                {
                    card.style.display = "block";
                }
                else
                {
                    card.style.display = "none";
                }

            });

        }

        buscador.addEventListener(
            "keyup",
            filtrarTiendas
        );

    </script>

</body>

</html>

<?php
ob_end_flush();
?>