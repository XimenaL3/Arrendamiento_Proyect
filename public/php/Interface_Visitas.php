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

/* ==============================
OBTENER VISITAS
============================== */

$sqlVisitas = "SELECT * FROM vw_VisitasCobranza ORDER BY FechaVisita DESC";

$resultadoVisitas = mysqli_query($conn, $sqlVisitas);

if(!$resultadoVisitas)
{
    die("Error en consulta: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Visitas</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <style>

        /* =========================
           VISITAS
        ========================= */

        .visit-grid {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 25px;

        }

        .visit-card {

            background: white;

            border-radius: 26px;

            padding: 25px;

            box-shadow: var(--shadow);

            transition: 0.3s ease;

        }

        .visit-card:hover {

            transform: translateY(-6px);

        }

        .visit-header {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 15px;

            margin-bottom: 20px;

        }

        .visit-user {

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .visit-user img {

            width: 68px;
            height: 68px;

            border-radius: 18px;

            object-fit: cover;

        }

        .visit-user h3 {

            font-size: 18px;

            margin-bottom: 4px;

            color: var(--text);

        }

        .visit-user p {

            color: var(--text-muted);

            font-size: 14px;

        }

        .visit-info {

            margin-bottom: 22px;

            color: var(--text-muted);

            font-size: 14px;

        }

        .visit-info p {

            margin-bottom: 14px;

            display: flex;

            align-items: center;

            gap: 12px;

        }

        .visit-info img {

            width: 18px;
            height: 18px;

        }

        /* =========================
           ESTATUS
        ========================= */

        .status {

            padding: 7px 14px;

            border-radius: 12px;

            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

        }

        .pending {

            background: #fef3c7;

            color: #92400e;

        }

        .progress {

            background: #dbeafe;

            color: #1d4ed8;

        }

        .completed {

            background: #dcfce7;

            color: #166534;

        }

        .cancelled {

            background: #fee2e2;

            color: #991b1b;

        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1200px) {

            .visit-grid {

                grid-template-columns: repeat(2, 1fr);

            }

        }

        @media (max-width: 900px) {

            .visit-grid {

                grid-template-columns: 1fr;

            }

        }

        .visit-locked {

            width: 100%;

            padding: 14px;

            border-radius: 16px;

            background: #f3f4f6;

            color: #6b7280;

            font-size: 13px;

            font-weight: 600;

            text-align: center;

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

                <a href="Interface_Visitas.php" class="active">

                    <img 
                        src="../images/icons/Visitas_Oscuro.png"
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
                        Gestión de Visitas
                    </h1>

                    <p class="subtitle">
                        Administra las visitas agendadas y su estado actual.
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

                    <!-- FILTRO ESTATUS -->
                    <div class="filter-group">

                        <label>
                            Estatus
                        </label>

                        <select id="estatusFiltro">

                            <option value="">
                                Todos los estatus
                            </option>

                            <option value="pendiente">
                                Pendiente
                            </option>

                            <option value="en atención">
                                En Atención
                            </option>

                            <option value="atendida">
                                Atendida
                            </option>

                            <option value="cancelada">
                                Cancelada
                            </option>

                        </select>

                    </div>

                    <!-- FILTRO PERSONA -->
                    <div class="filter-group">

                        <label>
                            Persona que Visita
                        </label>

                        <select id="personaFiltro">

                            <option value="">
                                Todas las personas
                            </option>

                            <?php
                            
                            mysqli_data_seek($resultadoVisitas, 0);

                            while($persona = mysqli_fetch_assoc($resultadoVisitas))
                            {
                                $nombrePersona = 
                                    $persona['NombreInquilino'] . " " .
                                    $persona['ApellidoPInquilino'] . " " .
                                    $persona['ApellidoMInquilino'];
                                ?>

                                <option value="<?php echo strtolower($nombrePersona); ?>">

                                    <?php echo $nombrePersona; ?>

                                </option>

                                <?php
                            }

                            mysqli_data_seek($resultadoVisitas, 0);

                            ?>

                        </select>

                    </div>

                    <!-- BUSCADOR -->
                    <div class="search-input-wrapper">

                        <input 
                            type="text"
                            placeholder="Buscar visita..."
                            id="buscador"
                        >

                        <a 
                            href="Interface_Agregar_Visita.php"
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

            <!-- VISITAS -->
            <section class="workers-section">

                <div class="section-header">

                    <h2>

                        Visitas Agendadas

                        <span class="badge">

                            <?php echo mysqli_num_rows($resultadoVisitas); ?>

                        </span>

                    </h2>

                </div>

                <div class="visit-grid">

                    <?php while($visita = mysqli_fetch_assoc($resultadoVisitas)) { ?>

                        <?php
                        
                        $estatusClase = "";

                        switch($visita['Estatus'])
                        {
                            case "Pendiente":
                                $estatusClase = "pending";
                            break;

                            case "En Atención":
                                $estatusClase = "progress";
                            break;

                            case "Atendida":
                                $estatusClase = "completed";
                            break;

                            case "Cancelada":
                                $estatusClase = "cancelled";
                            break;
                        }

                        $nombreCompletoVisita =
                            $visita['NombreInquilino'] . " " .
                            $visita['ApellidoPInquilino'] . " " .
                            $visita['ApellidoMInquilino'];

                        ?>

                        <!-- CARD -->
                        <div 
                            class="visit-card"
                            data-estatus="<?php echo strtolower($visita['Estatus']); ?>"
                            data-persona="<?php echo strtolower($nombreCompletoVisita); ?>"
                        >

                            <div class="visit-header">

                                <div class="visit-user">

                                    <img 
                                        src="<?php
                                        
                                        if(!empty($visita['ImagenInquilino']))
                                        {
                                            echo "../images/person/" . $visita['ImagenInquilino'];
                                        }
                                        else
                                        {
                                            echo "../images/icons/Usuario.png";
                                        }

                                        ?>"
                                        alt="Visitante"
                                    >

                                    <div>

                                        <h3>

                                            <?php
                                            
                                            echo $nombreCompletoVisita;

                                            ?>

                                        </h3>

                                        <p>

                                            Cobrador:

                                            <?php
                                            
                                            echo $visita['NombreCobrador'] . " " .
                                                 $visita['ApellidoPCobrador'];

                                            ?>

                                        </p>

                                    </div>

                                </div>

                                <span class="status <?php echo $estatusClase; ?>">

                                    <?php echo $visita['Estatus']; ?>

                                </span>

                            </div>

                            <div class="visit-info">

                                <p>

                                    <img 
                                        src="../images/icons/Correo.png"
                                        alt=""
                                    >

                                    <?php echo $visita['CorreoInquilino']; ?>

                                </p>

                                <p>

                                    <img 
                                        src="../images/icons/Telefono.png"
                                        alt=""
                                    >

                                    <?php echo $visita['TelefonoInquilino']; ?>

                                </p>

                                <p>

                                    <img 
                                        src="../images/icons/Fecha_Visita.png"
                                        alt=""
                                    >

                                    <?php
                                    
                                    echo date(
                                        "d M Y - h:i A",
                                        strtotime($visita['FechaVisita'])
                                    );

                                    ?>

                                </p>

                                <p>

                                    <img 
                                        src="../images/icons/Comentario.png"
                                        alt=""
                                    >

                                    <?php echo $visita['Observaciones']; ?>

                                </p>

                            </div>

                            <div class="card-footer">

                                <?php if($visita['Estatus'] == "Pendiente") { ?>

                                    <!-- EDITAR -->
                                    <a 
                                        href="Interface_Editar_Visita.php?id=<?php echo $visita['idVisita']; ?>"
                                        class="btn-action edit"
                                    >

                                        <img 
                                            src="../images/icons/Editar.png"
                                            alt="Editar"
                                            class="action-icon"
                                        >

                                    </a>

                                    <!-- ELIMINAR -->
                                    <a 
                                        href="Eliminar_Visita.php?id=<?php echo $visita['idVisita']; ?>"
                                        class="btn-action delete"
                                        onclick="return confirm('¿Deseas eliminar esta visita?')"
                                    >

                                        <img 
                                            src="../images/icons/Eliminar.png"
                                            alt="Eliminar"
                                            class="action-icon"
                                        >

                                    </a>

                                <?php } else { ?>

                                    <div class="visit-locked">

                                        Visita cerrada

                                    </div>

                                <?php } ?>

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

        // FILTROS
        const buscador = document.getElementById('buscador');

        const estatusFiltro = document.getElementById('estatusFiltro');

        const personaFiltro = document.getElementById('personaFiltro');

        const cards = document.querySelectorAll('.visit-card');

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

        function filtrarVisitas()
        {
            const texto = buscador.value.toLowerCase();

            const estatus = estatusFiltro.value.toLowerCase();

            const persona = personaFiltro.value.toLowerCase();

            cards.forEach(card =>
            {
                const contenido = card.innerText.toLowerCase();

                const estatusCard = card.dataset.estatus;

                const personaCard = card.dataset.persona;

                let visible = true;

                // BUSCADOR
                if(!contenido.includes(texto))
                {
                    visible = false;
                }

                // FILTRO ESTATUS
                if(estatus !== "" && estatus !== estatusCard)
                {
                    visible = false;
                }

                // FILTRO PERSONA
                if(persona !== "" && persona !== personaCard)
                {
                    visible = false;
                }

                card.style.display = visible ? "block" : "none";
            });
        }

        // EVENTOS
        buscador.addEventListener("keyup", filtrarVisitas);

        estatusFiltro.addEventListener("change", filtrarVisitas);

        personaFiltro.addEventListener("change", filtrarVisitas);

    </script>

</body>

</html>

<?php
ob_end_flush();
?>