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
// FILTROS
// =============================================

$filtroEstado = $_GET['estado'] ?? "";
$filtroTipo = $_GET['tipo'] ?? "";
$busqueda = trim($_GET['buscar'] ?? "");

$whereSolicitudes = " WHERE 1 = 1 ";
$whereAdeudos = " WHERE 1 = 1 ";

// =============================================
// FILTRO ESTADO
// =============================================

if (!empty($filtroEstado)) {

    $estadoSeguro =
        $conn->real_escape_string($filtroEstado);

    $whereSolicitudes .= "
        AND EstadoSolicitud = '$estadoSeguro'
    ";

    $whereAdeudos .= "
        AND Estado = '$estadoSeguro'
    ";

}

// =============================================
// FILTRO TIPO
// =============================================

if (!empty($filtroTipo)) {

    $tipoSeguro =
        $conn->real_escape_string($filtroTipo);

    $whereSolicitudes .= "
        AND TipoPropiedad = '$tipoSeguro'
    ";

    $whereAdeudos .= "
        AND TipoPropiedad = '$tipoSeguro'
    ";

}

// =============================================
// BÚSQUEDA
// =============================================

if (!empty($busqueda)) {

    $buscarSeguro =
        $conn->real_escape_string($busqueda);

    $filtroBusqueda = "

        AND (

            NombreInquilino
            LIKE '%$buscarSeguro%'

            OR ApellidoP
            LIKE '%$buscarSeguro%'

            OR NumeroIdentificador
            LIKE '%$buscarSeguro%'

        )

    ";

    $whereSolicitudes .= $filtroBusqueda;
    $whereAdeudos .= $filtroBusqueda;

}

// =============================================
// MENSAJES
// =============================================

$mensaje = "";
$tipoMensaje = "";

// =============================================
// APROBAR ABONO
// =============================================

