<?php

ob_start();
session_start();

if (!isset($_SESSION['idUsuario']) || ($_SESSION['rol'] ?? 0) != 1) {
    header("Location: Login.php");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$idPersona = $_SESSION['idPersona'];

require_once "../../includes/Conexion.php";

if (!$conn) {
    die("Error de conexión a la base de datos");
}

/* ================================
   USUARIO LOGUEADO
================================ */
$stmt = $conn->prepare("
    SELECT p.Nombre, p.ApellidoP, p.ApellidoM, p.Imagen, r.NombreRol
    FROM Usuarios u
    INNER JOIN Personas p ON p.idPersona = u.idPersona
    INNER JOIN Roles r ON r.idRol = u.idRol
    WHERE u.idUsuario = ?
");

if ($stmt) {

    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellidoP, $apellidoM, $imagen, $rol);
    $stmt->fetch();
    $stmt->close();

    $nombre = trim($nombre ?? '');
    $apellidoP = trim($apellidoP ?? '');
    $apellidoM = trim($apellidoM ?? '');

    $imagenUsuario = (!empty($imagen))
        ? "../images/person/" . $imagen
        : "../images/icons/Usuario.png";

} else {
    $nombre = "Usuario";
    $apellidoP = "";
    $apellidoM = "";
    $imagenUsuario = "../images/icons/Usuario.png";
    $rol = "Sin rol";
}

$nombreCompleto = $nombre . " " . $apellidoP . " " . $apellidoM;


/* ================================
   NOTIFICACIONES
================================ */
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

$totalNotificaciones = ($row = $resultCount->fetch_assoc()) ? $row['total'] : 0;


/* ================================
   REPORTES (NUEVA VISTA)
================================ */
$sql = "SELECT * FROM vw_Reportes ORDER BY FechaRegistro DESC";
$resultado = $conn->query($sql);

$reportes = [];
$totalReportes = 0;
$pendientes = 0;
$Finalizado = 0;
$cancelados = 0;

if ($resultado && $resultado->num_rows > 0) {

    while ($row = $resultado->fetch_assoc()) {

        $reportes[] = $row;
        $totalReportes++;

        switch ($row['Estado'] ?? '') {
            case 'Pendiente':
                $pendientes++;
                break;
            case 'Finalizado':
                $Finalizado++;
                break;
            case 'Cancelado':
                $cancelados++;
                break;
        }
    }
}

/* =========================================
MANTENIMIENTOS
========================================= */

$mantenimientos = [];

$sqlMantenimiento = "
SELECT *
FROM vw_MantenimientoDetalle
";

$resultMantenimiento = $conn->query($sqlMantenimiento);

if(
    $resultMantenimiento &&
    $resultMantenimiento->num_rows > 0
){

    while($m = $resultMantenimiento->fetch_assoc()){

        $mantenimientos[$m['idReporte']][] = $m;

    }

}

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
        Gestión de Reportes
    </title>

    <!-- CSS -->
    <link 
        rel="stylesheet" 
        href="../css/style.css"
    >

    <style>

        .date-wrapper{

            position: relative;

        }

        .date-input{

            min-width: 220px;

            height: 52px;

            padding: 0 16px;

            border-radius: 14px;

            border: 1px solid var(--border);

            background: #fafafa;

            color: var(--text);

            font-size: 14px;

            outline: none;

            transition: 0.3s ease;

            cursor: pointer;

        }

        .reports-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));

            gap: 25px;

        }

        .report-card{

            background: white;

            border-radius: 26px;

            padding: 25px;

            box-shadow: var(--shadow);

            transition: 0.3s ease;

        }

        .report-card:hover{

            transform: translateY(-6px);

        }

        .report-header{

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            margin-bottom: 20px;

        }

        .report-user{

            display: flex;

            align-items: center;

            gap: 15px;

        }

        .report-user img{

            width: 68px;

            height: 68px;

            border-radius: 18px;

            object-fit: cover;

            border: 2px solid #e5e7eb;

        }

        .report-user h3{

            font-size: 18px;

            margin-bottom: 5px;

        }

        .report-user p{

            color: var(--text-muted);

            font-size: 14px;

        }

        .status{

            padding: 8px 14px;

            border-radius: 30px;

            font-size: 12px;

            font-weight: 700;

        }

        .pending{

            background: #fef3c7;

            color: #92400e;

        }

        .completed{

            background: #dcfce7;

            color: #166534;

        }

        .cancelled{

            background: #fee2e2;

            color: #991b1b;

        }

        .report-info{

            margin-bottom: 20px;

        }

        .report-info p{

            color: var(--text-muted);

            line-height: 1.6;

            margin-bottom: 12px;

        }

        .report-details{

            display: grid;

            grid-template-columns: repeat(2,1fr);

            gap: 15px;

            margin-top: 20px;

        }

        .detail-box{

            background: #f9fafb;

            border-radius: 16px;

            padding: 14px;

            border: 1px solid var(--border);

        }

        .detail-box span{

            display: block;

            font-size: 12px;

            color: var(--text-muted);

            margin-bottom: 6px;

        }

        .detail-box strong{

            font-size: 15px;

            color: var(--text);

        }

        .report-actions{

            display: flex;

            gap: 12px;

            margin-top: 24px;

        }

        .btn-report{

            flex: 1;

            border: none;

            padding: 13px;

            border-radius: 14px;

            cursor: pointer;

            font-weight: 600;

            transition: 0.3s ease;

            text-decoration: none;

            text-align: center;

        }

        .btn-download{

            background: black;

            color: white;

        }

        .btn-download:hover{

            background: #111827;

        }

        .btn-view{

            background: #f3f3f3;

            color: black;

        }

        .btn-view:hover{

            background: #e5e7eb;

        }

        .stats-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));

            gap: 20px;

            margin-bottom: 35px;

        }

        .stat-card{

            background: white;

            border-radius: 24px;

            padding: 24px;

            box-shadow: var(--shadow);

        }

        .stat-card h3{

            color: var(--text-muted);

            font-size: 14px;

            margin-bottom: 10px;

        }

        .stat-card strong{

            font-size: 30px;

            color: var(--text);

        }

        /* =========================================
        MODAL
        ========================================= */

        .modal{

            position: fixed;

            top: 50%;

            left: 50%;

            transform: translate(-50%, -50%) scale(0.8);

            width: 90%;

            max-width: 800px;

            background: white;

            border-radius: 25px;

            padding: 30px;

            z-index: 9999;

            opacity: 0;

            visibility: hidden;

            transition: .3s ease;

            max-height: 90vh;

            overflow-y: auto;

        }

        .modal.active{

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

            font-size: 24px;

        }

        .close-modal{

            background: #f3f4f6;

            border: none;

            width: 40px;

            height: 40px;

            border-radius: 50%;

            cursor: pointer;

            font-size: 18px;

        }

        .modal-image{

            width: 100%;

            max-height: 350px;

            object-fit: cover;

            border-radius: 20px;

            margin-bottom: 20px;

        }

        .modal-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));

            gap: 20px;

            margin-top: 20px;

        }

        .modal-box{

            background: #f9fafb;

            padding: 18px;

            border-radius: 18px;

            border: 1px solid #e5e7eb;

        }

        .modal-box span{

            display: block;

            color: #6b7280;

            margin-bottom: 8px;

            font-size: 13px;

        }

        .modal-box strong{

            font-size: 16px;

        }

        /* =========================================
        MANTENIMIENTO TARJETAS
        ========================================= */

        .mantenimiento-section{

            margin-top: 30px;

        }

        .mantenimiento-title{

            font-size: 22px;

            margin-bottom: 20px;

            font-weight: 700;

        }

        .mantenimiento-grid{

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));

            gap: 18px;

        }

        .mantenimiento-card{

            background: #f9fafb;

            border: 1px solid #e5e7eb;

            border-radius: 22px;

            padding: 20px;

            transition: .3s ease;

        }

        .mantenimiento-card:hover{

            transform: translateY(-4px);

        }

        .mantenimiento-card h4{

            font-size: 18px;

            margin-bottom: 15px;

            color: #111827;

        }

        .mantenimiento-item{

            margin-bottom: 14px;

        }

        .mantenimiento-item span{

            display: block;

            font-size: 12px;

            color: #6b7280;

            margin-bottom: 5px;

        }

        .mantenimiento-item strong{

            font-size: 15px;

            color: #111827;

            line-height: 1.5;

        }
        
    </style>

