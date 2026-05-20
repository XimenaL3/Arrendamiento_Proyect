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
OBTENER ID PROPIEDAD
========================================= */

$idPropiedad = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;

if($idPropiedad <= 0){

    die("Propiedad inválida");

}

/* =========================================
OBTENER PROPIEDAD SELECCIONADA
========================================= */

$sqlPropiedad = "
SELECT 
    *
FROM vw_Propiedades
WHERE idPropiedad = ?
AND EstadoDisponibilidad = 'Disponible'
";

$stmtPropiedad = $conn->prepare($sqlPropiedad);

$stmtPropiedad->bind_param("i", $idPropiedad);

$stmtPropiedad->execute();

$resultPropiedad = $stmtPropiedad->get_result();

if($resultPropiedad->num_rows <= 0){

    die("La propiedad no está disponible");

}

$propiedad = $resultPropiedad->fetch_assoc();

/* =========================================
OBTENER INQUILINOS
========================================= */

$sqlInquilinos = "
SELECT 
    i.idInquilino,
    CONCAT(p.Nombre,' ',p.ApellidoP,' ',p.ApellidoM) AS NombreCompleto,
    p.Telefono,
    p.Correo
FROM Inquilinos i
JOIN Personas p ON p.idPersona = i.idPersona
LEFT JOIN Usuarios u ON u.idPersona = p.idPersona
WHERE u.idRol IS NULL
ORDER BY NombreCompleto
";


$resultInquilinos = $conn->query($sqlInquilinos);

/* =========================================
OBTENER NOTIFICACIONES
========================================= */

$sqlNotificaciones = "
SELECT 
    *,
    TIMESTAMPDIFF(
        MINUTE,
        FechaNotificacion,
        NOW()
    ) AS MinutosTranscurridos
FROM Notificaciones
WHERE idUsuario = ?
ORDER BY FechaNotificacion DESC
LIMIT 10
";

$stmtNoti = $conn->prepare($sqlNotificaciones);

$stmtNoti->bind_param("i", $idUsuario);

$stmtNoti->execute();

$resultNoti = $stmtNoti->get_result();

/* =========================================
TOTAL NOTIFICACIONES
========================================= */

$sqlTotalNotificaciones = "
SELECT COUNT(*) AS total
FROM Notificaciones
WHERE idUsuario = ?
AND Estado = 'No leida'
";

$stmtTotal = $conn->prepare($sqlTotalNotificaciones);

$stmtTotal->bind_param("i", $idUsuario);

$stmtTotal->execute();

$resultTotal = $stmtTotal->get_result();

$filaTotal = $resultTotal->fetch_assoc();

$totalNotificaciones = $filaTotal['total'];

/* =========================================
MENSAJE
========================================= */

$mensaje = "";
$tipoMensaje = "";

/* =========================================
REGISTRAR RENTA
========================================= */

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $idInquilino = intval($_POST['idInquilino']);

    $fechaInicio = $_POST['fechaInicio'];

    $fechaFin = $_POST['fechaFin'];

    $montoRenta = floatval($_POST['montoRenta']);

    $montoDeposito = floatval($_POST['montoDeposito']);

    $observaciones = trim($_POST['observaciones']);

    $permitirAbonos = intval($_POST['permitirAbonos']);

    $nombreArchivo = "";

    $serviciosJSON = [];

if(isset($_POST['servicios'])){

    foreach($_POST['servicios'] as $idServicio => $datos){

        if(isset($datos['activo'])){

            $serviciosJSON[] = [

                "idServicio" => intval($idServicio),

                "ManejoPorPorcentaje" =>
                    intval($datos['manejo']),

                "PorcentajeAsignado" =>
                    $datos['porcentaje'] !== ''
                    ? floatval($datos['porcentaje'])
                    : 0,

                "CostoFijo" =>
                    $datos['costo'] !== ''
                    ? floatval($datos['costo'])
                    : 0

            ];

        }

    }

}

