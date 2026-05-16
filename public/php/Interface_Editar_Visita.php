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
OBTENER ID VISITA
========================================= */

$idVisita = isset($_GET['id']) ? $_GET['id'] : 0;

/* =========================================
OBTENER DATOS DE LA VISITA
========================================= */

$sqlVisita = "
    SELECT 
        vc.idVisita,
        vc.idUsuario,
        vc.idInquilino,
        vc.FechaVisita,
        vc.Observaciones,
        vc.Estatus,

        CONCAT(
            pu.Nombre,' ',
            pu.ApellidoP,' ',
            IFNULL(pu.ApellidoM,'')
        ) AS NombreUsuario,

        CONCAT(
            pi.Nombre,' ',
            pi.ApellidoP,' ',
            IFNULL(pi.ApellidoM,'')
        ) AS NombreCliente,

        pi.Imagen AS ImagenCliente

    FROM Visitas_Cobranza vc

    INNER JOIN Usuarios u
        ON vc.idUsuario = u.idUsuario

    INNER JOIN Personas pu
        ON u.idPersona = pu.idPersona

    INNER JOIN Inquilinos i
        ON vc.idInquilino = i.idInquilino

    INNER JOIN Personas pi
        ON i.idPersona = pi.idPersona

    WHERE vc.idVisita = ?
";

$stmtVisita = $conn->prepare($sqlVisita);

$stmtVisita->bind_param("i", $idVisita);

$stmtVisita->execute();

$resultadoVisita = $stmtVisita->get_result();

$visita = $resultadoVisita->fetch_assoc();

/* =========================================
ACTUALIZAR VISITA
========================================= */

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $idUsuario       = $_POST['idUsuario'];
    $idInquilino     = $_POST['idInquilino'];

    $fecha           = $_POST['fechaVisita'];
    $hora            = $_POST['horaVisita'];

    $fechaCompleta   = $fecha . " " . $hora . ":00";

    $estatus         = $_POST['estatus'];

    $observaciones   = $_POST['observaciones'];

    $sqlEditar = "CALL sp_EditarVisita(?, ?, ?, ?, ?, ?)";

    $stmtEditar = $conn->prepare($sqlEditar);

    $stmtEditar->bind_param(
        "iiisss",
        $idVisita,
        $idUsuario,
        $idInquilino,
        $fechaCompleta,
        $observaciones,
        $estatus
    );

    if($stmtEditar->execute())
    {
        header("Location: Interface_Visitas.php");
        exit();
    }
    else
    {
       $mensaje = "Error al registrar trabajador";
    }

}

/* =========================================
OBTENER COBRADORES
========================================= */

$sqlUsuarios = "
    SELECT 
        u.idUsuario,

        CONCAT(
            p.Nombre,' ',
            p.ApellidoP,' ',
            IFNULL(p.ApellidoM,'')
        ) AS NombreCompleto

    FROM Usuarios u

    INNER JOIN Personas p
        ON u.idPersona = p.idPersona
";

$usuarios = $conn->query($sqlUsuarios);

/* =========================================
OBTENER CLIENTES
========================================= */

$sqlClientes = "
    SELECT 
        i.idInquilino,

        CONCAT(
            p.Nombre,' ',
            p.ApellidoP,' ',
            IFNULL(p.ApellidoM,'')
        ) AS NombreCompleto

    FROM Inquilinos i

    INNER JOIN Personas p
        ON i.idPersona = p.idPersona
";

$clientes = $conn->query($sqlClientes);

/* =========================================
FORMATEAR FECHA Y HORA
========================================= */

$fecha = date("Y-m-d", strtotime($visita['FechaVisita']));