</head>

<body>

<div class="overlay" id="overlay"></div>

<!-- MODAL REPORTE -->
<div class="modal" id="modalReporte">

    <div class="modal-header">
        <h2 id="modalTitulo">Reporte</h2>
        <button class="close-modal" id="cerrarModal">✕</button>
    </div>

    <img src="" id="modalImagen" class="modal-image">

    <p id="modalDescripcion"></p>

    <div class="modal-grid">

        <div class="modal-box">
            <span>Usuario</span>
            <strong id="modalUsuario"></strong>
        </div>

        <div class="modal-box">
            <span>Propiedad</span>
            <strong id="modalPropiedad"></strong>
        </div>

        <div class="modal-box">
            <span>Tipo</span>
            <strong id="modalTipo"></strong>
        </div>

        <div class="modal-box">
            <span>Prioridad</span>
            <strong id="modalPrioridad"></strong>
        </div>

        <div class="modal-box">
            <span>Estado</span>
            <strong id="modalEstado"></strong>
        </div>

        <div class="modal-box">
            <span>Fecha</span>
            <strong id="modalFecha"></strong>
        </div>

    </div>
    <div id="contenedorMantenimiento"></div>
</div>

<div class="container">

<!-- SIDEBAR -->
 <aside class="sidebar collapsed" id="sidebar">

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

            <nav class="sidebar-nav">

                <a href="Interface_Trabajadores.php">

                    <img 
                        src="../images/icons/Trabajadores_Claro.png"
                        class="menu-icon"
                    >

                    <span>Trabajadores</span>

                </a>

                <a href="Interface_Clientes.php">

                    <img 
                        src="../images/icons/Clientes_Claro.png"
                        class="menu-icon"
                    >

                    <span>Clientes</span>

                </a>

                <a href="Interface_Visitas.php">

                    <img 
                        src="../images/icons/Visitas_Claro.png"
                        class="menu-icon"
                    >

                    <span>Visitas</span>

                </a>

                <a href="Interface_Arrendamientos.php">

                    <img 
                        src="../images/icons/Arrendamiento_Claro.png"
                        class="menu-icon"
                    >

                    <span>Arrendamientos</span>

                </a>

                <a href="Interface_Abonos.php">

                    <img 
                        src="../images/icons/Pago_Claro.png"
                        class="menu-icon"
                    >

                    <span>Abonos</span>

                </a>

                <a href="Interface_Productos_Limpieza.php">

                    <img 
                        src="../images/icons/Mantenimiento_Claro.png"
                        class="menu-icon"
                    >

                    <span>Almacén Limpieza</span>

                </a>

                <a href="Interface_Reportes.php" class="active">

                    <img 
                        src="../images/icons/Reportes_Oscuro.png"
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

