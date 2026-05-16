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
CONSULTA
========================================= */

$sql = "SELECT * FROM vw_Trabajadores";

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
        Panel de Trabajadores
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

                <a href="Interface_Trabajadores.php" class="active">

                    <img 
                        src="../images/icons/Trabajadores_Oscuro.png"
                        alt="Trabajadores"
                        class="menu-icon"
                    >

                    <span>
                        Trabajadores
                    </span>

                </a>

                <a href="Interface_Clientes.php">

                    <img 
                        src="../images/icons/Clientes_Claro.png"
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
                        alt="Almacen Limpieza"
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
                        Bienvenido, <?php echo htmlspecialchars($nombre); ?>
                    </h1>

                    <p class="subtitle">
                        Gestiona trabajadores y actividades del sistema.
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

                    <!-- FILTRO ROL -->
                    <div class="filter-group">

                        <label>
                            Rol
                        </label>

                        <select id="rolFiltro">

                            <option value="">
                                Todos los roles
                            </option>

                            <option value="Administrador">
                                Administrador
                            </option>

                            <option value="Cajero">
                                Cajero
                            </option>

                            <option value="Cobrador">
                                Cobrador
                            </option>

                            <option value="Mantenimiento">
                                Mantenimiento
                            </option>

                        </select>

                    </div>

                    <!-- FILTRO ESTADO -->
                    <div class="filter-group">

                        <label>
                            Estado
                        </label>

                        <select id="estadoFiltro">

                            <option value="">
                                Todos
                            </option>

                            <option value="Activo">
                                Activos
                            </option>

                            <option value="Inactivo">
                                Inactivos
                            </option>

                        </select>

                    </div>

                    <!-- BUSCADOR -->
                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar trabajador..."
                            id="buscador"
                        >

                        <a 
                            href="Interface_Agregar_Trabajador.php"
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

            <!-- WORKERS -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Equipo de Trabajo

                        <span class="badge">

                            <?php echo $resultado->num_rows; ?>

                        </span>

                    </h2>

                </div>

                <div class="workers-grid" id="contenedorTrabajadores">

                    <?php while($fila = $resultado->fetch_assoc()) { ?>

                    <?php
                        
                        $estado = !empty($fila['Estado'])
                            ? $fila['Estado']
                            : 'Activo';

                        $rolTrabajador = $fila['NombreRol'];

                    ?>

                    <div 
                        class="worker-card trabajador-card"
                        data-rol="<?php echo $rolTrabajador; ?>"
                        data-estado="<?php echo $estado; ?>"
                    >

                        <div class="card-header">

                            <div class="worker-meta">

                                <?php
                                
                                $imagen = !empty($fila['Imagen'])
                                    ? "../images/person/" . $fila['Imagen']
                                    : "../images/icons/Usuario.png";
                                
                                ?>

                                <img 
                                    src="<?php echo $imagen; ?>"
                                    alt="Trabajador"
                                >

                                <div class="worker-title">

                                    <h3>
                                        <?php echo $fila['NombreCompleto']; ?>
                                    </h3>

                                    <p>
                                        <?php echo $fila['NombreRol']; ?>
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

                                <?php echo $fila['Correo']; ?>

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Telefono.png"
                                    alt="Teléfono"
                                    class="info-icon"
                                >

                                <?php echo $fila['Telefono']; ?>

                            </p>

                            <p>

                                <img 
                                    src="../images/icons/Rol.png"
                                    alt="Rol"
                                    class="info-icon"
                                >

                                <?php echo $fila['NombreRol']; ?>

                            </p>

                            <p>

                                <strong>
                                    Estado:
                                </strong>

                                <?php echo $estado; ?>

                            </p>

                        </div>

                        <div class="card-footer">

                            <a 
                                href="Interface_Editar_Trabajador.php?id=<?php echo $fila['idUsuario']; ?>" 
                                class="btn-action edit"
                            >
                                    
                                <img 
                                    src="../images/icons/Editar.png"
                                    alt="Editar"
                                    class="action-icon"
                                >

                            </a>
                            
                            <a 
                                href="Eliminar_Trabajador.php?id=<?php echo $fila['idUsuario']; ?>"
                                class="btn-action delete"
                                onclick="return confirm('¿Deseas eliminar este trabajador?')"
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

        const rolFiltro = document.getElementById('rolFiltro');

        const estadoFiltro = document.getElementById('estadoFiltro');

        const cards = document.querySelectorAll('.trabajador-card');

        /* ==============================
        SIDEBAR
        ============================== */

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
        MARCAR NOTIFICACIÓN
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

        function filtrarTrabajadores()
        {

            const texto = buscador.value.toLowerCase();

            const rol = rolFiltro.value;

            const estado = estadoFiltro.value;

            cards.forEach(card =>
            {

                const contenido = card.innerText.toLowerCase();

                const rolCard = card.dataset.rol;

                const estadoCard = card.dataset.estado;

                let visible = true;

                // BUSCADOR
                if(!contenido.includes(texto))
                {
                    visible = false;
                }

                // FILTRO ROL
                if(rol !== "" && rol !== rolCard)
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

        /* EVENTOS */

        buscador.addEventListener(
            "keyup",
            filtrarTrabajadores
        );

        rolFiltro.addEventListener(
            "change",
            filtrarTrabajadores
        );

        estadoFiltro.addEventListener(
            "change",
            filtrarTrabajadores
        );

    </script>

</body>

</html>