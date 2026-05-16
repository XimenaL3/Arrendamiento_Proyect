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
OBTENER ID
============================== */

if(!isset($_GET['id']))
{
    die("ID de propiedad no válido");
}

$idPropiedad = $_GET['id'];

/* ==============================
OBTENER DATOS DE LA PROPIEDAD
============================== */

$sql = "SELECT * FROM vw_Propiedades WHERE idPropiedad = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $idPropiedad);

$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows == 0)
{
    die("Propiedad no encontrada");
}

$propiedad = $resultado->fetch_assoc();

/* ==============================
ACTUALIZAR PROPIEDAD
============================== */

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $tipoPropiedad = $_POST['tipoPropiedad'];
    $numeroIdentificador = $_POST['numeroIdentificador'];
    $descripcion = $_POST['descripcion'];
    $estadoFisico = $_POST['estadoFisico'];
    $estadoDisponibilidad = $_POST['estadoDisponibilidad'];

    /* ==============================
    IMAGEN ACTUAL
    ============================== */

    $imagenActual = $propiedad['Imagen'];

    /* ==============================
    SUBIR NUEVA IMAGEN
    ============================== */

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0)
    {
        /* Carpeta física */

        $carpetaServidor = "../../images/properties/";

        /* Ruta para guardar en BD */

        $carpetaBD = "images/properties/";

        if(!file_exists($carpetaServidor))
        {
            mkdir($carpetaServidor, 0777, true);
        }

        /* Obtener extensión */

        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);

        /* Nombre único */

        $nombreImagen = time() . "_" . uniqid() . "." . $extension;

        /* Ruta completa servidor */

        $rutaServidor = $carpetaServidor . $nombreImagen;

        /* Subir imagen */

        if(move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaServidor))
        {
            /* Guardar ruta relativa en BD */

            $imagenActual = $carpetaBD . $nombreImagen;
        }
    }

    /* ==============================
    PROCEDIMIENTO ALMACENADO
    ============================== */

    $stmtEditar = $conn->prepare("CALL sp_EditarPropiedad(?, ?, ?, ?, ?, ?, ?)");

    $stmtEditar->bind_param(
        "issssss",
        $idPropiedad,
        $tipoPropiedad,
        $numeroIdentificador,
        $descripcion,
        $estadoFisico,
        $estadoDisponibilidad,
        $imagenActual
    );

    if($stmtEditar->execute())
    {
        header("Location: Interface_Arrendamientos.php");
        exit();
    }
    else
    {
        echo "Error al actualizar: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Arrendamiento</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

        .status-select{
            background: #f5f7fb;
            border: 1px solid #dcdfe6;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
            font-size: 15px;
        }

        .extra-info{
            margin-top: 25px;
            padding: 20px;
            border-radius: 16px;
            background: #f8f9fc;
            border: 1px solid #e5e7eb;
        }

        .extra-info h3{
            margin-bottom: 18px;
            color: #1f2937;
        }

        .status-badge{
            display: inline-block;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-maintenance{
            background: #fff4d6;
            color: #c98a00;
        }

        .btn-delete{
            background: #ff4d4f;
            color: white;
            border: none;
            padding: 14px 22px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: .3s;
        }

        .btn-delete:hover{
            background: #e63b3d;
        }

        .alert-error{
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .edit-avatar{
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 18px;
            border: 3px solid #e5e7eb;
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
                        Editar Arrendamiento
                    </h1>

                    <p class="subtitle">
                        Modifica la información del inmueble.
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

            <!-- EDIT SECTION -->
            <section class="edit-section">

                <div class="edit-card">

                    <?php if(isset($error)) { ?>

                        <div class="alert-error">

                            <?php echo $error; ?>

                        </div>

                    <?php } ?>

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="../../<?php echo $propiedad['Imagen']; ?>"
                            alt="Arrendamiento"
                            class="edit-avatar"
                        >

                        <div class="status-badge status-maintenance">

                            <?php echo $propiedad['EstadoFisico']; ?>

                        </div>

                    </div>

                    <!-- FORM -->
                    <form 
                        class="edit-form"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <div class="form-grid">

                            <!-- NUMERO -->
                            <div class="input-group">

                                <label>
                                    Número Identificador
                                </label>

                                <input 
                                    type="text"
                                    name="numeroIdentificador"
                                    value="<?php echo $propiedad['NumeroIdentificador']; ?>"
                                    required
                                >

                            </div>

                            <!-- TIPO -->
                            <div class="input-group">

                                <label>
                                    Tipo de Propiedad
                                </label>

                                <select 
                                    name="tipoPropiedad"
                                    class="status-select"
                                    required
                                >

                                    <option value="Local comercial"
                                        <?php if($propiedad['TipoPropiedad'] == 'Local comercial') echo 'selected'; ?>>
                                        Local Comercial
                                    </option>

                                    <option value="Casa"
                                        <?php if($propiedad['TipoPropiedad'] == 'Casa') echo 'selected'; ?>>
                                        Casa
                                    </option>

                                    <option value="Edificio"
                                        <?php if($propiedad['TipoPropiedad'] == 'Edificio') echo 'selected'; ?>>
                                        Edificio
                                    </option>

                                </select>

                            </div>

                            <!-- ESTADO FISICO -->
                            <div class="input-group">

                                <label>
                                    Estado Físico
                                </label>

                                <select 
                                    name="estadoFisico"
                                    class="status-select"
                                    required
                                >

                                    <option value="Buenas condiciones"
                                        <?php if($propiedad['EstadoFisico'] == 'Buenas condiciones') echo 'selected'; ?>>
                                        Buenas condiciones
                                    </option>

                                    <option value="Malas condiciones"
                                        <?php if($propiedad['EstadoFisico'] == 'Malas condiciones') echo 'selected'; ?>>
                                        Malas condiciones
                                    </option>

                                    <option value="En mantenimiento"
                                        <?php if($propiedad['EstadoFisico'] == 'En mantenimiento') echo 'selected'; ?>>
                                        En mantenimiento
                                    </option>

                                </select>

                            </div>

                            <!-- DISPONIBILIDAD -->
                            <div class="input-group">

                                <label>
                                    Estado de Disponibilidad
                                </label>

                                <select 
                                    name="estadoDisponibilidad"
                                    class="status-select"
                                    required
                                >

                                    <option value="Disponible"
                                        <?php if($propiedad['EstadoDisponibilidad'] == 'Disponible') echo 'selected'; ?>>
                                        Disponible
                                    </option>

                                    <option value="Rentado"
                                        <?php if($propiedad['EstadoDisponibilidad'] == 'Rentado') echo 'selected'; ?>>
                                        Rentado
                                    </option>

                                    <option value="Aspecto Legal"
                                        <?php if($propiedad['EstadoDisponibilidad'] == 'Aspecto Legal') echo 'selected'; ?>>
                                        Aspecto Legal
                                    </option>

                                </select>

                            </div>

                            <!-- IMAGEN -->
                            <div class="input-group full-width">

                                <label>
                                    Cambiar Imagen
                                </label>

                                <input 
                                    type="file"
                                    name="imagen"
                                    accept="image/*"
                                >

                            </div>

                            <!-- DESCRIPCION -->
                            <div class="input-group full-width">

                                <label>
                                    Descripción
                                </label>

                                <textarea 
                                    rows="5"
                                    name="descripcion"
                                    required
                                ><?php echo $propiedad['Descripcion']; ?></textarea>

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="form-buttons">

                            <a 
                                href="Interface_Arrendamientos.php"
                                class="btn-cancel"
                                style="text-decoration:none;"
                            >
                                Cancelar
                            </a>

                            <button type="submit" class="btn-save">
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

<?php

ob_end_flush();

?>