<!-- TOP BAR-->
 <header class="top-bar">

                <div>

                    <h1>
                        Crear Nuevo Reporte
                    </h1>

                    <p class="subtitle">
                        Registra incidencias dentro del sistema.
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

<!-- FILTROS -->
<section class="search-section">

    <div class="filters">

        <div class="filter-group">
            <label>Estado</label>
            <select id="estadoFiltro">
                <option value="">Todos</option>
                <option value="Pendiente">Pendientes</option>
                <option value="En proceso">En proceso</option>
                <option value="Finalizado">Finalizado</option>
                <option value="Cancelado">Cancelados</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Fecha</label>
            <input type="date" class="date-input" id="fechaFiltro">
        </div>

        <div class="search-input-wrapper">
            <input type="text" placeholder="Buscar Reporte..." id="buscador">

            <a href="Interface_Agregar_Reporte.php" class="btn-search">
                <img src="../images/icons/Agregar.png" class="button-icon">
            </a>
        </div>

    </div>

</section>

<!-- ESTADÍSTICAS -->
<section class="workers-section">

<div class="stats-grid">

    <div class="stat-card">
        <h3>Pendientes</h3>
        <strong><?php echo $pendientes; ?></strong>
    </div>

    <div class="stat-card">
        <h3>Finalizados</h3>
        <strong><?php echo $Finalizado; ?></strong>
    </div>

    <div class="stat-card">
        <h3>Cancelados</h3>
        <strong><?php echo $cancelados; ?></strong>
    </div>

    <div class="stat-card">
        <h3>Totales</h3>
        <strong><?php echo $totalReportes; ?></strong>
    </div>

