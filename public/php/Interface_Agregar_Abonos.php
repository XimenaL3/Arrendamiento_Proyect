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

// =========================================
// LIMPIAR RESULTADOS MYSQL
// =========================================

function limpiarResultados($conn)
{

    while ($conn->more_results()) {

        $conn->next_result();

        if ($resultado = $conn->store_result()) {

            $resultado->free();

        }

    }

}

// =========================================
// FORMULARIOS
// =========================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $accion = trim($_POST['accion'] ?? '');

    // =====================================
    // REGISTRAR PAGO COMPLETO
    // =====================================

    if ($accion === "pago") {

        $idContrato =
            intval($_POST['idContrato'] ?? 0);

        $idTienda =
            intval($_POST['idTienda'] ?? 0);

        $monto =
            floatval($_POST['monto'] ?? 0);

        if (
            $idContrato <= 0 ||
            $idTienda <= 0 ||
            $monto <= 0
        ) {

            $mensaje =
                "Completa todos los campos del pago";

            $tipoMensaje = "error";

        } else {

            try {

                limpiarResultados($conn);

                $stmt = $conn->prepare("
                    CALL sp_RegistrarCobro(
                        ?, ?, ?, ?
                    )
                ");

                $stmt->bind_param(
                    "iiid",
                    $idContrato,
                    $idTienda,
                    $idUsuario,
                    $monto
                );

                if (!$stmt->execute()) {

                    throw new Exception(
                        $stmt->error
                    );

                }

                do {

                    if (
                        $result =
                        $stmt->get_result()
                    ) {

                        $result->free();

                    }

                } while (
                    $stmt->more_results() &&
                    $stmt->next_result()
                );

                $stmt->close();

                limpiarResultados($conn);

                $mensaje =
                    "Pago registrado correctamente";

                $tipoMensaje = "success";

            } catch (Exception $e) {

                $mensaje =
                    "Error al registrar pago: " .
                    $e->getMessage();

                $tipoMensaje = "error";

            }

        }

    }

    // =====================================
    // SOLICITAR ABONO
    // =====================================

    if ($accion === "solicitar_abono") {

        $idContrato =
            intval($_POST['idContratoAbono'] ?? 0);

        $montoSolicitado =
            floatval($_POST['montoSolicitado'] ?? 0);

        $observaciones =
            trim($_POST['observaciones'] ?? '');

        $idInquilino = 0;

        // =================================
        // OBTENER ID INQUILINO
        // =================================

        $stmtInquilino = $conn->prepare("
            SELECT idInquilino
            FROM ContratosArrendamiento
            WHERE idContrato = ?
            LIMIT 1
        ");

        $stmtInquilino->bind_param(
            "i",
            $idContrato
        );

        $stmtInquilino->execute();

        $stmtInquilino->bind_result(
            $idInquilino
        );

        $stmtInquilino->fetch();

        $stmtInquilino->close();

        if (
            $idContrato <= 0 ||
            $idInquilino <= 0 ||
            $montoSolicitado <= 0
        ) {

            $mensaje =
                "Completa todos los campos de la solicitud";

            $tipoMensaje = "error";

        } else {

            try {

                limpiarResultados($conn);

                $stmt = $conn->prepare("
                    CALL sp_SolicitarAbono(
                        ?, ?, ?, ?, ?
                    )
                ");

                $stmt->bind_param(
                    "iiids",
                    $idUsuario,
                    $idContrato,
                    $idInquilino,
                    $montoSolicitado,
                    $observaciones
                );

                if (!$stmt->execute()) {

                    throw new Exception(
                        $stmt->error
                    );

                }

                do {

                    if (
                        $result =
                        $stmt->get_result()
                    ) {

                        $result->free();

                    }

                } while (
                    $stmt->more_results() &&
                    $stmt->next_result()
                );

                $stmt->close();

                limpiarResultados($conn);

                $mensaje =
                    "Solicitud de abono registrada correctamente";

                $tipoMensaje = "success";

            } catch (Exception $e) {

                $mensaje =
                    "Error al registrar solicitud: " .
                    $e->getMessage();

                $tipoMensaje = "error";

            }

        }

    }

}

// =========================================
// CONTRATOS
// =========================================

$contratos = [];

$sqlContratos = "
    SELECT
        ca.idContrato,
        i.idInquilino,
        p.Nombre,
        p.ApellidoP,
        pr.TipoPropiedad,
        pr.NumeroIdentificador
    FROM ContratosArrendamiento ca
    INNER JOIN Inquilinos i
        ON i.idInquilino = ca.idInquilino
    INNER JOIN Personas p
        ON p.idPersona = i.idPersona
    INNER JOIN Propiedades pr
        ON pr.idPropiedad = ca.idPropiedad
    ORDER BY ca.idContrato DESC
";

$resultContratos = $conn->query($sqlContratos);

while ($row = $resultContratos->fetch_assoc()) {

    $contratos[] = $row;

}

// =========================================
// TIENDAS
// =========================================

$tiendas = [];

$sqlTiendas = "
    SELECT *
    FROM Tiendas_Cobro
    ORDER BY NombreTienda ASC
";

$resultTiendas = $conn->query($sqlTiendas);

while ($row = $resultTiendas->fetch_assoc()) {

    $tiendas[] = $row;

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
        Pagos y Abonos
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <link
        rel="stylesheet"
        href="../css/Estilo_Edicion.css"
    >

    <style>

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

        .hidden {

            display: none;

        }

        .notification-badge:empty {

            display: none;

        }

        .notificaciones-panel {

            position: absolute;
            top: 60px;
            right: 0;
            width: 350px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.15);
            padding: 15px;
            z-index: 999;
            display: none;

        }

        .notificaciones-panel.active {

            display: block;

        }

        .notificacion-item {

            border-bottom: 1px solid #e5e7eb;
            padding: 10px 0;

        }

        .notificacion-item:last-child {

            border-bottom: none;

        }

        .notificacion-item strong {

            display: block;
            margin-bottom: 5px;

        }

    </style>

</head>

<body>

<div class="overlay" id="overlay"></div>

<div class="container">

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

    <main class="main-content">

        <header class="top-bar">

            <div>

                <h1>
                    Pagos y Abonos
                </h1>

                <p class="subtitle">
                    Gestiona pagos completos y solicitudes de abono.
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

        <?php if (!empty($mensaje)): ?>

            <div class="message <?php echo $tipoMensaje; ?>">

                <?php
                echo htmlspecialchars($mensaje);
                ?>

            </div>

        <?php endif; ?>

        <section class="edit-section">

            <div class="edit-card">

                <form
                    method="POST"
                    class="edit-form"
                >

                    <div class="input-group">

                        <label>
                            Acción
                        </label>

                        <select
                            name="accion"
                            id="accion"
                            required
                        >

                            <option value="pago">
                                Registrar Pago
                            </option>

                            <option value="solicitar_abono">
                                Solicitar Abono
                            </option>

                        </select>

                    </div>

                    <!-- PAGO -->

                    <div id="pagoFields">

                        <div class="form-grid">

                            <div class="input-group">

                                <label>
                                    Contrato
                                </label>

                                <select
                                    name="idContrato"
                                >

                                    <option value="">
                                        Selecciona contrato
                                    </option>

                                    <?php foreach ($contratos as $contrato): ?>

                                        <option
                                            value="<?php echo $contrato['idContrato']; ?>"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $contrato['Nombre']
                                                . " "
                                                . $contrato['ApellidoP']
                                                . " - "
                                                . $contrato['TipoPropiedad']
                                                . " #"
                                                . $contrato['NumeroIdentificador']
                                            );

                                            ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="input-group">

                                <label>
                                    Tienda
                                </label>

                                <select
                                    name="idTienda"
                                >

                                    <option value="">
                                        Selecciona tienda
                                    </option>

                                    <?php foreach ($tiendas as $tienda): ?>

                                        <option
                                            value="<?php echo $tienda['idTienda']; ?>"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $tienda['NombreTienda']
                                            );
                                            ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="input-group">

                                <label>
                                    Monto
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    name="monto"
                                >

                            </div>

                        </div>

                    </div>

                    <!-- SOLICITUD ABONO -->

                    <div
                        id="solicitudFields"
                        class="hidden"
                    >

                        <div class="form-grid">

                            <div class="input-group">

                                <label>
                                    Contrato
                                </label>

                                <select
                                    name="idContratoAbono"
                                >

                                    <option value="">
                                        Selecciona contrato
                                    </option>

                                    <?php foreach ($contratos as $contrato): ?>

                                        <option
                                            value="<?php echo $contrato['idContrato']; ?>"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $contrato['Nombre']
                                                . " "
                                                . $contrato['ApellidoP']
                                            );

                                            ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="input-group">

                                <label>
                                    Monto solicitado
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    name="montoSolicitado"
                                >

                            </div>

                            <div class="input-group">

                                <label>
                                    Observaciones
                                </label>

                                <textarea
                                    name="observaciones"
                                ></textarea>

                            </div>

                        </div>

                    </div>

                    <div class="form-buttons">

                        <button
                            type="reset"
                            class="btn-cancel"
                        >

                            Limpiar

                        </button>

                        <button
                            type="submit"
                            class="btn-save"
                        >

                            Guardar

                        </button>

                    </div>

                </form>

            </div>

        </section>

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

        <!-- FOOTER -->
        <footer class="footer">

            <p>

                © 2026 DiamondsCorporation.
                Todos los derechos reservados.

            </p>

        </footer>

    </main>

</div>

<script>

    // =====================================
    // SIDEBAR
    // =====================================

    const sidebar =
        document.getElementById('sidebar');

    const brandToggle =
        document.getElementById('brandToggle')

    function toggleSidebar() {

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

    // =====================================
    // FORMULARIOS
    // =====================================

    const accion =
        document.getElementById('accion');

    const pagoFields =
        document.getElementById(
            'pagoFields'
        );

    const solicitudFields =
        document.getElementById(
            'solicitudFields'
        );

    function actualizarFormulario() {

        pagoFields.classList.add(
            'hidden'
        );

        solicitudFields.classList.add(
            'hidden'
        );

        if (
            accion.value === 'pago'
        ) {

            pagoFields.classList.remove(
                'hidden'
            );

        }

        if (
            accion.value === 'solicitar_abono'
        ) {

            solicitudFields.classList.remove(
                'hidden'
            );

        }

    }

    accion.addEventListener(
        'change',
        actualizarFormulario
    );

    actualizarFormulario();

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