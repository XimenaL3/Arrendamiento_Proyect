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

        .main-content-clean {

            padding: 40px 60px;
            width: 100%;

        }

        .topbar-clean {

            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;

        }

        .topbar-clean h1 {

            font-size: 30px;
            font-weight: 800;

        }

        .muted {

            color: var(--text-muted);
            font-size: 13px;

        }

        .header-right {

            display: flex;
            align-items: center;
            gap: 14px;

        }

        .logout-circle {

            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow);
            cursor: pointer;

        }

        .logout-circle img {

            width: 22px;
            height: 22px;

        }

        .user-box {

            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--white);
            padding: 10px 16px;
            border-radius: 18px;
            box-shadow: var(--shadow);

        }

        .user-avatar {

            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;

        }

        .grid-clean {

            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;

        }

        .card-clean {

            background: var(--white);
            border-radius: 18px;
            padding: 18px;
            box-shadow: var(--shadow);

        }

        .property-img {

            width: 100%;
            height: 180px;
            border-radius: 18px;
            object-fit: cover;
            margin-bottom: 14px;

        }

        .info-row {

            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;

        }

        .progress-bar {

            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 12px;

        }

        .progress {

            height: 100%;
            background: #000;

        }

        .badge {

            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;

        }

        .pendiente {

            background: #fff7cd;
            color: #a16207;

        }

        .atendido {

            background: #dcfce7;
            color: #166534;

        }

        .cancelado {

            background: #fee2e2;
            color: #991b1b;

        }

        .section-title-clean {

            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: bold;
            margin: 35px 0 18px;

        }

        .section-title-clean img {

            width: 22px;

        }

        .report-description {

            margin-top: 10px;
            line-height: 1.6;
            color: var(--text-muted);

        }

        .report-meta {

            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;

        }

        .meta-box {

            background: #f8fafc;
            border-radius: 14px;
            padding: 10px 14px;
            flex: 1;

        }

        .evidence-box img {

            width: 100%;
            height: 170px;
            object-fit: cover;
            border-radius: 16px;
            margin-top: 15px;
            cursor: pointer;

        }

        .table-wrapper {

            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow);

        }

        table {

            width: 100%;
            border-collapse: collapse;

        }

        th {

            background: #f9fafb;
            padding: 16px;
            text-align: left;

        }

        td {

            padding: 16px;
            border-top: 1px solid #eee;

        }

        .visit-person {

            display: flex;
            align-items: center;
            gap: 12px;

        }

        .visit-person img {

            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;

        }

        .image-modal {

            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.75);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: .3s ease;
            z-index: 999;

        }

        .image-modal.active {

            opacity: 1;
            visibility: visible;

        }

        .image-modal img {

            width: 80%;
            max-width: 900px;
            border-radius: 20px;

        }

    </style>

</head>

<body>

<div class="main-content-clean">

    <!-- HEADER -->

    <div class="topbar-clean">

        <div>

            <h1>
                Resumen del Inquilino
            </h1>

            <p class="muted">
                Consulta tus propiedades, adeudos, reportes y visitas
            </p>

        </div>

        <div class="header-right">

            <a href="Login.php" class="logout-circle">

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

    </div>

    <!-- PROPIEDADES -->

    <div class="section-title-clean">

        <img src="../images/icons/Casa_Claro.png">

        Propiedades en renta

    </div>

    <div class="grid-clean">

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

            <div class="card-clean">

                <img 
                    src="../../<?= htmlspecialchars($propiedad['Imagen']) ?>"
                    class="property-img"
                >

                <h3>
                    <?= htmlspecialchars($propiedad['Propiedad']) ?>
                </h3>

                <div class="info-row">

                    <span>
                        Deuda inicial
                    </span>

                    <strong>
                        $<?= number_format($propiedad['MontoTotal'], 2) ?>
                    </strong>

                </div>

                <div class="info-row">

                    <span>
                        Deuda actual
                    </span>

                    <strong>
                        $<?= number_format($propiedad['MontoPendiente'], 2) ?>
                    </strong>

                </div>

                <div class="progress-bar">

                    <div 
                        class="progress"
                        style="width:<?= $pagado ?>%;"
                    ></div>

                </div>

                <p class="muted" style="margin-top:10px;">

                    <?= round($pagado) ?>% pagado

                </p>

                <div class="info-row">

                    <span>
                        Fecha límite
                    </span>

                    <strong>
                        <?= date("d/m/Y", strtotime($propiedad['FechaLimite'])) ?>
                    </strong>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <!-- REPORTES -->

    <div class="section-title-clean">

        <img src="../images/icons/Reporte_Claro.png">

        Reportes realizados

    </div>

    <div class="grid-clean">

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

            <div class="card-clean">

                <div style="display:flex;justify-content:space-between;gap:10px;">

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

                <div class="evidence-box">

                    <img 
                        src="../images/reports/<?= htmlspecialchars($reporte['Evidencia']) ?>"
                        onclick="openModal(this.src)"
                    >

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <!-- VISITAS -->

    <div class="section-title-clean">

        <img src="../images/icons/Calendario_Claro.png">

        Visitas programadas

    </div>

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

</script>

</body>
</html>