</div>

</section>

<!-- REPORTES -->
<section class="workers-section">

<div class="reports-grid" id="contenedorReportes">

    <?php foreach ($reportes as $reporte): ?>

    <div class="report-card"
        data-estado="<?php echo $reporte['Estado']; ?>"
        data-fecha="<?php echo date('Y-m-d', strtotime($reporte['FechaRegistro'])); ?>">

        <div class="report-header">

            <div class="report-user">

                <img src="<?php echo !empty($reporte['ImagenUsuario']) 
                    ? '../images/person/' . $reporte['ImagenUsuario'] 
                    : '../images/icons/Usuario.png'; ?>">

                <div>
                    <h3><?php echo $reporte['Titulo']; ?></h3>
                    <p>
                        <?php echo $reporte['NombreUsuario'] . ' ' . $reporte['ApellidoP']; ?>
                    </p>
                </div>

            </div>

            <div class="status <?php echo strtolower($reporte['Estado']); ?>">
                <?php echo $reporte['Estado']; ?>
            </div>

        </div>

        <div class="report-info">
            <p><?php echo $reporte['Descripcion']; ?></p>
        </div>

        <div class="report-details">

            <div class="detail-box">
                <span>Fecha</span>
                <strong><?php echo date("d/m/Y", strtotime($reporte['FechaRegistro'])); ?></strong>
            </div>

            <div class="detail-box">
                <span>Prioridad</span>
                <strong><?php echo $reporte['Prioridad']; ?></strong>
            </div>

        </div>

        <div class="report-actions">

            <button class="btn-report btn-view btnVerReporteInfo"
                data-idreporte="<?php echo $reporte['idReporte']; ?>"
                data-titulo="<?php echo htmlspecialchars($reporte['Titulo']); ?>"
                data-descripcion="<?php echo htmlspecialchars($reporte['Descripcion']); ?>"
                data-usuario="<?php echo htmlspecialchars($reporte['NombreUsuario'].' '.$reporte['ApellidoP']); ?>"
                data-propiedad="<?php echo htmlspecialchars($reporte['NumeroIdentificador']); ?>"
                data-tipo="<?php echo htmlspecialchars($reporte['TipoReporte']); ?>"
                data-prioridad="<?php echo htmlspecialchars($reporte['Prioridad']); ?>"
                data-estado="<?php echo htmlspecialchars($reporte['Estado']); ?>"
                data-fecha="<?php echo date('d/m/Y', strtotime($reporte['FechaRegistro'])); ?>">
                Ver Reporte
            </button>

            <button class="btn-report btn-download btnVerEvidencia"
                data-imagen="<?php echo !empty($reporte['Evidencia']) 
                    ? '../images/reports/' . $reporte['Evidencia'] 
                    : '../images/icons/Reportes_Oscuro.png'; ?>">
                Ver Evidencia
            </button>

        </div>

    </div>

    <?php endforeach; ?>

