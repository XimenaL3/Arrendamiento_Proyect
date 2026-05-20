<?php

ob_start();

session_start();

if (
    !isset($_SESSION['idInquilino']) ||
    ($_SESSION['tipo_usuario']  ?? '') != 'inquilino'
) {

    header("Location: Login.php");
    exit();

}

$idInquilino = $_SESSION['idInquilino'];
$idPersona = $_SESSION['idPersona'];

// =============================================
// CONEXIÓN
// =============================================

require_once "../../includes/Conexion.php";

// =============================================
// VALIDAR CONEXIÓN
// =============================================

if (!$conn) {

    die("Error de conexión a la base de datos");

}

// =============================================
// DATOS DEL INQUILINO LOGUEADO
// =============================================

$stmt = $conn->prepare("
    SELECT 
        p.Nombre,
        p.ApellidoP,
        p.ApellidoM,
        p.Imagen
    FROM Inquilinos i
    INNER JOIN Personas p
        ON p.idPersona = i.idPersona
    WHERE i.idInquilino = ?
");

if (!$stmt) {

    die("Error al cargar datos del inquilino");

}

$stmt->bind_param("i", $idInquilino);

$stmt->execute();

$stmt->bind_result(
    $nombre,
    $apellidoP,
    $apellidoM,
    $imagen
);

$stmt->fetch();

$stmt->close();

// =============================================
// DATOS VISUALES
// =============================================

$rol = "Inquilino";

$nombreCompleto = trim(
    $nombre . " " .
    $apellidoP . " " .
    $apellidoM
);

$imagenUsuario = (!empty($imagen))
    ? "../images/person/" . $imagen
    : "../images/icons/Usuario.png";

// =============================================
// OBTENER ID INQUILINO
// =============================================

$idInquilino = 0;

$stmtInq = $conn->prepare("
    SELECT i.idInquilino
    FROM Inquilinos i
    INNER JOIN Personas p 
        ON p.idPersona = i.idPersona
    WHERE p.idPersona = ?
");

$stmtInq->bind_param("i", $idPersona);

$stmtInq->execute();

$stmtInq->bind_result($idInquilino);

$stmtInq->fetch();

$stmtInq->close();

// =============================================
// PROPIEDADES Y ADEUDOS
// =============================================

$propiedades = [];

$sqlPropiedades = "
    SELECT 
        aa.idAdeudo,
        aa.MontoTotal,
        aa.MontoPendiente,
        aa.FechaLimite,
        aa.Estado,
        aa.Propiedad,
        p.Imagen
    FROM vw_abonos_adeudos aa
    INNER JOIN Propiedades p
        ON p.idPropiedad = aa.idPropiedad
    WHERE aa.idInquilino = ?
    AND aa.MontoPendiente > 0
";

$stmtProp = $conn->prepare($sqlPropiedades);

$stmtProp->bind_param("i", $idInquilino);

$stmtProp->execute();

$resultProp = $stmtProp->get_result();

while ($row = $resultProp->fetch_assoc()) {

    $propiedades[] = $row;

}

$stmtProp->close();

// =============================================
// REPORTES
// =============================================

$reportes = [];

$sqlReportes = "
    SELECT *
    FROM vw_Reportes
    WHERE idInquilino = ?
    ORDER BY FechaRegistro DESC
";

$stmtRep = $conn->prepare($sqlReportes);

$stmtRep->bind_param("i", $idInquilino);

$stmtRep->execute();

$resultRep = $stmtRep->get_result();

while ($row = $resultRep->fetch_assoc()) {

    $reportes[] = $row;

}

$stmtRep->close();

// =============================================
// VISITAS
// =============================================

$visitas = [];

$sqlVisitas = "
    SELECT *
    FROM vw_VisitasCobranza
    WHERE idInquilino = ?
    ORDER BY FechaVisita DESC
";

$stmtVis = $conn->prepare($sqlVisitas);

$stmtVis->bind_param("i", $idInquilino);

$stmtVis->execute();

$resultVis = $stmtVis->get_result();

while ($row = $resultVis->fetch_assoc()) {

    $visitas[] = $row;

}

$stmtVis->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Resumen de Inquilino
    </title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        *{

            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;

        }

        body{

            background:#f3f3f3;
            color:#111;
            min-height:100vh;
            overflow:hidden;

        }

        .dashboard{

            display:grid;
            grid-template-columns: 420px 1fr;
            gap:0;
            min-height:100vh;

        }

        .left-panel{

            background:#0d0d0d;
            padding:28px;
            color:#fff;

            height:100vh;
            position:sticky;
            top:0;

            overflow-y:auto;

        }

        .left-panel::-webkit-scrollbar{

            width:6px;

        }

        .left-panel::-webkit-scrollbar-thumb{

            background:#444;
            border-radius:20px;

        }

        .right-panel{

            display:flex;
            flex-direction:column;
            gap:24px;
            padding:28px;

            height:100vh;
            overflow-y:auto;

        }

        .overview-title h1{

            font-size:36px;
            font-weight:800;
            margin-bottom:8px;

        }

        .overview-title p{

            color:#a1a1a1;
            font-size:14px;

        }

        .filter-box{

            margin-top:28px;
            margin-bottom:24px;

        }

        .filter-box select{

            width:100%;
            background:#1a1a1a;
            border:1px solid #2a2a2a;
            color:#fff;
            padding:14px 16px;
            border-radius:16px;
            outline:none;
            font-size:14px;

        }

        .reports-container{

            display:flex;
            flex-direction:column;
            gap:18px;

        }

        .report-card{

            background:#181818;
            border:1px solid #2a2a2a;
            border-radius:24px;
            padding:20px;
            transition:.3s ease;

        }

        .report-card:hover{

            transform:translateY(-3px);

        }

        .report-top{

            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;

        }

        .report-top h3{

            color:#fff;
            font-size:18px;
            font-weight:700;

        }

        .report-description{

            color:#b5b5b5;
            margin-top:14px;
            line-height:1.6;
            font-size:14px;

        }

        .report-meta{

            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
            margin-top:18px;

        }

        .meta-box{

            background:#202020;
            border-radius:16px;
            padding:14px;

        }

        .meta-box span{

            display:block;
            color:#8f8f8f;
            font-size:12px;
            margin-bottom:5px;

        }

        .meta-box strong{

            color:#fff;
            font-size:14px;

        }

        .report-image{

            width:100%;
            height:180px;
            object-fit:cover;
            border-radius:18px;
            margin-top:18px;
            cursor:pointer;

        }

        .badge{

            padding:8px 14px;
            border-radius:14px;
            font-size:12px;
            font-weight:700;
            white-space:nowrap;

        }

        .pendiente{

            background:#2d2d2d;
            color:#d4d4d4;

        }

        .atendido{

            background:#fff;
            color:#000;

        }

        .cancelado{

            background:#3a3a3a;
            color:#c5c5c5;

        }

        .top-right{

            display:flex;
            justify-content:flex-end;
            align-items:center;

        }

        .user-box{

            display:flex;
            align-items:center;
            gap:14px;
            background:#fff;
            border-radius:22px;
            padding:10px 18px;
            box-shadow:0 10px 25px rgba(0,0,0,.05);

        }

        .user-avatar{

            width:58px;
            height:58px;
            border-radius:50%;
            object-fit:cover;

        }

        .user-box small{

            color:#777;
            display:block;
            margin-bottom:4px;

        }

        .logout-btn{

            width:54px;
            height:54px;
            background:#fff;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;

            margin-right:14px;

            box-shadow:0 10px 25px rgba(0,0,0,.05);

        }

        .logout-btn img{

            width:22px;

        }

        .card{

            background:#fff;
            border-radius:30px;
            padding:24px;
            box-shadow:0 10px 25px rgba(0,0,0,.04);

        }

        .property-card{

            display:grid;
            grid-template-columns:280px 1fr;
            gap:26px;
            align-items:center;

        }

        .property-image{

            width:100%;
            height:250px;
            object-fit:cover;
            border-radius:24px;

        }

        .property-content h2{

            font-size:34px;
            font-weight:800;
            margin-bottom:10px;

        }

        .property-sub{

            color:#777;
            margin-bottom:24px;

        }

        .info-line{

            display:flex;
            justify-content:space-between;
            margin-bottom:15px;
            font-size:15px;

        }

        .progress-container{

            width:100%;
            height:12px;
            background:#e6e6e6;
            border-radius:30px;
            overflow:hidden;
            margin-top:10px;

        }

        .progress-bar{

            height:100%;
            background:#000;
            border-radius:30px;

        }

        .progress-text{

            margin-top:10px;
            color:#666;
            font-size:14px;

        }

        .section-title{

            font-size:25px;
            font-weight:800;
            margin-bottom:18px;

        }

        .table-wrapper{

            overflow-x:auto;

        }

        table{

            width:100%;
            border-collapse:collapse;

        }

        th{

            background:#f7f7f7;
            color:#666;
            text-align:left;
            padding:18px;
            font-size:14px;

        }

        td{

            padding:18px;
            border-top:1px solid #efefef;
            font-size:14px;

        }

        .visit-person{

            display:flex;
            align-items:center;
            gap:12px;

        }

        .visit-person img{

            width:48px;
            height:48px;
            border-radius:50%;
            object-fit:cover;

        }

        .image-modal{

            position:fixed;
            inset:0;
            background:rgba(0,0,0,.8);
            display:flex;
            justify-content:center;
            align-items:center;
            opacity:0;
            visibility:hidden;
            transition:.3s ease;
            z-index:999;

        }

        .image-modal.active{

            opacity:1;
            visibility:visible;

        }

        .image-modal img{

            width:80%;
            max-width:900px;
            border-radius:24px;

        }

        @media(max-width:1200px){

            .dashboard{

                grid-template-columns:1fr;

            }

            .left-panel{

                height:auto;

            }

        }

        @media(max-width:800px){

            .property-card{

                grid-template-columns:1fr;

            }

        }

    </style>

</head>

<body>

<div class="dashboard">

    <!-- PANEL IZQUIERDO -->

    <div class="left-panel">

        <div class="overview-title">

            <h1>
                Tus Reportes
            </h1>

            <p>
                Gestiona y consulta tus reportes en tiempo real
            </p>

        </div>

        <!-- FILTRO -->

        <div class="filter-box">

            <select id="filtroReportes">

                <option value="todos">
                    Todos los reportes
                </option>

                <option value="Pendiente">
                    Pendientes
                </option>

                <option value="En proceso">
                    En proceso
                </option>

                <option value="Finalizado">
                    Finalizados
                </option>

                <option value="Cancelado">
                    Cancelados
                </option>

            </select>

        </div>

        <!-- REPORTES -->

        <div class="reports-container" id="contenedorReportes">

            <?php foreach ($reportes as $reporte): ?>

                <?php

                    $claseEstado = "pendiente";

                    if ($reporte['Estado'] == 'Atendido') {

                        $claseEstado = "atendido";

                    }

                    if ($reporte['Estado'] == 'Cancelado') {

                        $claseEstado = "cancelado";

                    }

                ?>

                <div 
                    class="report-card"
                    data-estado="<?= htmlspecialchars($reporte['Estado']) ?>"
                >

                    <div class="report-top">

                        <h3>
                            <?= htmlspecialchars($reporte['Titulo']) ?>
                        </h3>

                        <span class="badge <?= $claseEstado ?>">

                            <?= htmlspecialchars($reporte['Estado']) ?>

                        </span>

                    </div>

                    <p class="report-description">

                        <?= htmlspecialchars($reporte['Descripcion']) ?>

                    </p>

                    <div class="report-meta">

                        <div class="meta-box">

                            <span>
                                Tipo
                            </span>

                            <strong>
                                <?= htmlspecialchars($reporte['TipoReporte']) ?>
                            </strong>

                        </div>

                        <div class="meta-box">

                            <span>
                                Prioridad
                            </span>

                            <strong>
                                <?= htmlspecialchars($reporte['Prioridad']) ?>
                            </strong>

                        </div>

                    </div>

                    <img 
                        src="../images/reports/<?= htmlspecialchars($reporte['Evidencia']) ?>"
                        class="report-image"
                        onclick="openModal(this.src)"
                    >

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <!-- PANEL DERECHO -->

    <div class="right-panel">

        <div class="top-right">

            <a href="Login.php" class="logout-btn">

                <img 
                    src="../images/icons/Cerrar_Oscuro.png"
                    alt="Cerrar sesión"
                >

            </a>

            <div class="user-box">

                <img 
                    src="<?= htmlspecialchars($imagenUsuario) ?>"
                    class="user-avatar"
                >

                <div>

                    <small>
                        <?= htmlspecialchars($rol) ?>
                    </small>

                    <strong>
                        <?= htmlspecialchars($nombreCompleto) ?>
                    </strong>

                </div>

            </div>

        </div>

        <!-- PROPIEDADES -->

        <?php foreach ($propiedades as $propiedad): ?>

            <?php

                $pagado = 0;

                if ($propiedad['MontoTotal'] > 0) {

                    $pagado = (
                        ($propiedad['MontoTotal'] - $propiedad['MontoPendiente'])
                        / $propiedad['MontoTotal']
                    ) * 100;

                }

            ?>

            <div class="card property-card">

                <img 
                    src="../../<?= htmlspecialchars($propiedad['Imagen']) ?>"
                    class="property-image"
                >

                <div class="property-content">

                    <h2>
                        <?= htmlspecialchars($propiedad['Propiedad']) ?>
                    </h2>

                    <p class="property-sub">
                        Estado de renta y progreso de pago
                    </p>

                    <div class="info-line">

                        <span>
                            Deuda inicial
                        </span>

                        <strong>
                            $<?= number_format($propiedad['MontoTotal'], 2) ?>
                        </strong>

                    </div>

                    <div class="info-line">

                        <span>
                            Deuda actual
                        </span>

                        <strong>
                            $<?= number_format($propiedad['MontoPendiente'], 2) ?>
                        </strong>

                    </div>

                    <div class="info-line">

                        <span>
                            Fecha límite
                        </span>

                        <strong>
                            <?= date("d/m/Y", strtotime($propiedad['FechaLimite'])) ?>
                        </strong>

                    </div>

                    <div class="progress-container">

                        <div 
                            class="progress-bar"
                            style="width:<?= $pagado ?>%;"
                        ></div>

                    </div>

                    <p class="progress-text">

                        <?= round($pagado) ?>% pagado

                    </p>

                </div>

            </div>

        <?php endforeach; ?>

        <!-- VISITAS -->

        <div class="card">

            <h2 class="section-title">
                Visitas programadas
            </h2>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Persona asignada
                            </th>

                            <th>
                                Observaciones
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($visitas as $visita): ?>

                            <?php

                                $estadoVisita = "pendiente";

                                if ($visita['Estatus'] == 'Confirmada') {

                                    $estadoVisita = "atendido";

                                }

                                if ($visita['Estatus'] == 'Cancelada') {

                                    $estadoVisita = "cancelado";

                                }

                                $imagenCobrador = !empty($visita['ImagenCobrador'])
                                    ? "../images/person/" . $visita['ImagenCobrador']
                                    : "../images/icons/Usuario.png";

                            ?>

                            <tr>

                                <td>

                                    <div class="visit-person">

                                        <img src="<?= htmlspecialchars($imagenCobrador) ?>">

                                        <div>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $visita['NombreCobrador'] . ' ' .
                                                    $visita['ApellidoPCobrador']
                                                ) ?>

                                            </strong>

                                            <br>

                                            <small>

                                                <?= htmlspecialchars($visita['NombreRol']) ?>

                                            </small>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <?= htmlspecialchars($visita['Observaciones']) ?>

                                </td>

                                <td>

                                    <?= date("d/m/Y", strtotime($visita['FechaVisita'])) ?>

                                </td>

                                <td>

                                    <span class="badge <?= $estadoVisita ?>">

                                        <?= htmlspecialchars($visita['Estatus']) ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- FOOTER -->
        <footer class="footer">

            <p>

                © 2026 DiamondsCorporation.
                Todos los derechos reservados.

            </p>

        </footer>

    </div>

</div>

<!-- MODAL -->

<div class="image-modal" id="imageModal">

    <img id="modalImage">

</div>

<script>

    const imageModal = document.getElementById('imageModal');

    const modalImage = document.getElementById('modalImage');

    function openModal(src){

        modalImage.src = src;

        imageModal.classList.add('active');

    }

    imageModal.addEventListener('click', () => {

        imageModal.classList.remove('active');

    });

    // =========================================
    // FILTRO EN TIEMPO REAL
    // =========================================

    const filtroReportes = document.getElementById('filtroReportes');

    const reportCards = document.querySelectorAll('.report-card');

    filtroReportes.addEventListener('change', function(){

        const valor = this.value;

        reportCards.forEach(card => {

            const estado = card.dataset.estado;

            if(valor === 'todos'){

                card.style.display = 'block';

            }else{

                if(estado === valor){

                    card.style.display = 'block';

                }else{

                    card.style.display = 'none';

                }

            }

        });

    });

</script>

</body>
</html>