$serviciosJSON = json_encode($serviciosJSON);

    /* =========================================
    VALIDACIONES
    ========================================= */

    if(empty($fechaInicio) || empty($fechaFin)){

        $mensaje = "Debes ingresar las fechas";

        $tipoMensaje = "error";

    }
    else if($fechaFin <= $fechaInicio){

        $mensaje = "La fecha final debe ser mayor a la fecha inicial";

        $tipoMensaje = "error";

    }
    else{

        /* =========================================
        SUBIR ARCHIVO
        ========================================= */

        if(
            isset($_FILES['evidencia']) &&
            $_FILES['evidencia']['error'] == 0
        ){

            $carpeta = "../uploads/evidencias/";

            if(!file_exists($carpeta)){

                mkdir($carpeta, 0777, true);

            }

            $extension = strtolower(
                pathinfo(
                    $_FILES['evidencia']['name'],
                    PATHINFO_EXTENSION
                )
            );

            $extensionesPermitidas = [
                'jpg',
                'jpeg',
                'png',
                'pdf'
            ];

            if(in_array($extension, $extensionesPermitidas)){

                $nombreArchivo = time() . "_" .
                basename($_FILES['evidencia']['name']);

                $rutaFinal = $carpeta . $nombreArchivo;

                move_uploaded_file(
                    $_FILES['evidencia']['tmp_name'],
                    $rutaFinal
                );

            }else{

                $mensaje = "Formato de archivo no permitido";

                $tipoMensaje = "error";

            }

        }

        /* =========================================
        PROCEDIMIENTO ALMACENADO
        ========================================= */

        if($mensaje == ""){

            $stmt = $conn->prepare("
            CALL sp_RentarPropiedad(
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
            ");

            if($stmt){

                $stmt->bind_param(
                    "iissddsiss",
                    $idInquilino,
                    $idPropiedad,
                    $fechaInicio,
                    $fechaFin,
                    $montoRenta,
                    $montoDeposito,
                    $observaciones,
                    $permitirAbonos,
                    $nombreArchivo,
                    $serviciosJSON
                );

                if($stmt->execute()){

                    $resultado = $stmt->get_result();

                    if($resultado){

                        $fila = $resultado->fetch_assoc();

                        $mensaje = $fila['Resultado'];

                        if(
                            $mensaje ==
                            'Renta procesada exitosamente'
                        ){

                            $tipoMensaje = "success";

                        }else{

                            $tipoMensaje = "error";

                        }

                    }else{

                        $mensaje =
                        "Renta registrada correctamente";

                        $tipoMensaje = "success";

                    }

                }else{

                    $mensaje =
                    "Error al registrar: " . $stmt->error;

                    $tipoMensaje = "error";

                }

                $stmt->close();

                while(
                    $conn->more_results() &&
                    $conn->next_result()
                ){

                    $dummyResult = $conn->use_result();

                    if(
                        $dummyResult instanceof mysqli_result
                    ){

                        $dummyResult->free();

                    }

                }

            }else{

                $mensaje = "Error en la consulta";

                $tipoMensaje = "error";

            }

        }

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
        Registrar Arrendamiento
    </title>

    <!-- CSS -->
    <link 
        rel="stylesheet" 
        href="../css/style.css"
    >

    <link 
        rel="stylesheet" 
        href="../css/Estilo_Edicion.css"
    >

    <style>

        .status-badge{

            display:inline-block;
            padding:8px 14px;
            border-radius:12px;
            background:#f3f4f6;
            color:#111827;
            font-size:13px;
            font-weight:600;

        }

        .section-subtitle{

            margin-top:30px;
            margin-bottom:20px;
            font-size:18px;
            color:#1f2937;
            font-weight:700;

        }

        .upload-box{

            border:2px dashed #d1d5db;
            border-radius:16px;
            padding:30px;
            text-align:center;
            background:#f9fafb;

        }

        .upload-box input{

            width:100%;

        }

        .info-card{

            margin-top:30px;
            background:#ffffff;
            border-radius:20px;
            border:1px solid #e5e7eb;
            padding:22px;
            box-shadow:0 10px 25px rgba(0,0,0,.05);

        }

        .info-grid{

            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
            gap:18px;

        }

        .info-item{

            background:#f9fafb;
            border-radius:14px;
            padding:16px;
            border:1px solid #e5e7eb;

        }

        .info-item span{

            display:block;
            font-size:13px;
            color:#6b7280;
            margin-bottom:8px;

        }

        .success-message{

            background:#dcfce7;
            color:#166534;
            padding:15px;
            border-radius:12px;
            margin-bottom:20px;
            font-weight:600;

        }

        .error-message{

            background:#fee2e2;
            color:#991b1b;
            padding:15px;
            border-radius:12px;
            margin-bottom:20px;
            font-weight:600;

        }

        input:disabled{

            background:#e5e7eb;
            cursor:not-allowed;
            opacity:0.7;

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

                <a 
                    href="Interface_Arrendamientos.php" 
                    class="active"
                >

                    <img 
                        src="../images/icons/Arrendamiento_Oscuro.png"
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
                        Registrar Nueva Renta
                    </h1>

                    <p class="subtitle">
                        Registro completo del arrendamiento.
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

            <?php if($mensaje != ""){ ?>

                <div class="<?= $tipoMensaje == 'success' ? 'success-message' : 'error-message'; ?>">

                    <?= htmlspecialchars($mensaje); ?>

                </div>

            <?php } ?>

            <!-- FORM -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <?php

                        $imagenPropiedad = !empty($propiedad['Imagen'])
                            ? "../../" . $propiedad['Imagen']
                            : "../images/icons/Usuario.png";

                        ?>

                        <img 
                            src="<?= $imagenPropiedad; ?>"
                            alt="Propiedad"
                            class="edit-avatar"
                        >

                        <button 
                            type="button"
                            class="change-photo-btn"
                        >
                            Contrato de Renta
                        </button>

                        <div style="margin-top:15px;">

                            <span class="status-badge">
                                <?= $propiedad['EstadoDisponibilidad']; ?>
                            </span>

                        </div>

                    </div>

                    <!-- FORMULARIO -->
                    <form 
                        class="edit-form"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <h3 class="section-subtitle">
                            Información del Cliente
                        </h3>

                        <div class="form-grid">

                            <div class="input-group">

                                <label>
                                    Inquilino
                                </label>

                                <select 
                                    name="idInquilino"
                                    required
                                >

                                    <option value="">
                                        Selecciona un inquilino
                                    </option>

                                    <?php
                                    
                                    while($inquilino = $resultInquilinos->fetch_assoc())
                                    {

                                    ?>

                                    <option 
                                        value="<?= $inquilino['idInquilino']; ?>"
                                    >

                                        <?= htmlspecialchars($inquilino['NombreCompleto']); ?>

                                    </option>

                                    <?php } ?>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Información de la Propiedad
                        </h3>

                        <div class="form-grid">

                            <div class="input-group">

                                <label>
                                    Propiedad
                                </label>

                                <input 
                                    type="text"
                                    value="<?= htmlspecialchars($propiedad['NumeroIdentificador']); ?>"
                                    readonly
                                >

                            </div>

                            <div class="input-group">

                                <label>
                                    Tipo
                                </label>

                                <input 
                                    type="text"
                                    value="<?= htmlspecialchars($propiedad['TipoPropiedad']); ?>"
                                    readonly
                                >

                            </div>

                            <div class="input-group full-width">

                            <label>
                                Descripción
                            </label>

                            <textarea 
                                rows="4"
                                readonly
                            ><?= htmlspecialchars($propiedad['Descripcion']); ?></textarea>

                        </div>

                        </div>

                        <h3 class="section-subtitle">
                            Información de la Renta
                        </h3>

                        <div class="form-grid">

                            <div class="input-group">

                                <label>
                                    Renta Mensual
                                </label>

                                <input 
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="montoRenta"
                                    required
                                >

                            </div>

                            <div class="input-group">

                                <label>
                                    Depósito Inicial
                                </label>

                                <input 
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="montoDeposito"
                                    required
                                >

                            </div>

                            <div class="input-group">

                                <label>
                                    Fecha Inicio
                                </label>

                                <input 
                                    type="date"
                                    name="fechaInicio"
                                    required
                                >

                            </div>

                            <div class="input-group">

                                <label>
                                    Fecha Final
                                </label>

                                <input 
                                    type="date"
                                    name="fechaFin"
                                    required
                                >

                            </div>

                            <div class="input-group">

                                <label>
                                    Permitir Abonos
                                </label>

                                <select 
                                    name="permitirAbonos"
                                    required
                                >

                                    <option value="1">
                                        Sí
                                    </option>

                                    <option value="0">
                                        No
                                    </option>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Observaciones
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Detalles del Contrato
                                </label>

                                <textarea 
                                    name="observaciones"
                                    rows="5"
                                ></textarea>

                            </div>

                            <div class="input-group full-width">

                                <label>
                                    Subir Contrato
                                </label>

                                <div class="upload-box">

                                    <input 
                                        type="file"
                                        name="evidencia"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                    >

                                </div>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Servicios de la Propiedad
                        </h3>

                        <div class="form-grid">

                            <?php

                            $sqlServicios = "
                            SELECT *
                            FROM Servicios
                            ORDER BY NombreServicio ASC
                            ";

                            $resultServicios = $conn->query($sqlServicios);

                            while($servicio = $resultServicios->fetch_assoc()){

                            ?>

                            <div class="info-card">

                                <h4>
                                    <?= htmlspecialchars($servicio['NombreServicio']); ?>
                                </h4>

                                <input 
                                    type="checkbox"
                                    name="servicios[<?= $servicio['idServicio']; ?>][activo]"
                                    value="1"
                                >
                                Activar Servicio

                                <div class="input-group">

                                    <label>
                                        Manejo por porcentaje
                                    </label>

                                    <select
                                        class="manejo-select"
                                        name="servicios[<?= $servicio['idServicio']; ?>][manejo]"
                                    >

                                        <option value="1">
                                            Sí
                                        </option>

                                        <option value="0">
                                            No
                                        </option>

                                    </select>

                                </div>

                                <div class="input-group">

                                    <label>
                                        Porcentaje Asignado
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        class="input-porcentaje"
                                        name="servicios[<?= $servicio['idServicio']; ?>][porcentaje]"
                                    >

                                </div>

                                <div class="input-group">

                                    <label>
                                        Costo Fijo
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="input-costo"
                                        name="servicios[<?= $servicio['idServicio']; ?>][costo]"
                                    >

                                </div>

                            </div>

                            <?php } ?>

                        </div>

                        <!-- RESUMEN -->
                        <div class="info-card">

                            <h3>
                                Resumen del Arrendamiento
                            </h3>

                            <div class="info-grid">

                                <div class="info-item">

                                    <span>
                                        Tipo
                                    </span>

                                    <strong>
                                        <?= $propiedad['TipoPropiedad']; ?>
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Estado
                                    </span>

                                    <strong>
                                        <?= $propiedad['EstadoDisponibilidad']; ?>
                                    </strong>

                                </div>

                                <div class="info-item">

                                    <span>
                                        Estado Físico
                                    </span>

                                    <strong>
                                        <?= $propiedad['EstadoFisico']; ?>
                                    </strong>

                                </div>

                            </div>

                        </div>

                        <!-- BOTONES -->
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
                                Registrar Arrendamiento
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

                        <?php if($noti['Estado'] == 'No leida') { ?>

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

        /* ==============================
        SIDEBAR
        ============================== */

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

        /* ==============================
        MARCAR NOTIFICACION
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
    SERVICIOS PORCENTAJE / COSTO
    ============================== */

    document.querySelectorAll('.info-card').forEach(card =>
    {
        const selectManejo = card.querySelector('.manejo-select');

        const inputPorcentaje = card.querySelector('.input-porcentaje');

        const inputCosto = card.querySelector('.input-costo');

        function actualizarCampos()
        {
            if(selectManejo.value == "1")
            {
                // Manejo por porcentaje = SI

                inputPorcentaje.disabled = false;

                inputCosto.disabled = true;

                inputCosto.value = "";

            }
            else
            {
                // Manejo por porcentaje = NO

                inputCosto.disabled = false;

                inputPorcentaje.disabled = true;

                inputPorcentaje.value = "";
            }
        }

        // Ejecutar al cargar
        actualizarCampos();

        // Ejecutar al cambiar
        selectManejo.addEventListener(
            'change',
            actualizarCampos
        );
    });

    </script>

</body>

</html>