</div>

</section>

<!-- FOOTER -->
<footer class="footer">
    <p>© 2026 DiamondsCorporation. Todos los derechos reservados.</p>
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

<!-- ================= JS CORREGIDO ================= -->
<script>

const sidebar = document.getElementById('sidebar');
const brandToggle = document.getElementById('brandToggle');

const overlay = document.getElementById('overlay');

const modal = document.getElementById('modalReporte');
const cerrarModal = document.getElementById('cerrarModal');

const notificationsModal = document.getElementById('notificationsModal');

const notificationBtn = document.querySelector('.notification-wrapper');
const closeNotifications = document.getElementById('closeModal');

const imgModal = document.getElementById('modalImagen');
const descripcionModal = document.getElementById('modalDescripcion');
const gridModal = document.querySelector('.modal-grid');

/* =========================================
   FILTROS
========================================= */

const buscador = document.getElementById('buscador');

const estadoFiltro = document.getElementById('estadoFiltro');

const fechaFiltro = document.getElementById('fechaFiltro');

const reportCards = document.querySelectorAll('.report-card');

/* =========================================
   SIDEBAR
========================================= */

brandToggle.addEventListener('click', () => {

    sidebar.classList.toggle('collapsed');

});

/* =========================================
   OVERLAY
========================================= */

overlay.addEventListener('click', () => {

    cerrarTodo();

});

/* =========================================
   FUNCIÓN CERRAR TODO
========================================= */

function cerrarTodo() {

    modal.classList.remove('active');

    notificationsModal.classList.remove('active');

    overlay.classList.remove('active');

    imgModal.src = "";

    imgModal.style.display = "block";

    if (descripcionModal) {

        descripcionModal.style.display = "block";

    }

    if (gridModal) {

        gridModal.style.display = "grid";

    }

}

/* =========================================
   MODAL REPORTE
========================================= */

cerrarModal.addEventListener('click', cerrarTodo);

/* =========================================
   EVENTOS GENERALES
========================================= */

document.addEventListener('click', (e) => {

    /* =====================================
       VER INFORMACIÓN REPORTE
    ===================================== */

    if (e.target.classList.contains('btnVerReporteInfo')) {

        const btn = e.target;

        document.getElementById('modalTitulo').innerText =
            btn.dataset.titulo;

        document.getElementById('modalDescripcion').innerText =
            btn.dataset.descripcion;

        document.getElementById('modalUsuario').innerText =
            btn.dataset.usuario;

        document.getElementById('modalPropiedad').innerText =
            btn.dataset.propiedad;

        document.getElementById('modalTipo').innerText =
            btn.dataset.tipo;

        document.getElementById('modalPrioridad').innerText =
            btn.dataset.prioridad;

        document.getElementById('modalEstado').innerText =
            btn.dataset.estado;

        document.getElementById('modalFecha').innerText =
            btn.dataset.fecha;

        /* =====================================
        MANTENIMIENTOS
        ===================================== */

        const idReporte = btn.dataset.idreporte;

        const estadoReporte = btn.dataset.estado;

        const contenedor =
            document.getElementById(
                'contenedorMantenimiento'
            );

        contenedor.innerHTML = "";

        if(
            estadoReporte === "Finalizado" &&
            mantenimientosData[idReporte]
        ){

        let html = `

        <div class="mantenimiento-section">

            <h3 class="mantenimiento-title">
                Detalles de Mantenimiento
            </h3>

            <div class="mantenimiento-grid">

                <div class="mantenimiento-card">

                    <h4>
                        Productos Utilizados
                    </h4>

        `;

        mantenimientosData[idReporte].forEach(m => {

            html += `

                <div class="mantenimiento-item">

                    <span>
                        Producto
                    </span>

                    <strong>
                        ${m.NombreProducto ?? 'Sin producto'}
                    </strong>

                </div>

            `;

        });

        html += `

                    <div class="mantenimiento-item">

                        <span>
                            Tarea Realizada
                        </span>

                        <strong>
                            ${mantenimientosData[idReporte][0].TareaRealizada}
                        </strong>

                    </div>

                    <div class="mantenimiento-item">

                        <span>
                            Fecha Inicio
                        </span>

                        <strong>
                            ${mantenimientosData[idReporte][0].FechaInicio}
                        </strong>

                    </div>

                    <div class="mantenimiento-item">

                        <span>
                            Fecha Fin
                        </span>

                        <strong>
                            ${mantenimientosData[idReporte][0].FechaFin ?? 'Sin finalizar'}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

        `;

        html += `

                </div>

            </div>

        `;

        contenedor.innerHTML = html;

        }

        imgModal.style.display = "none";

        if (descripcionModal) {

            descripcionModal.style.display = "block";

        }

        if (gridModal) {

            gridModal.style.display = "grid";

        }

        modal.classList.add('active');

        overlay.classList.add('active');

    }

    /* =====================================
       VER EVIDENCIA
    ===================================== */

    if (e.target.classList.contains('btnVerEvidencia')) {

        const btn = e.target;

        document.getElementById('modalTitulo').innerText =
            "Evidencia";

        /* =====================================
        OCULTAR MANTENIMIENTOS
        ===================================== */

        document.getElementById(
            'contenedorMantenimiento'
        ).innerHTML = "";

        if (descripcionModal) {

            descripcionModal.style.display = "none";

        }

        if (gridModal) {

            gridModal.style.display = "none";

        }

        imgModal.style.display = "block";

        imgModal.src = btn.dataset.imagen;

        modal.classList.add('active');

        overlay.classList.add('active');

    }

});

