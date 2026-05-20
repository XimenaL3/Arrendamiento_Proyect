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

$mensaje = "";

/* ==============================
OBTENER USUARIOS
============================== */

$sqlUsuarios = 
"SELECT DISTINCT
    i.idInquilino,

    CONCAT(
        p.Nombre, ' ',
        p.ApellidoP, ' ',
        IFNULL(p.ApellidoM,'')
    ) AS NombreCompleto

FROM Inquilinos i

INNER JOIN Personas p
    ON p.idPersona = i.idPersona

INNER JOIN ContratosArrendamiento c
    ON c.idInquilino = i.idInquilino

WHERE c.FechaFin >= CURDATE()

ORDER BY NombreCompleto ASC
";

$inquilinos = mysqli_query($conn, $sqlUsuarios);

if(
    isset($_POST['idInquilino']) &&
    !isset($_POST['titulo'])
)
{
    $idInquilino = $_POST['idInquilino'];

    $sql = "
    SELECT 
        p.idPropiedad,

        CONCAT(
            p.TipoPropiedad,
            ' #',
            p.NumeroIdentificador
        ) AS Propiedad

    FROM ContratosArrendamiento c

    INNER JOIN Propiedades p
        ON p.idPropiedad = c.idPropiedad

    WHERE c.idInquilino = ?
    AND c.FechaFin >= CURDATE()
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $idInquilino);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $propiedades = [];

    while($fila = $resultado->fetch_assoc())
    {
        $propiedades[] = $fila;
    }

    echo json_encode($propiedades);

    exit();
}

/* ==============================
OBTENER PROPIEDADES
============================== */

$sqlPropiedades = "SELECT
                        idPropiedad,
                        CONCAT(
                            TipoPropiedad,
                            ' #',
                            NumeroIdentificador
                        ) AS Propiedad
                    FROM Propiedades
                    ORDER BY TipoPropiedad ASC";

$propiedades = mysqli_query($conn, $sqlPropiedades);

