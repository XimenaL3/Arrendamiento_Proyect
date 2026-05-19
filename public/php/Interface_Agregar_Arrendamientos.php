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
REGISTRAR PROPIEDAD
========================================= */

$mensaje = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $tipoPropiedad = $_POST['tipo_propiedad'];
    $numeroIdentificador = $_POST['numero_identificador'];
    $descripcion = $_POST['descripcion'];
    $estadoFisico = $_POST['estado_fisico'];
    $estadoDisponibilidad = $_POST['estado_disponibilidad'];

    /* =========================================
    IMAGEN
    ========================================= */

    // Imagen por defecto
    $rutaImagen = "images/properties/default-building.jpg";

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0)
    {
        // Carpeta donde se guardarán las imágenes
        $directorio = "../../images/properties/";

        // Crear carpeta si no existe
        if(!is_dir($directorio))
        {
            mkdir($directorio, 0777, true);
        }

        // Obtener extensión
        $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);

        // Crear nombre único
        $nombreImagen = time() . "_" . uniqid() . "." . $extension;

        // Ruta física del archivo
        $rutaDestino = $directorio . $nombreImagen;

        // Ruta que se guardará en la BD
        $rutaImagenBD = "images/properties/" . $nombreImagen;

        // Mover imagen
        if(move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino))
        {
            $rutaImagen = $rutaImagenBD;
        }
    }

    /* =========================================
    PROCEDIMIENTO ALMACENADO
    ========================================= */

    $sql = "CALL sp_RegistrarPropiedad(?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssss",
        $tipoPropiedad,
        $numeroIdentificador,
        $descripcion,
        $estadoFisico,
        $estadoDisponibilidad,
        $rutaImagen
    );

    if(mysqli_stmt_execute($stmt))
    {
        header("Location: Interface_Arrendamientos.php");
        exit();
    }
    else
    {
        echo "Error al actualizar: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar Arrendamiento</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-badge {

            display: inline-block;

            padding: 8px 14px;

            border-radius: 10px;

            background: #dcfce7;

            color: #166534;

            font-size: 13px;

            font-weight: 600;

        }

        .section-subtitle {

            margin-top: 30px;

            margin-bottom: 20px;

            font-size: 18px;

            color: #374151;

            font-weight: 600;

        }

        .upload-box {

            border: 2px dashed #cbd5e1;

            border-radius: 14px;

            padding: 30px;

            text-align: center;

            background: #f8fafc;

            transition: 0.3s;

        }

        .upload-box:hover {

            border-color: #3b82f6;

            background: #eff6ff;

        }

        .upload-box p {

            color: #64748b;

            margin-top: 10px;

        }

        .message {

            width: 100%;

            padding: 14px;

            margin-bottom: 20px;

            border-radius: 12px;

            background: #dcfce7;

            color: #166534;

            font-weight: bold;

            text-align: center;

        }

        .file-input {

            margin-top: 15px;

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
                        Agregar Arrendamiento
                    </h1>

                    <p class="subtitle">
                        Registra nuevos locales comerciales, casas o edificios.
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

                    <?php if($mensaje != ""): ?>

                        <div class="message">
                            <?php echo $mensaje; ?>
                        </div>

                    <?php endif; ?>

                    <!-- FORMULARIO -->
                    <form class="edit-form" method="POST" enctype="multipart/form-data">

                        <h3 class="section-subtitle">
                            Información General
                        </h3>

                        <div class="form-grid">

                            <!-- IDENTIFICADOR -->
                            <div class="input-group">

                                <label>
                                    Número Identificador
                                </label>

                                <input 
                                    type="text"
                                    name="numero_identificador"
                                    placeholder="Ejemplo: Local #12"
                                    required
                                >

                            </div>

                            <!-- TIPO -->
                            <div class="input-group">

                                <label>
                                    Tipo de Propiedad
                                </label>

                                <select name="tipo_propiedad" required>

                                    <option selected disabled>
                                        Selecciona una opción
                                    </option>

                                    <option value="Local comercial">
                                        Local Comercial
                                    </option>

                                    <option value="Casa">
                                        Casa
                                    </option>

                                    <option value="Edificio">
                                        Edificio
                                    </option>

                                </select>

                            </div>

                            <!-- DESCRIPCION -->
                            <div class="input-group full-width">

                                <label>
                                    Descripción
                                </label>

                                <textarea 
                                    name="descripcion"
                                    rows="5"
                                    placeholder="Describe la propiedad..."
                                    required
                                ></textarea>

                            </div>

                            <!-- ESTADO FISICO -->
                            <div class="input-group">

                                <label>
                                    Estado Físico
                                </label>

                                <select name="estado_fisico" required>

                                    <option value="Buenas condiciones">
                                        Buenas condiciones
                                    </option>

                                    <option value="Malas condiciones">
                                        Malas condiciones
                                    </option>

                                    <option value="En mantenimiento">
                                        En mantenimiento
                                    </option>

                                </select>

                            </div>

                            <!-- DISPONIBILIDAD -->
                            <div class="input-group">

                                <label>
                                    Estado de Disponibilidad
                                </label>

                                <select name="estado_disponibilidad" required>

                                    <option value="Disponible">
                                        Disponible
                                    </option>

                                    <option value="Rentado">
                                        Rentado
                                    </option>

                                    <option value="Aspecto Legal">
                                        Aspecto Legal
                                    </option>

                                </select>

                            </div>

                        </div>

                        <h3 class="section-subtitle">
                            Imagen de la Propiedad
                        </h3>

                        <div class="form-grid">

                            <div class="input-group full-width">

                                <label>
                                    Subir Imagen
                                </label>

                                <div class="upload-box">

                                    <img 
                                        src="../images/icons/Agregar.png"
                                        alt="Upload"
                                        width="50"
                                    >

                                    <p>
                                        Selecciona una imagen para la propiedad
                                    </p>

                                    <input 
                                        type="file"
                                        name="imagen"
                                        class="file-input"
                                        accept="image/*"
                                    >

                                </div>

                            </div>

                        </div>

                        <!-- BOTONES -->
                        <div class="form-buttons">

                            <button type="reset" class="btn-cancel">
                                Limpiar
                            </button>

                            <button type="submit" class="btn-save">
                                Guardar Arrendamiento
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