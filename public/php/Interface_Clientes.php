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

// =============================================
// OBTENER CLIENTES
// =============================================

$sqlClientes = "
SELECT * FROM vw_Clientes
ORDER BY idInquilino DESC
";

$resultadoClientes = mysqli_query($conn, $sqlClientes);

if(!$resultadoClientes)
{
    die("Error en la consulta: " . mysqli_error($conn));
}

// =============================================
// CONTAR CLIENTES
// =============================================

$totalClientes = mysqli_num_rows($resultadoClientes);

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
        Panel de Clientes
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

                    <h2>
                        Sunlight Gardens
                    </h2>

                    <span>
                        Panel Administrativo
                    </span>

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

                    <span>
                        Trabajadores
                    </span>

                </a>

                <a 
                    href="Interface_Clientes.php" 
                    class="active"
                >

                    <img 
                        src="../images/icons/Clientes_Oscuro.png"
                        alt="Clientes"
                        class="menu-icon"
                    >

                    <span>
                        Clientes
                    </span>

                </a>

                <a href="Interface_Visitas.php">

                    <img 
                        src="../images/icons/Visitas_Claro.png"
                        alt="Visitas"
                        class="menu-icon"
                    >

                    <span>
                        Visitas
                    </span>

                </a>

                <a href="Interface_Arrendamientos.php">

                    <img 
                        src="../images/icons/Arrendamiento_Claro.png"
                        alt="Arrendamiento"
                        class="menu-icon"
                    >

                    <span>
                        Arrendamientos
                    </span>

                </a>

                <a href="Interface_Abonos.php">

                    <img 
                        src="../images/icons/Pago_Claro.png"
                        alt="Abonos"
                        class="menu-icon"
                    >

                    <span>
                        Abonos
                    </span>

                </a>

                <a href="Interface_Productos_Limpieza.php">

                    <img 
                        src="../images/icons/Mantenimiento_Claro.png"
                        alt="Almacén"
                        class="menu-icon"
                    >

                    <span>
                        Almacén Limpieza
                    </span>

                </a>

                <a href="Interface_Reportes.php">

                    <img 
                        src="../images/icons/Reportes_Claro.png"
                        alt="Reportes"
                        class="menu-icon"
                    >

                    <span>
                        Reportes
                    </span>

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

                    <span>
                        Cerrar Sesión
                    </span>

                </a>

            </div>

        </aside>

        <!-- MAIN -->
        <main class="main-content">

            <!-- TOPBAR -->
            <header class="top-bar">

                <div>

                    <h1>
                        Gestión de Clientes
                    </h1>

                    <p class="subtitle">
                        Administra clientes, contratos y estados de renta.
                    </p>

                </div>

                <div class="user-profile">

                    <!-- NOTIFICACIONES -->
                    <div class="notification-wrapper" id="notificationWrapper">

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

            <!-- FILTROS -->
            <section class="search-section">

                <div class="filters">

                    <!-- TIPO -->
                    <div class="filter-group">

                        <label>
                            Tipo de Cliente
                        </label>

                        <select id="tipoFiltro">

                            <option value="">
                                Todos
                            </option>

                            <option value="Bueno">
                                Bueno
                            </option>

                            <option value="Nuevo">
                                Nuevo
                            </option>

                            <option value="Malo">
                                Malo
                            </option>

                        </select>

                    </div>

                    <!-- ESTADO -->
                    <div class="filter-group">

                        <label>
                            Estado
                        </label>

                        <select id="estadoFiltro">

                            <option value="">
                                Todos
                            </option>

                            <option value="Activo">
                                Activo
                            </option>

                            <option value="Inactivo">
                                Inactivo
                            </option>

                        </select>

                    </div>

                    <!-- BUSCADOR -->
                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar cliente..."
                            id="buscador"
                        >

                        <a 
                            href="Interface_Agregar_Cliente.php"
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

            <!-- CLIENTES -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Clientes Registrados

                        <span class="badge">
                            <?php echo $totalClientes; ?>
                        </span>

                    </h2>

                </div>

                <div class="workers-grid" id="contenedorClientes">

                    <?php while($cliente = mysqli_fetch_assoc($resultadoClientes)) { ?>

                        <?php

                        $historial = !empty($cliente['HistorialCrediticio'])
                            ? $cliente['HistorialCrediticio']
                            : "Regular";

                        $estadoCliente = !empty($cliente['Estado'])
                            ? $cliente['Estado']
                            : "Activo";

                        ?>

                        <div 
                            class="worker-card cliente-card"
                            data-tipo="<?php echo strtolower($historial); ?>"
                            data-estado="<?php echo strtolower($estadoCliente); ?>"
                        >

                            <div class="card-header">

                                <div class="worker-meta">

                                    <img 
                                        src="<?php
                                        
                                            if(!empty($cliente['Imagen']))
                                            {
                                                echo "../images/person/" . $cliente['Imagen'];
                                            }
                                            else
                                            {
                                                echo "../images/icons/Usuario.png";
                                            }

                                        ?>"
                                        alt="Cliente"
                                    >

                                    <div class="worker-title">

                                        <h3>
                                            <?php echo htmlspecialchars($cliente['NombreCompleto']); ?>
                                        </h3>

                                        <p>
                                            Cliente Registrado
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <div class="card-body">

                                <p>

                                    <img 
                                        src="../images/icons/Correo.png"
                                        alt="Correo"
                                        class="info-icon"
                                    >

                                    <?php echo htmlspecialchars($cliente['Correo']); ?>

                                </p>

                                <p>

                                    <img 
                                        src="../images/icons/Telefono.png"
                                        alt="Teléfono"
                                        class="info-icon"
                                    >

                                    <?php echo htmlspecialchars($cliente['Telefono']); ?>

                                </p>

                                <p>

                                    <img 
                                        src="../images/icons/Historial_Credito.png"
                                        alt="Historial"
                                        class="info-icon"
                                    >

                                    <?php echo htmlspecialchars($cliente['HistorialCrediticio']); ?>

                                </p>

                            </div>

                            <div class="card-footer">

                                <a 
                                    href="Interface_Editar_Cliente.php?id=<?php echo $cliente['idInquilino']; ?>" 
                                    class="btn-action edit"
                                >

                                    <img 
                                        src="../images/icons/Editar.png"
                                        alt="Editar"
                                        class="action-icon"
                                    >

                                </a>

                                <a 
                                    href="Eliminar_Cliente.php?id=<?php echo $cliente['idInquilino']; ?>"
                                    class="btn-action delete"
                                    onclick="return confirm('¿Deseas eliminar este cliente?')"
                                >

                                    <img 
                                        src="../images/icons/Eliminar.png"
                                        alt="Eliminar"
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

            <!-- MODAL -->
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

        // =========================================
        // ELEMENTOS
        // =========================================

        const sidebar = document.getElementById('sidebar');

        const brandToggle = document.getElementById('brandToggle');

        const overlay = document.getElementById('overlay');

        const notificationWrapper = document.getElementById('notificationWrapper');

        const notificationsModal = document.getElementById('notificationsModal');

        const closeModal = document.getElementById('closeModal');

        const checkButtons = document.querySelectorAll('.btn-check');

        // FILTROS
        const buscador = document.getElementById('buscador');

        const tipoFiltro = document.getElementById('tipoFiltro');

        const estadoFiltro = document.getElementById('estadoFiltro');

        const cards = document.querySelectorAll('.cliente-card');

        // =========================================
        // SIDEBAR
        // =========================================

        function toggleSidebar()
        {
            sidebar.classList.toggle('collapsed');

            overlay.classList.toggle('active');
        }

        brandToggle.addEventListener('click', toggleSidebar);

        // =========================================
        // MODAL
        // =========================================

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

        overlay.addEventListener('click', () =>
        {
            overlay.classList.remove('active');

            notificationsModal.classList.remove('active');

            sidebar.classList.remove('collapsed');
        });

        // =========================================
        // NOTIFICACIONES
        // =========================================

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

        // =========================================
        // FILTROS
        // =========================================

        function filtrarClientes()
        {
            const texto = buscador.value.toLowerCase();

            const tipo = tipoFiltro.value.toLowerCase();

            const estado = estadoFiltro.value.toLowerCase();

            cards.forEach(card =>
            {
                const contenido = card.innerText.toLowerCase();

                const tipoCard = card.dataset.tipo;

                const estadoCard = card.dataset.estado;

                let visible = true;

                // BUSCADOR
                if(!contenido.includes(texto))
                {
                    visible = false;
                }

                // FILTRO TIPO
                if(tipo !== "" && tipo !== tipoCard)
                {
                    visible = false;
                }

                // FILTRO ESTADO
                if(estado !== "" && estado !== estadoCard)
                {
                    visible = false;
                }

                card.style.display = visible ? "block" : "none";
            });
        }

        // EVENTOS
        buscador.addEventListener("keyup", filtrarClientes);

        tipoFiltro.addEventListener("change", filtrarClientes);

        estadoFiltro.addEventListener("change", filtrarClientes);

    </script>

</body>

</html>

<?php
ob_end_flush();
?>