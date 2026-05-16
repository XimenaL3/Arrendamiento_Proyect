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
CONTAR NOTIFICACIONES
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

if ($rowCount = $resultCount->fetch_assoc()) {

    $totalNotificaciones = $rowCount['total'];

}

/* =========================================
OBTENER PROPIEDADES
========================================= */

$propiedades = [];

$sqlPropiedades = "
SELECT
    idPropiedad,
    TipoPropiedad,
    NumeroIdentificador
FROM Propiedades
ORDER BY idPropiedad DESC
";

$resultPropiedades = mysqli_query(
    $conn,
    $sqlPropiedades
);

while ($row = mysqli_fetch_assoc($resultPropiedades)) {

    $propiedades[] = $row;

}

/* =========================================
REGISTRAR TIENDA
========================================= */

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombreTienda = trim($_POST['nombreTienda']);
    $idPropiedad = intval($_POST['idPropiedad']);

    $stmtRegistrar = $conn->prepare("
        CALL sp_AgregarTiendaCobro(
            ?,
            ?
        )
    ");

    if (!$stmtRegistrar) {

        $mensaje = "Error al preparar consulta";
        $tipoMensaje = "error";

    } else {

        $stmtRegistrar->bind_param(
            "si",
            $nombreTienda,
            $idPropiedad
        );

        if ($stmtRegistrar->execute()) {
            header("Location: Interface_Tiendas.php");
            exit();
        } else {
            die("Error al ejecutar SP: " . $stmt->error);
        }

        $stmtRegistrar->close();

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
        Agregar Tienda de Cobro
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

        .success-message {

            background: #dcfce7;
            color: #166534;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;

        }

        .error-message {

            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;

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
                        Agregar Tienda de Cobro
                    </h1>

                    <p class="subtitle">
                        Registra nuevas tiendas de cobro.
                    </p>

                </div>

                <div class="user-profile">

                    <!-- NOTIFICACIONES -->
                    <div
                        class="notification-wrapper"
                        id="notificationWrapper"
                    >

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
            <?php if($mensaje != "") { ?>

                <div class="<?php
                    echo ($tipoMensaje == "success")
                    ? "success-message"
                    : "error-message";
                ?>">

                    <?php echo $mensaje; ?>

                </div>

            <?php } ?>

            <!-- FORM -->
            <section class="edit-section">

                <div class="edit-card">

                    <!-- FORMULARIO -->
                    <form
                        class="edit-form"
                        method="POST"
                    >

                        <div class="form-grid">

                            <!-- NOMBRE TIENDA -->
                            <div class="input-group">

                                <label>
                                    Nombre de la Tienda
                                </label>

                                <input
                                    type="text"
                                    name="nombreTienda"
                                    required
                                >

                            </div>

                            <!-- PROPIEDAD -->
                            <div class="input-group">

                                <label>
                                    Propiedad
                                </label>

                                <select
                                    name="idPropiedad"
                                    required
                                >

                                    <option value="">
                                        Selecciona una propiedad
                                    </option>

                                    <?php foreach($propiedades as $propiedad) { ?>

                                        <option
                                            value="<?php
                                                echo $propiedad['idPropiedad'];
                                            ?>"
                                        >

                                            <?php

                                            echo
                                                $propiedad['TipoPropiedad']
                                                . " #"
                                                . $propiedad['NumeroIdentificador'];

                                            ?>

                                        </option>

                                    <?php } ?>

                                </select>

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

                                Registrar Tienda

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
            <div
                class="notifications-modal"
                id="notificationsModal"
            >

                <div class="modal-header">

                    <h2>
                        Notificaciones
                    </h2>

                    <button
                        class="close-modal"
                        id="closeModal"
                    >
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

                    <div class="notification-item <?php
                        echo ($noti['Estado'] == 'Leida')
                        ? 'completed'
                        : '';
                    ?>">

                        <div class="notification-info">

                            <h4>

                                <?php
                                    echo htmlspecialchars(
                                        $noti['Titulo']
                                    );
                                ?>

                            </h4>

                            <p>

                                <?php
                                    echo htmlspecialchars(
                                        $noti['Mensaje']
                                    );
                                ?>

                            </p>

                            <span>

                                <?php

                                if(
                                    $noti['MinutosTranscurridos'] < 60
                                )
                                {

                                    echo "Hace "
                                        . $noti['MinutosTranscurridos']
                                        . " minutos";

                                }
                                else if(
                                    $noti['MinutosTranscurridos'] < 1440
                                )
                                {

                                    echo "Hace "
                                        . floor(
                                            $noti['MinutosTranscurridos'] / 60
                                        )
                                        . " horas";

                                }
                                else
                                {

                                    echo "Hace "
                                        . floor(
                                            $noti['MinutosTranscurridos'] / 1440
                                        )
                                        . " días";

                                }

                                ?>

                            </span>

                        </div>

                        <?php if($noti['Estado'] == 'No Leida') { ?>

                        <button
                            class="btn-check"
                            data-id="<?php
                                echo $noti['idNotificacion'];
                            ?>"
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

        const sidebar =
            document.getElementById('sidebar');

        const brandToggle =
            document.getElementById('brandToggle');

        const overlay =
            document.getElementById('overlay');

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

        /* =========================
        SIDEBAR
        ========================= */

        function toggleSidebar()
        {

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

        /* =========================
        MODAL
        ========================= */

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

                sidebar.classList.remove(
                    'collapsed'
                );

            }
        );

        /* =========================
        MARCAR NOTIFICACIÓN
        ========================= */

        document.querySelectorAll('.btn-check')
        .forEach(button =>
        {

            button.addEventListener(
                'click',
                () =>
                {

                    const id =
                        button.dataset.id;

                    fetch(
                        'Marcar_Notificacion.php',
                        {

                            method: 'POST',

                            headers:
                            {
                                'Content-Type':
                                'application/x-www-form-urlencoded'
                            },

                            body:
                                'idNotificacion=' + id

                        }
                    )
                    .then(response => response.text())
                    .then(data =>
                    {

                        if(data === "OK")
                        {

                            const notification =
                                button.parentElement;

                            notification.classList.add(
                                'completed'
                            );

                            button.remove();

                            const badge =
                                document.querySelector(
                                    '.notification-badge'
                                );

                            if(badge)
                            {

                                let total =
                                    parseInt(
                                        badge.innerText
                                    );

                                total--;

                                if(total <= 0)
                                {

                                    badge.remove();

                                }
                                else
                                {

                                    badge.innerText =
                                        total;

                                }

                            }

                        }

                    });

                }
            );

        });

    </script>

</body>

</html>

<?php
ob_end_flush();
?>