/* ==============================
REGISTRAR REPORTE
============================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idInquilino  = $_POST['idInquilino'];
    $idPropiedad  = $_POST['idPropiedad'];

    $titulo       = $_POST['titulo'];
    $descripcion  = $_POST['descripcion'];

    $tipoReporte  = $_POST['tipoReporte'];
    $prioridad    = $_POST['prioridad'];

    $evidencia = "";

    // SUBIR ARCHIVO
    if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] == 0) {

        $carpeta = "../images/reports/";

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = time() . "_" . basename($_FILES['evidencia']['name']);
        $rutaDestino = $carpeta . $nombreArchivo;

        if (move_uploaded_file($_FILES['evidencia']['tmp_name'], $rutaDestino)) {
            $evidencia = $nombreArchivo;
        }
    }

    // DEBUG (IMPORTANTE PARA VER ERRORES)
    if (!$conn->query("SET @debug=1")) {}

    $stmt = $conn->prepare("CALL sp_RegistrarReporte(?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Error en prepare: " . $conn->error);
    }

    $stmt->bind_param(
        "iisssss",
        $idInquilino,
        $idPropiedad,
        $titulo,
        $descripcion,
        $tipoReporte,
        $prioridad,
        $evidencia
    );

    if ($stmt->execute()) {

        header("Location: Interface_Reportes.php");
        exit();

    } else {

        die("Error al ejecutar SP: " . $stmt->error);
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Crear Reporte
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-badge{

            display: inline-block;

            padding: 8px 14px;

            border-radius: 12px;

            background: #fef3c7;

            color: #92400e;

            font-size: 13px;

            font-weight: 600;

        }

        .section-subtitle{

            margin-top: 30px;

            margin-bottom: 20px;

            font-size: 18px;

            color: #111827;

            font-weight: 700;

        }

        .upload-box{

            border: 2px dashed #d1d5db;

            border-radius: 18px;

            padding: 35px;

            text-align: center;

            background: #f9fafb;

            transition: .3s ease;

        }

        .upload-box:hover{

            border-color: #111827;

            background: #f3f4f6;

        }

        .upload-box p{

            margin-top: 12px;

            color: #6b7280;

            font-size: 14px;

        }

        .priority-box{

            display: flex;

            gap: 14px;

            flex-wrap: wrap;

        }

        .priority-card{

            flex: 1;

            min-width: 150px;

            padding: 18px;

            border-radius: 16px;

            border: 1px solid #e5e7eb;

            background: #f9fafb;

            cursor: pointer;

            transition: .3s ease;

        }

        .priority-card:hover{

            transform: translateY(-3px);

        }

        .priority-card h4{

            margin-bottom: 8px;

            font-size: 15px;

            color: #111827;

        }

        .priority-card p{

            font-size: 13px;

            color: #6b7280;

        }

        .priority-high{

            border-left: 5px solid #dc2626;

        }

        .priority-medium{

            border-left: 5px solid #f59e0b;

        }

        .priority-low{

            border-left: 5px solid #10b981;

        }

        .hidden-radio{

            display: none;

        }

        .priority-selected{

            border: 2px solid black;

            background: #ececec;

        }

        .alerta{

            margin-bottom: 20px;

            padding: 15px;

            border-radius: 12px;

            font-weight: 600;

        }

        .success{

            background: #dcfce7;

            color: #166534;

        }

        .error{

            background: #fee2e2;

            color: #991b1b;

        }

    </style>

</head>

<body>

    <!-- OVERLAY -->
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

            <!-- TOPBAR -->
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

            <!-- FORM -->
            <section class="edit-section">

                <div class="edit-card">

                    <?php if($mensaje != ""){ ?>

                        <div class="alerta success">

                            <?php echo $mensaje; ?>

                        </div>

                    <?php } ?>

                    <!-- FORM -->
                    <form 
                        class="edit-form"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <h3 class="section-subtitle">
                            Información General
                        </h3>

                        <div class="form-grid">

                            <!-- USUARIO -->
                            <div class="input-group">

                                <label>
                                    Inquilino
                                </label>

                                <select name="idInquilino" id="idInquilino" required>

                                    <option selected disabled>
                                        Selecciona un inquilino
                                    </option>

                                    <?php while($inquilino = mysqli_fetch_assoc($inquilinos)){ ?>

                                        <option value="<?php echo $inquilino['idInquilino']; ?>">

                                            <?php echo $inquilino['NombreCompleto']; ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- PROPIEDAD -->
                            <div class="input-group">

                                <label>
                                    Propiedad
                                </label>

                                <select name="idPropiedad" id="idPropiedad" required>

                                    <option selected disabled>
                                        Primero selecciona un inquilino
                                    </option>

                                </select>

                            </div>

                            <!-- TITULO -->
                            <div class="input-group">

                                <label>
                                    Título del Reporte
                                </label>

                                <input 
                                    type="text"
                                    name="titulo"
                                    required
                                    placeholder="Ejemplo: Fuga de agua"
                                >

                            </div>

                            <!-- TIPO -->
                            <div class="input-group">

                                <label>
                                    Tipo de Reporte
                                </label>

                                <select name="tipoReporte" required>

                                    <option selected disabled>
                                        Selecciona una opción
                                    </option>

                                    <option value="Mantenimiento">
                                        Mantenimiento
                                    </option>

                                    <option value="Cobranza">
                                        Cobranza
                                    </option>

                                    <option value="Legal">
                                        Legal
                                    </option>

                                    <option value="Inventario">
                                        Inventario
                                    </option>

                                    <option value="General">
                                        General
                                    </option>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Nivel de Prioridad
                        </h3>

                        <div class="priority-box">

                            <label class="priority-card priority-high">

                                <input 
                                    type="radio"
                                    name="prioridad"
                                    value="Alta"
                                    class="hidden-radio"
                                    required
                                >

                                <h4>
                                    Alta
                                </h4>

                                <p>
                                    Problemas urgentes.
                                </p>

                            </label>

                            <label class="priority-card priority-medium">

                                <input 
                                    type="radio"
                                    name="prioridad"
                                    value="Media"
                                    class="hidden-radio"
                                >

                                <h4>
                                    Media
                                </h4>

                                <p>
                                    Situaciones importantes.
                                </p>

                            </label>

                            <label class="priority-card priority-low">

                                <input 
                                    type="radio"
                                    name="prioridad"
                                    value="Baja"
                                    class="hidden-radio"
                                >

                                <h4>
                                    Baja
                                </h4>

                                <p>
                                    Solicitudes menores.
                                </p>

                            </label>

                        </div>

                        <h3 class="section-subtitle">
                            Descripción del Problema
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Detalles del Reporte
                                </label>

                                <textarea 
                                    rows="6"
                                    name="descripcion"
                                    required
                                    placeholder="Describe el problema..."
                                ></textarea>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Evidencias
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Subir Evidencia
                                </label>

                                <input 
                                    type="file"
                                    name="evidencia"
                                    accept="image/*"
                                >

                            </div>

                        </div>

                        <!-- BOTONES -->
                        <div class="form-buttons">

                            <button type="reset" class="btn-cancel">
                                Limpiar
                            </button>

                            <button type="submit" class="btn-save">
                                Registrar Reporte
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

        const priorityCards = document.querySelectorAll('.priority-card');

        const selectInquilino =
            document.getElementById('idInquilino');

        const selectPropiedad =
            document.getElementById('idPropiedad');

        priorityCards.forEach(card => {

            card.addEventListener('click', () => {

                priorityCards.forEach(c => {

                    c.classList.remove('priority-selected');

                });

                card.classList.add('priority-selected');

            });

        });

        /* ==============================
            CARGAR PROPIEDADES
            ============================== */

            selectInquilino.addEventListener('change', () =>
            {
                const idInquilino = selectInquilino.value;

                fetch(window.location.href,
                {
                    method: 'POST',

                    headers:
                    {
                        'Content-Type':
                        'application/x-www-form-urlencoded'
                    },

                    body: 'idInquilino=' + idInquilino
                })
                .then(response => response.json())
                .then(data =>
                {
                    selectPropiedad.innerHTML = '';

                    if(data.length <= 0)
                    {
                        selectPropiedad.innerHTML = `
                            <option disabled selected>
                                No tiene propiedades
                            </option>
                        `;

                        return;
                    }

                    if(data.length > 1)
                    {
                        selectPropiedad.innerHTML = `
                            <option disabled selected>
                                Selecciona una propiedad
                            </option>
                        `;
                    }

                    data.forEach(propiedad =>
                    {
                        const option =
                            document.createElement('option');

                        option.value =
                            propiedad.idPropiedad;

                        option.textContent =
                            propiedad.Propiedad;

                        selectPropiedad.appendChild(option);
                    });

                    // AUTOSELECCIONAR SI SOLO HAY 1

                    if(data.length === 1)
                    {
                        selectPropiedad.value =
                            data[0].idPropiedad;
                    }

                });

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