if (
    $_SERVER['REQUEST_METHOD'] === "POST" &&
    isset($_POST['aprobar_abono'])
) {

    $idSolicitud = intval($_POST['idSolicitud']);

    try {

        // =============================================
        // OBTENER DATOS SOLICITUD
        // =============================================

        $stmtDatos = $conn->prepare("

            SELECT

                sa.idContrato,
                sa.MontoSolicitado,
                sa.EstadoSolicitud

            FROM Solicitudes_Abono sa

            WHERE sa.idSolicitud = ?

            LIMIT 1

        ");

        if (!$stmtDatos) {

            throw new Exception(
                "Error al obtener solicitud: " .
                $conn->error
            );

        }

        $stmtDatos->bind_param(
            "i",
            $idSolicitud
        );

        $stmtDatos->execute();

        $stmtDatos->store_result();

        if ($stmtDatos->num_rows <= 0) {

            throw new Exception(
                "La solicitud no existe"
            );

        }

        $stmtDatos->bind_result(

            $idContrato,
            $montoSolicitado,
            $estadoSolicitud

        );

        $stmtDatos->fetch();

        $stmtDatos->close();

        // =============================================
        // VALIDAR ESTADO
        // =============================================

        if (
            strtolower(trim($estadoSolicitud))
            != "pendiente"
        ) {

            throw new Exception(
                "La solicitud ya fue procesada"
            );

        }

        // =============================================
        // OBTENER TIENDA
        // =============================================

        $idTienda = 0;

        $stmtTienda = $conn->prepare("

            SELECT idTienda

            FROM Tiendas_Cobro

            ORDER BY idTienda ASC

            LIMIT 1

        ");

        if (!$stmtTienda) {

            throw new Exception(
                "Error al obtener tienda"
            );

        }

        $stmtTienda->execute();

        $stmtTienda->bind_result(
            $idTienda
        );

        $stmtTienda->fetch();

        $stmtTienda->close();

        if (empty($idTienda)) {

            throw new Exception(
                "No existen tiendas registradas"
            );

        }

        // =============================================
        // EJECUTAR PROCEDIMIENTO
        // =============================================

        $stmtProcedure = $conn->prepare("

            CALL sp_RegistrarAbono(
                ?, ?, ?, ?, ?
            )

        ");

        if (!$stmtProcedure) {

            throw new Exception(
                "Error al preparar procedure: " .
                $conn->error
            );

        }

        $stmtProcedure->bind_param(

            "iiiid",

            $idContrato,
            $idTienda,
            $idUsuario,
            $idSolicitud,
            $montoSolicitado

        );

        if (!$stmtProcedure->execute()) {

            throw new Exception(
                "Error al aprobar abono: " .
                $stmtProcedure->error
            );

        }

        $stmtProcedure->close();

        // =============================================
        // LIMPIAR RESULTADOS
        // =============================================

        while ($conn->more_results()) {

            $conn->next_result();

        }

        // =============================================
        // HISTORIAL
        // =============================================

        $stmtHistorial = $conn->prepare("

            INSERT INTO Historial_Aprobaciones_Abono(

                idSolicitud,
                idAdministrador,
                Accion,
                Comentario

            )

            VALUES(

                ?, ?,
                'Aprobado',
                'Solicitud aprobada correctamente'

            )

        ");

        if (!$stmtHistorial) {

            throw new Exception(
                "Error historial"
            );

        }

        $stmtHistorial->bind_param(

            "ii",

            $idSolicitud,
            $idUsuario

        );

        if (!$stmtHistorial->execute()) {

            throw new Exception(
                "Error historial: " .
                $stmtHistorial->error
            );

        }

        $stmtHistorial->close();

        header(
            "Location: Interface_Abonos.php?success=1"
        );

        exit();

    } catch (Exception $e) {

        $mensaje = $e->getMessage();

        $tipoMensaje = "error";

    }

}

// =============================================
// RECHAZAR ABONO
// =============================================

if (
    $_SERVER['REQUEST_METHOD'] === "POST" &&
    isset($_POST['cancelar_abono'])
) {

    $idSolicitud =
        intval($_POST['idSolicitud']);

    try {

        $stmt = $conn->prepare("

            UPDATE Solicitudes_Abono

            SET

                EstadoSolicitud = 'Rechazada',
                idAdministrador = ?,
                FechaRevision = NOW()

            WHERE idSolicitud = ?

        ");

        if (!$stmt) {

            throw new Exception(
                "Error al rechazar solicitud"
            );

        }

        $stmt->bind_param(
            "ii",
            $idUsuario,
            $idSolicitud
        );

        if (!$stmt->execute()) {

            throw new Exception(
                "No se pudo rechazar el abono: " .
                $stmt->error
            );

        }

        $stmt->close();

        // =============================================
        // HISTORIAL
        // =============================================

        $stmtHistorial = $conn->prepare("

            INSERT INTO
            Historial_Aprobaciones_Abono(

                idSolicitud,
                idAdministrador,
                Accion,
                Comentario

            )

            VALUES (?, ?, 'Rechazado', 'Solicitud rechazada')

        ");

        if (!$stmtHistorial) {

            throw new Exception(
                "Error al registrar historial"
            );

        }

        $stmtHistorial->bind_param(
            "ii",
            $idSolicitud,
            $idUsuario
        );

        $stmtHistorial->execute();

        $stmtHistorial->close();

        header("Location: Interface_Abonos.php?rechazado=1");
        exit();

    } catch (Exception $e) {

        $mensaje =
            $e->getMessage();

        $tipoMensaje = "error";

    }

}

// =============================================
// MENSAJES GET
// =============================================

if (isset($_GET['success'])) {

    $mensaje =
        "Abono aprobado y aplicado correctamente";

    $tipoMensaje = "success";

}

if (isset($_GET['rechazado'])) {

    $mensaje =
        "Abono rechazado correctamente";

    $tipoMensaje = "success";

}

// =============================================
// SOLICITUDES
// =============================================

$solicitudesAbono = [];

$sqlSolicitudes = "

    SELECT *

    FROM vw_SolicitudesAbono

    $whereSolicitudes

    ORDER BY FechaSolicitud DESC

";

$resultSolicitudes =
    $conn->query($sqlSolicitudes);

if ($resultSolicitudes) {

    while (
        $row =
        $resultSolicitudes->fetch_assoc()
    ) {

        $solicitudesAbono[] = $row;

    }

}

// =============================================
// HISTORIAL
// =============================================

$historialCobros = [];

$sqlHistorial = "

    SELECT *

    FROM vw_HistorialSolicitudesAbono

    ORDER BY FechaMovimiento DESC

    LIMIT 20

";

$resultHistorial =
    $conn->query($sqlHistorial);

if ($resultHistorial) {

    while (
        $row =
        $resultHistorial->fetch_assoc()
    ) {

        $historialCobros[] = $row;

    }

}

// =============================================
// ADEUDOS
// =============================================

$adeudosPendientes = [];

$sqlAdeudos = "

    SELECT *

    FROM vw_AdeudosPendientes

    $whereAdeudos

    ORDER BY FechaLimite ASC

";

$resultAdeudos =
    $conn->query($sqlAdeudos);

if ($resultAdeudos) {

    while (
        $row =
        $resultAdeudos->fetch_assoc()
    ) {

        $adeudosPendientes[] = $row;

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
        Control de Abonos
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

    .abonos-grid {

    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;

    }

    .abono-card {

        background: white;
        border-radius: 26px;
        padding: 25px;
        box-shadow: var(--shadow);
        transition: .3s ease;

    }

    .abono-card:hover {

        transform: translateY(-5px);

    }

    .abono-header {

        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;

    }

    .tenant {

        display: flex;
        align-items: center;
        gap: 15px;

    }

    .tenant img {

        width: 70px;
        height: 70px;
        border-radius: 18px;
        object-fit: cover;

    }

    .tenant-info h3 {

        font-size: 18px;
        margin-bottom: 5px;

    }

    .tenant-info p {

        font-size: 14px;
        color: var(--text-muted);

    }

    .status {

        padding: 8px 15px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;

    }

    .pending {

        background: #fef3c7;
        color: #92400e;

    }

    .approved {

        background: #dcfce7;
        color: #166534;

    }

    .rejected {

        background: #fee2e2;
        color: #991b1b;

    }

    .abono-info {

        margin-bottom: 20px;

    }

    .abono-info p {

        margin-bottom: 12px;
        color: var(--text-muted);
        font-size: 14px;

    }

    /* =========================================
    BOTONES
    ========================================= */

    .card-actions {

        display: flex;
        gap: 14px;
        margin-top: 28px;

    }

    .btn-custom {

        flex: 1;

        height: 40px;

        width: 120px;

        border: none;

        border-radius: 999px;

        cursor: pointer;

        font-size: 15px;

        font-weight: 600;

        letter-spacing: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        transition:
            background .2s ease,
            transform .15s ease;

        box-shadow:
            0 4px 10px rgba(0,0,0,0.06);

    }

    .btn-custom:hover {

        transform: translateY(-1px);

    }

    .btn-custom:active {

        transform: scale(.98);

    }

    .btn-primary {

        background: #d1d5db;
        color: #111111;

    }

    .btn-danger {

        background: #111111;
        color: white;

    }

    /* =========================================
    TABLA HISTORIAL
    ========================================= */

    .history-table {

        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--shadow);

    }

    .history-table th {

        background: black;
        color: white;
        text-align: left;
        padding: 18px;

    }

    .history-table td {

        padding: 18px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;

    }

    .history-table tr:hover {

        background: #fafafa;

    }

    /* =========================================
    BADGES Y MENSAJES
    ========================================= */

    .badge {

        background: black;
        color: white;
        border-radius: 30px;
        padding: 6px 14px;
        font-size: 12px;
        margin-left: 10px;

    }

    .message {

        padding: 15px;
        border-radius: 14px;
        margin-bottom: 20px;
        font-weight: 600;

    }

    .success {

        background: #dcfce7;
        color: #166534;

    }

    .error {

        background: #fee2e2;
        color: #991b1b;

    }

    /* =========================================
    RESPONSIVE
    ========================================= */

    @media (max-width: 1200px) {

        .abonos-grid {

            grid-template-columns: repeat(2, 1fr);

        }

    }

    @media (max-width: 900px) {

        .abonos-grid {

            grid-template-columns: 1fr;

        }

    }

    @media (max-width: 600px) {

        .card-actions {

            flex-direction: column;

        }

    }

    </style>

</head>

<body>

<div class="overlay" id="overlay"></div>

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
                    alt="Almacén"
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

        <div class="logout">

            <a href="../../includes/logout.php">

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

        <header class="top-bar">

            <div>

                <h1>
                    Control de Abonos
                </h1>

                <p class="subtitle">
                    Gestión de solicitudes de abono.
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

                <div class="logged-user">

                    <img
                        src="<?php echo $imagenUsuario; ?>"
                        alt="Usuario"
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

        <!-- MENSAJES -->

        <?php if (!empty($mensaje)): ?>

            <div class="message <?php echo $tipoMensaje; ?>">

                <?php echo htmlspecialchars($mensaje); ?>

            </div>

        <?php endif; ?>

        <!-- FILTROS -->

        <section class="search-section">

            <form method="GET" id="filterForm">

                <div class="filters">

                    <div class="filter-group">

                        <label>
                            Tipo de propiedad
                        </label>

                        <select name="tipo" id="tipo">

                            <option value="">
                                Todas
                            </option>

                            <option
                                value="Casa"
                                <?php echo ($filtroTipo == "Casa") ? "selected" : ""; ?>
                            >
                                Casa
                            </option>

                            <option
                                value="Local Comercial"
                                <?php echo ($filtroTipo == "Local Comercial") ? "selected" : ""; ?>
                            >
                                Local Comercial
                            </option>

                            <option
                                value="Edificio"
                                <?php echo ($filtroTipo == "Edificio") ? "selected" : ""; ?>
                            >
                                Edificio
                            </option>

                        </select>

                    </div>

                    <div class="filter-group">

                        <label>
                            Estado
                        </label>

                        <select name="estado" id="estado">

                            <option value="">
                                Todos
                            </option>

                            <option
                                value="Pendiente"
                                <?php echo ($filtroEstado == "Pendiente") ? "selected" : ""; ?>
                            >
                                Pendiente
                            </option>

                            <option
                                value="Aprobada"
                                <?php echo ($filtroEstado == "Aprobada") ? "selected" : ""; ?>
                            >
                                Aprobada
                            </option>

                            <option
                                value="Rechazada"
                                <?php echo ($filtroEstado == "Rechazada") ? "selected" : ""; ?>
                            >
                                Rechazada
                            </option>

                        </select>

                    </div>

                    <div class="search-input-wrapper">

                        <input
                            type="text"
                            name="buscar"
                            id="buscar"
                            placeholder="Buscar inquilino..."
                            value="<?php echo htmlspecialchars($busqueda); ?>"
                        >

                        <a
                            href="Interface_Agregar_Abonos.php"
                            class="btn-search"
                        >

                            <img
                                src="../images/icons/Agregar.png"
                                alt="Agregar"
                                class="button-icon"
                            >

                        </a>

                        <a
                            href="Interface_Tiendas.php"
                            class="btn-search"
                        >

                            <img
                                src="../images/icons/Tiendas.png"
                                alt="Tiendas"
                                class="button-icon"
                            >

                        </a>

                    </div>

                </div>

            </form>

        </section>

        <!-- SOLICITUDES -->

        <section>

            <div class="section-header">

                <h2>

                    Solicitudes de Abono

                    <span class="badge">

                        <?php echo count($solicitudesAbono); ?>

                    </span>

                </h2>

            </div>

            <div class="abonos-grid">

                <?php if (!empty($solicitudesAbono)): ?>

                    <?php foreach ($solicitudesAbono as $solicitud): ?>

                        <?php

                            $nombreInquilino =
                                $solicitud['NombreInquilino'] . " " .
                                $solicitud['ApellidoP'] . " " .
                                $solicitud['ApellidoM'];

                            $imagenInquilino =
                                !empty($solicitud['ImagenInquilino'])
                                ? "../images/person/" . $solicitud['ImagenInquilino']
                                : "../images/icons/Usuario.png";

                           $estado =
                                strtolower(trim($solicitud['EstadoSolicitud']));

                            $classEstado = "pending";

                            if (
                                $estado == "aprobada"
                            ) {

                                $classEstado = "approved";

                            }

                            if (
                                $estado == "rechazada"
                            ) {

                                $classEstado = "rejected";

                            }

                        ?>

                        <div 
                            class="abono-card"

                            data-tipo="<?php echo strtolower($solicitud['TipoPropiedad']); ?>"

                            data-estado="<?php echo strtolower($solicitud['EstadoSolicitud']); ?>"

                            data-busqueda="<?php echo strtolower(
                                $nombreInquilino . ' ' .
                                $solicitud['NumeroIdentificador']
                            ); ?>"
                        >

                            <div class="abono-header">

                                <div class="tenant">

                                    <img
                                        src="<?php echo htmlspecialchars($imagenInquilino); ?>"
                                        alt="Usuario"
                                    >

                                    <div class="tenant-info">

                                        <h3>

                                            <?php
                                                echo htmlspecialchars(
                                                    $nombreInquilino
                                                );
                                            ?>

                                        </h3>

                                        <p>

                                            <?php
                                                echo htmlspecialchars(
                                                    $solicitud['TipoPropiedad']
                                                );
                                            ?>

                                            #

                                            <?php
                                                echo htmlspecialchars(
                                                    $solicitud['NumeroIdentificador']
                                                );
                                            ?>

                                        </p>

                                    </div>

                                </div>

                                <span class="status <?php echo $classEstado; ?>">

                                    <?php
                                        echo htmlspecialchars(
                                            $solicitud['EstadoSolicitud']
                                        );
                                    ?>

                                </span>

                            </div>

                            <div class="abono-info">

                                <p>

                                    <strong>
                                        Teléfono:
                                    </strong>

                                    <?php
                                        echo htmlspecialchars(
                                            $solicitud['Telefono']
                                        );
                                    ?>

                                </p>

                                <p>

                                    <strong>
                                        Monto solicitado:
                                    </strong>

                                    $<?php
                                        echo number_format(
                                            $solicitud['MontoSolicitado'],
                                            2
                                        );
                                    ?>

                                </p>

                                <p>

                                    <strong>
                                        Monto pendiente:
                                    </strong>

                                    $<?php
                                        echo number_format(
                                            $solicitud['MontoPendiente'],
                                            2
                                        );
                                    ?>

                                </p>

                                <p>

                                    <strong>
                                        Fecha solicitud:
                                    </strong>

                                    <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime(
                                                $solicitud['FechaSolicitud']
                                            )
                                        );
                                    ?>

                                </p>

                            </div>

                            <?php if (
                                $solicitud['EstadoSolicitud']
                                == "Pendiente"
                            ): ?>

                                <div class="card-actions">

                                    <!-- APROBAR -->

                                    <form method="POST" style="flex:1;">

                                        <input
                                            type="hidden"
                                            name="idSolicitud"
                                            value="<?php echo $solicitud['idSolicitud']; ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="aprobar_abono"
                                            class="btn-custom btn-primary"
                                        >
                                            Aprobar
                                        </button>

                                    </form>

                                    <!-- CANCELAR -->

                                    <form method="POST" style="flex:1;">

                                        <input
                                            type="hidden"
                                            name="idSolicitud"
                                            value="<?php echo $solicitud['idSolicitud']; ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="cancelar_abono"
                                            class="btn-custom btn-danger"
                                        >
                                            Rechazar
                                        </button>

                                    </form>

                                </div>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>
                        No hay solicitudes registradas.
                    </p>

                <?php endif; ?>

            </div>

        </section>

        <!-- HISTORIAL -->

        <section style="margin-top:40px;">

            <div class="section-header">

                <h2>

                    Historial de Solicitudes

                    <span class="badge">

                        <?php echo count($historialCobros); ?>

                    </span>

                </h2>

            </div>

            <table class="history-table">

                <thead>

                    <tr>

                        <th>Inquilino</th>
                        <th>Propiedad</th>
                        <th>Monto</th>
                        <th>Acción</th>
                        <th>Administrador</th>
                        <th>Fecha</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($historialCobros)): ?>

                        <?php foreach ($historialCobros as $historial): ?>

                            <tr>

                                <td>

                                    <?php
                                        echo htmlspecialchars(
                                            $historial['NombreInquilino']
                                            . " " .
                                            $historial['ApellidoP']
                                        );
                                    ?>

                                </td>

                                <td>

                                    <?php
                                        echo htmlspecialchars(
                                            $historial['TipoPropiedad']
                                        );
                                    ?>

                                    #

                                    <?php
                                        echo htmlspecialchars(
                                            $historial['NumeroIdentificador']
                                        );
                                    ?>

                                </td>

                                <td>

                                    $<?php
                                        echo number_format(
                                            $historial['MontoSolicitado'],
                                            2
                                        );
                                    ?>

                                </td>

                                <td>

                                    <?php
                                        echo htmlspecialchars(
                                            $historial['Accion']
                                        );
                                    ?>

                                </td>

                                <td>

                                    <?php
                                        echo htmlspecialchars(
                                            $historial['NombreAdministrador']
                                            . " " .
                                            $historial['ApellidoAdministrador']
                                        );
                                    ?>

                                </td>

                                <td>

                                    <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime(
                                                $historial['FechaMovimiento']
                                            )
                                        );
                                    ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6">

                                No hay historial registrado.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </section>

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

<script>

    // =====================================
    // SIDEBAR
    // =====================================

    const sidebar =
        document.getElementById('sidebar');

    const brandToggle =
        document.getElementById('brandToggle');

    function toggleSidebar() {

        sidebar.classList.toggle(
            'collapsed'
        );

        overlay.classList.toggle(
            'active'
        );

    }

    if (brandToggle) {

        brandToggle.addEventListener(
            'click',
            toggleSidebar
        );

    }

    // =====================================
    // FILTROS EN TIEMPO REAL
    // =====================================

    const filtroTipo =
        document.getElementById('tipo');

    const filtroEstado =
        document.getElementById('estado');

    const inputBuscar =
        document.getElementById('buscar');

    const tarjetas =
        document.querySelectorAll('.abono-card');

    function filtrarTarjetas() {

        const tipo =
            filtroTipo.value.toLowerCase();

        const estado =
            filtroEstado.value.toLowerCase();

        const buscar =
            inputBuscar.value.toLowerCase();

        tarjetas.forEach((card) => {

            const cardTipo =
                card.dataset.tipo;

            const cardEstado =
                card.dataset.estado;

            const cardBusqueda =
                card.dataset.busqueda;

            let mostrar = true;

            // ============================
            // FILTRO TIPO
            // ============================

            if (
                tipo !== "" &&
                cardTipo !== tipo
            ) {

                mostrar = false;

            }

            // ============================
            // FILTRO ESTADO
            // ============================

            if (
                estado !== "" &&
                cardEstado !== estado
            ) {

                mostrar = false;

            }

            // ============================
            // FILTRO BUSQUEDA
            // ============================

            if (
                buscar !== "" &&
                !cardBusqueda.includes(buscar)
            ) {

                mostrar = false;

            }

            // ============================
            // MOSTRAR / OCULTAR
            // ============================

            if (mostrar) {

                card.style.display = "block";

            } else {

                card.style.display = "none";

            }

        });

    }

    // =====================================
    // EVENTOS
    // =====================================

    filtroTipo.addEventListener(
        'change',
        filtrarTarjetas
    );

    filtroEstado.addEventListener(
        'change',
        filtrarTarjetas
    );

    inputBuscar.addEventListener(
        'keyup',
        filtrarTarjetas
    );

// =========================================
// ELEMENTOS NOTIFICACIONES
// =========================================

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

// =========================================
// ABRIR MODAL
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

    }
);

// =========================================
// CERRAR MODAL
// =========================================

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

    }
);

// =========================================
// CERRAR CON OVERLAY
// =========================================

overlay.addEventListener(
    'click',
    () =>
    {

        overlay.classList.remove(
            'active'
        );

        notificationsModal.classList.remove(
            'active'
        );

    }
);

</script>

</body>

</html>