$hora = date("H:i", strtotime($visita['FechaVisita']));

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Editar Visita
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

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

                <a href="Interface_Visitas.php" class="active">

                    <img 
                        src="../images/icons/Visitas_Oscuro.png"
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
                        Editar Visita
                    </h1>

                    <p class="subtitle">
                        Modifica la información y estado de la visita agendada.
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

            <!-- FORM SECTION -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                    <img
                        src="<?php 
                            echo !empty($visita['ImagenCliente']) 
                                ? '../images/person/' . $visita['ImagenCliente']
                                : '../images/icons/Usuario.png';
                        ?>"
                        alt="Cliente"
                        class="edit-avatar"
                    />

                        <button class="change-photo-btn">
                            Editar Visita
                        </button>

                    </div>

                    <!-- FORM -->
                    <form 
                        class="edit-form"
                        method="POST"
                    >

                        <div class="form-grid">

                            <!-- COBRADOR -->
                            <div class="input-group">

                                <label>
                                    Cobrador Responsable
                                </label>

                                <select 
                                    name="idUsuario"
                                    required
                                >

                                    <?php
                                    
                                    while($usuario = $usuarios->fetch_assoc())
                                    {

                                        $selected = ($usuario['idUsuario'] == $visita['idUsuario']) ? "selected" : "";

                                        ?>

                                        <option 
                                            value="<?= $usuario['idUsuario']; ?>"
                                            <?= $selected; ?>
                                        >

                                            <?= $usuario['NombreCompleto']; ?>

                                        </option>

                                        <?php

                                    }

                                    ?>

                                </select>

                            </div>

                            <!-- CLIENTE -->
                            <div class="input-group">

                                <label>
                                    Cliente a Visitar
                                </label>

                                <select 
                                    name="idInquilino"
                                    required
                                >

                                    <?php
                                    
                                    while($cliente = $clientes->fetch_assoc())
                                    {

                                        $selected = ($cliente['idInquilino'] == $visita['idInquilino']) ? "selected" : "";

                                        ?>

                                        <option 
                                            value="<?= $cliente['idInquilino']; ?>"
                                            <?= $selected; ?>
                                        >

                                            <?= $cliente['NombreCompleto']; ?>

                                        </option>

                                        <?php

                                    }

                                    ?>

                                </select>

                            </div>

                            <!-- FECHA -->
                            <div class="input-group">

                                <label>
                                    Fecha de la Visita
                                </label>

                                <input
                                    type="date"
                                    name="fechaVisita"
                                    value="<?= $fecha; ?>"
                                    required
                                >

                            </div>

                            <!-- HORA -->
                            <div class="input-group">

                                <label>
                                    Hora de la Visita
                                </label>

                                <input
                                    type="time"
                                    name="horaVisita"
                                    value="<?= $hora; ?>"
                                    required
                                >

                            </div>

                            <!-- ESTATUS -->
                            <div class="input-group">

                                <label>
                                    Estado de la Visita
                                </label>

                                <select 
                                    name="estatus"
                                    required
                                >

                                    <option value="Pendiente"
                                        <?= ($visita['Estatus'] == 'Pendiente') ? 'selected' : ''; ?>
                                    >
                                        Pendiente
                                    </option>

                                    <option value="En atencion"
                                        <?= ($visita['Estatus'] == 'En atencion') ? 'selected' : ''; ?>
                                    >
                                        En Atención
                                    </option>

                                    <option value="Atendida"
                                        <?= ($visita['Estatus'] == 'Atendida') ? 'selected' : ''; ?>
                                    >
                                        Atendida
                                    </option>

                                    <option value="Cancelada"
                                        <?= ($visita['Estatus'] == 'Cancelada') ? 'selected' : ''; ?>
                                    >
                                        Cancelada
                                    </option>

                                </select>

                            </div>

                            <!-- OBSERVACIONES -->
                            <div class="input-group full-width">

                                <label>
                                    Observaciones
                                </label>

                                <textarea 
                                    rows="5"
                                    name="observaciones"
                                ><?= $visita['Observaciones']; ?></textarea>

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="form-buttons">

                            <a 
                                href="Interface_Visitas.php"
                                class="btn-cancel"
                            >
                                Cancelar
                            </a>

                            <button 
                                type="submit"
                                class="btn-save"
                            >
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

    </script>

</body>

</html>