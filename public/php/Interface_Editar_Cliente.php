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

// ==============================
// VALIDAR ID
// ==============================

if(!isset($_GET['id']) || empty($_GET['id']))
{
    header("Location: Interface_Clientes.php");
    exit();
}

$idInquilino = intval($_GET['id']);

// ==============================
// OBTENER DATOS DEL CLIENTE
// ==============================

$sqlCliente = "
SELECT 

    I.idInquilino,
    I.HistorialCrediticio,
    I.RegistroDeudasPrevias,

    P.Nombre,
    P.ApellidoP,
    P.ApellidoM,
    P.Telefono,
    P.Correo,
    P.Imagen

FROM Inquilinos I

INNER JOIN Personas P
    ON I.idPersona = P.idPersona

WHERE I.idInquilino = $idInquilino
";

$resultadoCliente = mysqli_query($conn, $sqlCliente);

if(!$resultadoCliente)
{
    die("Error en consulta: " . mysqli_error($conn));
}

$cliente = mysqli_fetch_assoc($resultadoCliente);

if(!$cliente)
{
    header("Location: Interface_Clientes.php");
    exit();
}

// ==============================
// ACTUALIZAR CLIENTE
// ==============================

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);

    $apellidoP = mysqli_real_escape_string($conn, $_POST['apellidoP']);

    $apellidoM = mysqli_real_escape_string($conn, $_POST['apellidoM']);

    $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);

    $correo = mysqli_real_escape_string($conn, $_POST['correo']);

    $historial = mysqli_real_escape_string($conn, $_POST['historial']);

    $deudas = isset($_POST['deudas']) ? 1 : 0;

    // ==============================
    // IMAGEN
    // ==============================

    $imagenNombre = $cliente['Imagen'];

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0)
    {
        $imagenNombre = time() . "_" . basename($_FILES['imagen']['name']);

        $rutaTemporal = $_FILES['imagen']['tmp_name'];

        $rutaDestino = "../images/person/" . $imagenNombre;

        move_uploaded_file($rutaTemporal, $rutaDestino);
    }

    // ==============================
    // PROCEDIMIENTO ALMACENADO
    // ==============================

    $sqlEditar = "
    CALL sp_EditarCliente(

        '$idInquilino',

        '$nombre',
        '$apellidoP',
        '$apellidoM',
        '$telefono',
        '$correo',
        '$imagenNombre',

        '$historial',
        '$deudas'
    )
    ";

    if(mysqli_query($conn, $sqlEditar))
    {
        header("Location: Interface_Clientes.php");
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

    <title>Editar Cliente</title>

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

                    <h2>Sunlight Gardens</h2>
                    <span>Panel Administrativo</span>

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

                    <span>Trabajadores</span>

                </a>

                <a href="Interface_Clientes.php" class="active">

                    <img 
                        src="../images/icons/Clientes_Oscuro.png"
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

                <a href="Interface_Abonos.php">

                    <img 
                        src="../images/icons/Pago_Claro.png"
                        alt="Abonos"
                        class="menu-icon"
                    >

                    <span>Abonos</span>

                </a>

                <a href="Interface_Productos_Limpieza.php">

                    <img 
                        src="../images/icons/Mantenimiento_Claro.png"
                        alt="Almacen Limpieza"
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
                        Editar Cliente
                    </h1>

                    <p class="subtitle">
                        Modifica la información del cliente registrado.
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

                    <!-- FOTO -->
                    <div class="profile-edit">

                        <img 
                            src="<?php
                            
                                if(!empty($cliente['Imagen']))
                                {
                                    echo "../images/person/" . $cliente['Imagen'];
                                }
                                else
                                {
                                    echo "../images/icons/Usuario.png";
                                }

                            ?>"
                            alt="Cliente"
                            class="edit-avatar"
                        >

                    </div>

                    <!-- FORM -->
                    <form 
                        class="edit-form"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <div class="form-grid">

                            <!-- NOMBRE -->
                            <div class="input-group">

                                <label>
                                    Nombre
                                </label>

                                <input 
                                    type="text"
                                    name="nombre"
                                    value="<?php echo htmlspecialchars($cliente['Nombre']); ?>"
                                    required
                                >

                            </div>

                            <!-- APELLIDO P -->
                            <div class="input-group">

                                <label>
                                    Apellido Paterno
                                </label>

                                <input 
                                    type="text"
                                    name="apellidoP"
                                    value="<?php echo htmlspecialchars($cliente['ApellidoP']); ?>"
                                    required
                                >

                            </div>

                            <!-- APELLIDO M -->
                            <div class="input-group">

                                <label>
                                    Apellido Materno
                                </label>

                                <input 
                                    type="text"
                                    name="apellidoM"
                                    value="<?php echo htmlspecialchars($cliente['ApellidoM']); ?>"
                                >

                            </div>

                            <!-- TELEFONO -->
                            <div class="input-group">

                                <label>
                                    Número Telefónico
                                </label>

                                <input 
                                    type="text"
                                    name="telefono"
                                    value="<?php echo htmlspecialchars($cliente['Telefono']); ?>"
                                    required
                                >

                            </div>

                            <!-- CORREO -->
                            <div class="input-group">

                                <label>
                                    Correo Electrónico
                                </label>

                                <input 
                                    type="email"
                                    name="correo"
                                    value="<?php echo htmlspecialchars($cliente['Correo']); ?>"
                                    required
                                >

                            </div>

                            <!-- HISTORIAL -->
                            <div class="input-group">

                                <label>
                                    Historial Crediticio
                                </label>

                                <select name="historial" required>

                                    <option value="Bueno"
                                    
                                    <?php 
                                    
                                        if($cliente['HistorialCrediticio'] == 'Bueno')
                                        {
                                            echo "selected";
                                        }

                                    ?>

                                    >
                                        Bueno
                                    </option>

                                    <option value="Malo"

                                    <?php 
                                    
                                        if($cliente['HistorialCrediticio'] == 'Malo')
                                        {
                                            echo "selected";
                                        }

                                    ?>

                                    >
                                        Malo
                                    </option>

                                    <option value="Nuevo"

                                    <?php 
                                    
                                        if($cliente['HistorialCrediticio'] == 'Nuevo')
                                        {
                                            echo "selected";
                                        }

                                    ?>

                                    >
                                        Nuevo
                                    </option>

                                </select>

                            </div>

                            <!-- DEUDAS -->
                            <div class="input-group">

                                <label>
                                    Registro de Deudas
                                </label>

                                <div style="margin-top: 12px;">

                                    <input 
                                        type="checkbox"
                                        name="deudas"

                                        <?php
                                        
                                            if($cliente['RegistroDeudasPrevias'] == 1)
                                            {
                                                echo "checked";
                                            }

                                        ?>
                                    >

                                    Tiene deudas previas

                                </div>

                            </div>

                            <!-- FOTO -->
                            <div class="input-group full-width">

                                <label>
                                    Cambiar Foto
                                </label>

                                <input 
                                    type="file"
                                    name="imagen"
                                >

                            </div>

                        </div>

                        <!-- BUTTONS -->
                        <div class="form-buttons">

                            <a 
                                href="Interface_Clientes.php"
                                class="btn-cancel"
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

        function toggleSidebar() {

            sidebar.classList.toggle('collapsed');

            overlay.classList.toggle('active');

        }

        brandToggle.addEventListener('click', toggleSidebar);

        overlay.addEventListener('click', () => {

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
        MARCAR COMO VISTA
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