/* =========================================
   MODAL NOTIFICACIONES
========================================= */

if (notificationBtn) {

    notificationBtn.addEventListener('click', () => {

        notificationsModal.classList.add('active');

        overlay.classList.add('active');

    });

}

if (closeNotifications) {

    closeNotifications.addEventListener('click', cerrarTodo);

}

/* =========================================
   FILTROS EN TIEMPO REAL
========================================= */

function filtrarReportes() {

    const texto =
        buscador.value.toLowerCase().trim();

    const estado =
        estadoFiltro.value.toLowerCase();

    const fecha =
        fechaFiltro.value;

    reportCards.forEach(card => {

        /* =============================
           CONTENIDO GENERAL
        ============================== */

        const contenido =
            card.innerText.toLowerCase();

        /* =============================
           DATASET
        ============================== */

        const estadoCard =
            card.dataset.estado.toLowerCase();

        const fechaCard =
            card.dataset.fecha;

        let visible = true;

        /* =============================
           FILTRO BUSCADOR
        ============================== */

        if (
            texto !== "" &&
            !contenido.includes(texto)
        ) {

            visible = false;

        }

        /* =============================
           FILTRO ESTADO
        ============================== */

        if (
            estado !== "" &&
            estado !== estadoCard
        ) {

            visible = false;

        }

        /* =============================
           FILTRO FECHA
        ============================== */

        if (
            fecha !== "" &&
            fecha !== fechaCard
        ) {

            visible = false;

        }

        /* =============================
           MOSTRAR / OCULTAR
        ============================== */

        card.style.display =
            visible
            ? "block"
            : "none";

    });

}

/* =========================================
   EVENTOS FILTROS
========================================= */

/* BUSCADOR TIEMPO REAL */

buscador.addEventListener(
    'input',
    filtrarReportes
);

/* FILTRO ESTADO */

estadoFiltro.addEventListener(
    'change',
    filtrarReportes
);

/* FILTRO FECHA */

fechaFiltro.addEventListener(
    'change',
    filtrarReportes
);


const mantenimientosData = <?php
echo json_encode(
    $mantenimientos,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);
?>;

</script>

</body>

</html>