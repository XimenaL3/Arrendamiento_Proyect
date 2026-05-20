<?php

ob_start();
session_start();

/* =========================================
VALIDAR SESIÓN
========================================= */

if (
    !isset($_SESSION['idUsuario']) ||
    ($_SESSION['rol'] ?? 0) != 3
) {

    header("Location: Login.php");
    exit();

}

$idUsuario = $_SESSION['idUsuario'];
$idPersona = $_SESSION['idPersona'];

require_once "../../includes/Conexion.php";

/* =========================================
VALIDAR CONEXIÓN
========================================= */

if (!$conn) {

    die("Error de conexión a la base de datos");

}

/* =========================================
OBTENER ID REPORTE
========================================= */

$idReporte = 0;

/* =========================================
SI VIENE POR GET
========================================= */

if(isset($_GET['idReporte'])){

    $idReporte = intval($_GET['idReporte']);

}

/* =========================================
SI VIENE POR POST
========================================= */

if(isset($_POST['idReporte'])){

    $idReporte = intval($_POST['idReporte']);

}

/* =========================================
VALIDAR
========================================= */

if($idReporte <= 0){

    die("Reporte inválido");

}

/* =========================================
USUARIO LOGUEADO
========================================= */

$stmtUser = $conn->prepare("
    SELECT 
        p.Nombre,
        p.ApellidoP,
        p.ApellidoM,
        p.Imagen,
        r.NombreRol
    FROM Usuarios u
    INNER JOIN Personas p
        ON p.idPersona = u.idPersona
    INNER JOIN Roles r
        ON r.idRol = u.idRol
    WHERE u.idUsuario = ?
");

$stmtUser->bind_param("i", $idUsuario);

$stmtUser->execute();

$stmtUser->bind_result(
    $nombre,
    $apellidoP,
    $apellidoM,
    $imagen,
    $rol
);

$stmtUser->fetch();

$stmtUser->close();

$imagenUsuario = (!empty($imagen))
    ? "../images/person/" . $imagen
    : "../images/icons/Usuario.png";

$nombreCompleto =
    trim($nombre . " " . $apellidoP . " " . $apellidoM);

/* =========================================
OBTENER REPORTE
========================================= */

$stmtReporte = $conn->prepare("
    SELECT *
    FROM vw_Reportes
    WHERE idReporte = ?
");

$stmtReporte->bind_param("i", $idReporte);

$stmtReporte->execute();

$resultReporte = $stmtReporte->get_result();

if($resultReporte->num_rows <= 0){

    die("Reporte no encontrado");

}

$reporte = $resultReporte->fetch_assoc();

$stmtReporte->close();

/* =========================================
OBTENER PRODUCTOS DEL REPORTE
========================================= */

$sqlProductos = "
SELECT 
    p.*,
    rp.cantidad AS CantidadUsada
FROM Reporte_Productos rp
INNER JOIN vw_Productos p 
    ON p.idProducto = rp.idProducto
WHERE rp.idReporte = ?
";

$stmtProductos = $conn->prepare($sqlProductos);

if(!$stmtProductos){
    die("Error prepare productos: " . $conn->error);
}

$stmtProductos->bind_param("i", $idReporte);

$stmtProductos->execute();

$resultProductos = $stmtProductos->get_result();

/* =========================================
REGISTRAR MANTENIMIENTO
========================================= */

if(isset($_POST['registrarMantenimiento'])){

    $tareaRealizada = trim($_POST['tareaRealizada']);
    $fechaFin = date('Y-m-d H:i:s');

    /* =========================================
    REGISTRAR TODOS LOS PRODUCTOS DEL REPORTE
    ========================================= */

    $sqlProductosReporte = "
    SELECT idProducto
    FROM Reporte_Productos
    WHERE idReporte = ?
    ";

    $stmtProductosReporte = $conn->prepare($sqlProductosReporte);

    $stmtProductosReporte->bind_param(
        "i",
        $idReporte
    );

    $stmtProductosReporte->execute();

    $resultProductosReporte =
        $stmtProductosReporte->get_result();

    while($producto = $resultProductosReporte->fetch_assoc()){

        $idProducto = intval($producto['idProducto']);

        $stmt = $conn->prepare("
            CALL sp_RegistrarMantenimientoDetalle(?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiiss",
            $idReporte,
            $idUsuario,
            $idProducto,
            $tareaRealizada,
            $fechaFin
        );

        $stmt->execute();
        $stmt->close();

        while(
            $conn->more_results() &&
            $conn->next_result()
        ){}
    }

    $stmtProductosReporte->close();

    /* =========================
       FINALIZAR REPORTE (UNA SOLA VEZ)
    ========================= */

    $stmtFinal = $conn->prepare("
        CALL sp_FinalizarReporte(?)
    ");

    $stmtFinal->bind_param("i", $idReporte);
    $stmtFinal->execute();
    $stmtFinal->close();

    while($conn->more_results() && $conn->next_result()){}

    header("Location: Interface_Productos_Limpieza_M.php");
    exit();
}

/* =========================================
IMAGEN REPORTE
========================================= */

$imagenReporte = !empty($reporte['Evidencia'])
    ? "../images/reports/" . $reporte['Evidencia']
    : "../images/default/default-report.jpg";

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
        Registrar Mantenimiento
    </title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/Estilo_Edicion.css">

    <style>

:root{
    --bg:#f3f4f6;
    --white:#ffffff;
    --black:#0f0f14;
    --gray:#6b7280;
    --shadow:0 18px 45px rgba(0,0,0,.08);
    --radius:30px;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:var(--bg);
    color:#111;
}

/* =========================================
LAYOUT
========================================= */

.container{
    display:flex;
    min-height:100vh;
}

/* =========================================
SIDEBAR
========================================= */

.sidebar{
    width:270px;
    background:#111;
    color:white;
    padding:30px 22px;
    display:flex;
    flex-direction:column;
    position:sticky;
    top:0;
    height:100vh;
}

.brand{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:50px;
}

.brand-logo{
    width:55px;
    height:55px;
    border-radius:16px;
    object-fit:cover;
}

.brand-text h2{
    font-size:20px;
}

.brand-text span{
    font-size:13px;
    color:#9ca3af;
}

.sidebar-nav{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.sidebar-nav a{
    display:flex;
    align-items:center;
    gap:15px;
    text-decoration:none;
    color:white;
    padding:16px;
    border-radius:18px;
    transition:.3s;
}

.sidebar-nav a.active,
.sidebar-nav a:hover{
    background:#1f1f1f;
}

.menu-icon{
    width:22px;
}

/* =========================================
MAIN
========================================= */

.main-content{
    flex:1;
    padding:35px;
}

/* =========================================
TOPBAR
========================================= */

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:35px;
    gap:25px;
}

.top-bar h1{
    font-size:38px;
    margin-bottom:8px;
}

.subtitle{
    color:var(--gray);
}

.user-profile{
    padding:0;
    background:transparent;
    box-shadow:none;
}

.logged-user{
    display:flex;
    align-items:center;
    gap:14px;
}

.avatar-admin{
    width:58px;
    height:58px;
    border-radius:50%;
    object-fit:cover;
}

.user-info small{
    color:#6b7280;
    display:block;
    margin-bottom:4px;
}

/* =========================================
DASHBOARD GRID
========================================= */

.dashboard-grid{
    display:grid;
    grid-template-columns: 1.3fr 0.9fr;
    gap:28px;

    align-items:stretch;
}

/* =========================================
REPORTE CARD BLANCA
========================================= */

.report-card{
    background:white;
    border-radius:35px;
    padding:20px;
    box-shadow:var(--shadow);

    display:flex;
    gap:18px;

    align-items:flex-start;

    height:340px;
    overflow:hidden;
}

.report-image{
    width:300px;
    min-width:300px;
    height:300px;
    object-fit:cover;
    border-radius:28px;
}

.report-content{
    flex:1;

    display:flex;
    flex-direction:column;

    min-width:0;
    overflow:hidden;
}

.report-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#f3f4f6;
    padding:10px 18px;
    border-radius:30px;
    font-size:13px;
    font-weight:bold;
    margin-bottom:18px;
    width:max-content;
}

.report-title{
    font-size:28px;
    margin-bottom:14px;
    line-height:1.2;

    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;

    overflow:hidden;
}

.report-details{
    display:flex;
    flex-direction:column;

    gap:10px;

    overflow-y:auto;

    padding-right:6px;

    flex:1;
}

.report-details p{
    color:#4b5563;
    line-height:1.4;
    font-size:14px;
}

.report-details strong{
    color:#111;
}

.report-details::-webkit-scrollbar{
    width:6px;
}

.report-details::-webkit-scrollbar-thumb{
    background:#d1d5db;
    border-radius:10px;
}

/* =========================================
STATUS
========================================= */

.status{
    display:inline-block;
    padding:8px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.Pendiente{
    background:#fef3c7;
    color:#92400e;
}

.Enproceso{
    background:#dbeafe;
    color:#1d4ed8;
}

.Alta{
    color:#dc2626;
    font-weight:bold;
}

.Media{
    color:#d97706;
    font-weight:bold;
}

.Baja{
    color:#16a34a;
    font-weight:bold;
}

/* =========================================
PRODUCTOS PANEL NEGRO
========================================= */

.products-panel{
    background:#0f0f14;
    color:white;
    border-radius:35px;
    padding:22px;

    display:flex;
    flex-direction:column;

    height:340px;

    overflow:hidden;
}

.products-panel h2{
    font-size:30px;
    margin-bottom:25px;
}

.products-scroll{
    flex:1;

    overflow-y:auto;

    padding-right:8px;

    display:flex;
    flex-direction:column;

    gap:20px;
}

.products-scroll::-webkit-scrollbar{
    width:6px;
}

.products-scroll::-webkit-scrollbar-thumb{
    background:#3f3f46;
    border-radius:10px;
}

/* =========================================
PRODUCT CARD
========================================= */

.product-card{
    background:#18181f;
    border-radius:28px;
    padding:18px;
    border:2px solid transparent;
    transition:.3s ease;

    display:flex;
    gap:18px;
    align-items:center;
}

.product-card.selected{
    border-color:white;
    transform:translateY(-3px);
}

.product-image{
    width:120px;
    min-width:120px;
    height:120px;
    object-fit:cover;
    border-radius:22px;
}

.product-content{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.product-content h3{
    font-size:22px;
    margin-bottom:12px;
}

.product-content p{
    color:#d1d5db;
    font-size:14px;
    line-height:1.6;
    margin-bottom:10px;
}

.btn-product{
    width:100%;
    border:none;
    background:white;
    color:#111;
    padding:15px;
    border-radius:16px;
    cursor:pointer;
    font-weight:bold;
    margin-top:15px;
    transition:.3s;
}

.btn-product.active{
    background:#22c55e;
    color:white;
}

/* =========================================
FORM PANEL ABAJO
========================================= */

.bottom-panel{
    margin-top:28px;
    background:white;
    border-radius:35px;
    padding:35px;
    box-shadow:var(--shadow);
}

.bottom-panel h2{
    font-size:30px;
    margin-bottom:20px;
}

.textarea-task{
    width:100%;
    border:none;
    outline:none;
    background:#f3f4f6;
    border-radius:24px;
    padding:25px;
    resize:none;
    min-height:240px;
    font-size:15px;
    line-height:1.7;
}

.form-buttons{
    margin-top:28px;
    display:flex;
    justify-content:flex-end;
    gap:18px;
}

.btn-cancel{
    background:#e5e7eb;
    color:#111;
    border:none;
    padding:18px 28px;
    border-radius:18px;
    font-weight:bold;
    text-decoration:none;
    display:flex;
    align-items:center;
    justify-content:center;
}

.btn-save{
    background:#111;
    color:white;
    border:none;
    padding:18px 32px;
    border-radius:18px;
    cursor:pointer;
    font-weight:bold;
}

/* =========================================
FOOTER
========================================= */

.footer{
    margin-top:35px;
    text-align:center;
    color:#6b7280;
}

/* =========================================
RESPONSIVE
========================================= */

@media(max-width:1200px){

    .dashboard-grid{
        grid-template-columns:1fr;
    }

    .products-panel{
        height:auto;
    }

}

@media(max-width:1000px){

    .report-card{
        flex-direction:column;
    }

    .report-image{
        width:100%;
        min-width:100%;
        height:300px;
    }

    .product-card{
        flex-direction:column;
        align-items:flex-start;
    }

    .product-image{
        width:100%;
        height:220px;
    }

}

@media(max-width:700px){

    .main-content{
        padding:20px;
    }

    .top-bar{
        flex-direction:column;
    }

    .report-title{
        font-size:28px;
    }

}

/* =========================================
BOTÓN LOGOUT
========================================= */

.top-actions{
    display:flex;
    align-items:center;
    gap:15px;
}

.logout-btn{
    width:58px;
    height:58px;
    background:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
    transition:.3s ease;
    text-decoration:none;
}

.logout-btn:hover{
    transform:translateY(-3px);
}

.logout-btn img{
    width:24px;
    height:24px;
    object-fit:contain;
}

</style>

<!-- =========================================
MAIN CONTENT
========================================= -->

<main class="main-content">

    <!-- TOPBAR -->
    <header class="top-bar">

        <div>

            <h1>
                Registrar Mantenimiento
            </h1>

            <p class="subtitle">
                Información y materiales utilizados para el reporte.
            </p>

        </div>

        <div class="top-actions">

            <!-- BOTÓN CERRAR SESIÓN -->
            <a href="Login.php" class="logout-btn">

                <img 
                    src="../images/icons/Cerrar_Oscuro.png"
                    alt="Cerrar sesión"
                >

            </a>

            <!-- USUARIO -->
            <div class="user-profile">

                <div class="logged-user">

                    <img 
                        src="<?php echo htmlspecialchars($imagenUsuario); ?>"
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

        </div>

    </header>

    <!-- FORM -->
    <form method="POST">

        <input 
            type="hidden"
            name="idReporte"
            value="<?php echo $idReporte; ?>"
        >

        <!-- GRID -->
        <section class="dashboard-grid">

            <!-- TARJETA REPORTE -->
        <div class="report-card">

            <img 
                src="<?php echo htmlspecialchars($imagenReporte); ?>"
                class="report-image"
            >

            <div class="report-content">

                <h2 class="report-title">

                    <?php echo htmlspecialchars($reporte['Titulo']); ?>

                </h2>

                <div class="report-details">

                    <p>

                        <strong>
                            Usuario:
                        </strong>

                        <?php

                        echo htmlspecialchars(

                            $reporte['NombreUsuario'] . ' ' .
                            $reporte['ApellidoP'] . ' ' .
                            $reporte['ApellidoM']

                        );

                        ?>

                    </p>

                    <p>

                        <strong>
                            Propiedad:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $reporte['NumeroIdentificador']
                        );
                        ?>

                    </p>

                    <p>

                        <strong>
                            Tipo:
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $reporte['TipoReporte']
                        );
                        ?>

                    </p>

                    <p>

                        <strong>
                            Prioridad:
                        </strong>

                        <span class="<?php echo $reporte['Prioridad']; ?>">

                            <?php
                            echo htmlspecialchars(
                                $reporte['Prioridad']
                            );
                            ?>

                        </span>

                    </p>

                    <p>

                        <strong>
                            Estado:
                        </strong>

                        <span class="status <?php echo str_replace(' ','',$reporte['Estado']); ?>">

                            <?php
                            echo htmlspecialchars(
                                $reporte['Estado']
                            );
                            ?>

                        </span>

                    </p>

                    <p>

                        <strong>
                            Descripción:
                        </strong><br>

                        <?php
                        echo htmlspecialchars(
                            $reporte['Descripcion']
                        );
                        ?>

                    </p>

                </div>

            </div>

        </div>

            <!-- PANEL PRODUCTOS -->
            <div class="products-panel">

                <h2>
                    Productos Utilizados
                </h2>

                <div class="products-scroll">

                    <?php while($producto = $resultProductos->fetch_assoc()): ?>

                        <?php

                        $imagenProducto =
                            !empty($producto['Imagen'])
                            ? "../../" . $producto['Imagen']
                            : "../images/default/default-product.png";

                        ?>

                        <div 
                            class="product-card"
                            data-id="<?php echo $producto['idProducto']; ?>"
                        >

                            <img 
                                src="<?php echo htmlspecialchars($imagenProducto); ?>"
                                class="product-image"
                            >

                            <div class="product-content">

                                <h3>

                                    <?php

                                    echo htmlspecialchars(
                                        $producto['NombreProducto']
                                    );

                                    ?>

                                </h3>

                                <p>

                                    <?php

                                    echo htmlspecialchars(
                                        $producto['Descripcion']
                                    );

                                    ?>

                                </p>

                                <p>

                                    <strong>
                                        Cantidad utilizada:
                                    </strong>

                                    <?php
                                    echo $producto['CantidadUsada'];
                                    ?>

                                </p>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            </div>

        </section>

        <!-- PANEL INFERIOR -->
        <section class="bottom-panel">

            <h2>
                Detalle del Mantenimiento
            </h2>

            <textarea
                name="tareaRealizada"
                class="textarea-task"
                required
                placeholder="Describe detalladamente el mantenimiento realizado..."
            ></textarea>

            <div class="form-buttons">

                <a 
                    href="Interface_Productos_Limpieza_M.php"
                    class="btn-cancel"
                >

                    Cancelar

                </a>

                <button
                    type="submit"
                    name="registrarMantenimiento"
                    class="btn-save"
                >

                    Registrar Mantenimiento

                </button>

            </div>

        </section>

    </form>

    <!-- FOOTER -->
    <footer class="footer">

        <p>

            © 2026 DiamondsCorporation.
            Todos los derechos reservados.

        </p>

    </footer>

</main>

</body>

</html>