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

if (!$conn) {

    die("Error de conexión a la base de datos");

}

/* =========================================
USUARIO LOGUEADO
========================================= */

$stmt = $conn->prepare("
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

$nombreCompleto = trim(
    ($nombre ?? '') . ' ' .
    ($apellidoP ?? '') . ' ' .
    ($apellidoM ?? '')
);

$imagenUsuario = (!empty($imagen))
    ? "../images/person/" . $imagen
    : "../images/icons/Usuario.png";

/* =========================================
FILTROS
========================================= */

$filtroFecha = $_GET['fecha'] ?? '';
$filtroPrioridad = $_GET['prioridad'] ?? '';

$where = "WHERE Estado = 'Pendiente'";

if (!empty($filtroFecha)) {

    $where .= " AND DATE(FechaRegistro) = '$filtroFecha'";

}

if (!empty($filtroPrioridad)) {

    $where .= " AND Prioridad = '$filtroPrioridad'";

}

/* =========================================
REPORTES
========================================= */

$sqlReportes = "
SELECT *
FROM vw_Reportes
$where
ORDER BY FechaRegistro DESC
";

$resultReportes = $conn->query($sqlReportes);

/* =========================================
PRODUCTOS
========================================= */

$sqlProductos = "
SELECT *
FROM vw_Productos
ORDER BY NombreProducto ASC
";

$resultProductos = $conn->query($sqlProductos);

/* =========================================
INQUILINOS URGENTES
========================================= */

$sqlUrgentes = "
SELECT 
    NombreUsuario,
    ApellidoP,
    ApellidoM,
    ImagenUsuario,
    Titulo,
    Prioridad
FROM vw_Reportes
WHERE Estado = 'Pendiente'
" . (!empty($filtroPrioridad)
    ? " AND Prioridad = '$filtroPrioridad'"
    : " AND Prioridad = 'Alta'") . "
ORDER BY FechaRegistro DESC
";

$resultUrgentes = $conn->query($sqlUrgentes);

/* =========================================
ATENDER REPORTE
========================================= */

if(isset($_POST['atenderReporte'])){

    $idReporte = intval($_POST['idReporte']);

    if(
        isset($_POST['productos']) &&
        is_array($_POST['productos'])
    ){

        foreach($_POST['productos'] as $producto){

            $idProducto = intval($producto['id']);
            $cantidad = intval($producto['cantidad']);

            $stmtDescontar = $conn->prepare("
                CALL sp_DescontarProductoBodega(?, ?)
            ");

            if($stmtDescontar){

                $stmtDescontar->bind_param(
                    "ii",
                    $idProducto,
                    $cantidad
                );

                $stmtDescontar->execute();
                $stmtDescontar->close();

                while(
                    $conn->more_results() &&
                    $conn->next_result()
                ){}

            }

        }

    }

    if(
        isset($_POST['productos']) &&
        is_array($_POST['productos'])
    ){

        foreach($_POST['productos'] as $producto){

            $idProducto = intval($producto['id']);
            $cantidad = intval($producto['cantidad']);

            $stmtGuardar = $conn->prepare("
                INSERT INTO Reporte_Productos
                (
                    idReporte,
                    idProducto,
                    cantidad
                )
                VALUES (?, ?, ?)
            ");

            $stmtGuardar->bind_param(
                "iii",
                $idReporte,
                $idProducto,
                $cantidad
            );

            if(!$stmtGuardar->execute()){

                die($stmtGuardar->error);

            }

            $stmtGuardar->close();

        }

    }

    $stmtReporte = $conn->prepare("
        UPDATE Reportes
        SET Estado = 'En proceso'
        WHERE idReporte = ?
    ");

    $stmtReporte->bind_param(
        "i",
        $idReporte
    );

    $stmtReporte->execute();
    $stmtReporte->close();

    header(
        "Location: Interface_Atender_Reporte.php?idReporte=" .
        $idReporte
    );

    exit();

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
    Dashboard Reportes
</title>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#f4f4f4;
    color:#111;
}

/* =========================================
LAYOUT
========================================= */

.dashboard{
    display:grid;
    grid-template-columns: 320px 1fr;
    gap:15px;
    min-height:100vh;
}

/* =========================================
PANEL IZQUIERDO
========================================= */

.main-panel{
    width:100%;
    padding:25px 25px 25px 5px;
}

/* =========================================
TOPBAR
========================================= */

.topbar{
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:30px;
    margin-bottom:45px;
}

.topbar-left{
    flex:1;
}

.subtitle{
    color:#6b7280;
    font-size:15px;
}

.user-box{
    background:white;
    padding:14px 22px;
    border-radius:24px;
    display:flex;
    align-items:center;
    gap:15px;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
    min-width:300px;
    margin-left:auto;
}

.user-avatar{
    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;
}

.user-info{
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.user-info small{
    color:#6b7280;
    margin-bottom:4px;
}

.user-info strong{
    font-size:15px;
}

.user-box img{
    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;
}

.user-box small{
    color:#6b7280;
    display:block;
    margin-bottom:4px;
}

.user-box strong{
    font-size:15px;
}

/* =========================================
REPORTES
========================================= */

.reports-container{
    display:flex;
    flex-direction:column;
    gap:28px;
    margin-bottom:60px;
}

.report-card{
    background:white;
    border-radius:34px;
    padding:24px;
    display:flex;
    align-items:center;
    gap:24px;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    transition:.3s ease;
}

.report-card:hover{
    transform:translateY(-4px);
}

.report-image{
    width:240px;
    height:190px;
    border-radius:28px;
    object-fit:cover;
    background:#e5e7eb;
}

.report-info{
    flex:1;
}

.report-info h2{
    font-size:30px;
    margin-bottom:12px;
    color:#111;
}

.report-info p{
    color:#4b5563;
    line-height:1.7;
    margin-bottom:10px;
}

.report-footer{
    margin-top:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* =========================================
STATUS
========================================= */

.status{
    padding:10px 18px;
    border-radius:30px;
    font-size:13px;
    font-weight:bold;
}

.Pendiente{
    background:#e5e7eb;
    color:#111;
}

.Alta{
    color:#111;
    font-weight:bold;
}

.Media{
    color:#525252;
    font-weight:bold;
}

.Baja{
    color:#9ca3af;
    font-weight:bold;
}

/* =========================================
BOTONES
========================================= */

.btn-attend{
    border:none;
    background:#111;
    color:white;
    padding:15px 24px;
    border-radius:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s ease;
}

.btn-attend:hover{
    transform:translateY(-2px);
    background:#000;
}

/* =========================================
SIDEBAR
========================================= */

.sidebar{
    background:#111111;
    min-height:100vh;
    padding:35px 25px;
    display:flex;
    flex-direction:column;
    gap:28px;
    position:sticky;
    top:0;
    overflow:visible;
}

/* =========================================
PANELES NEGROS
========================================= */

.dark-panel{
    background:transparent;
    color:white;
    padding:0;
    border-radius:0;
    box-shadow:none;
}

.dark-panel h2{
    font-size:28px;
    margin-bottom:22px;
}

/* =========================================
FILTRO PRIORIDAD
========================================= */

.priority-filter{
    margin-bottom:25px;
}

.priority-filter select{
    width:100%;
    border:none;
    outline:none;
    background:#1f1f1f;
    color:white;
    padding:15px;
    border-radius:18px;
    font-size:15px;
}

/* =========================================
INQUILINOS
========================================= */

.user-alert{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:22px;
    padding-bottom:18px;
    border-bottom:1px solid rgba(255,255,255,.08);
}

.user-alert:last-child{
    border-bottom:none;
    margin-bottom:0;
}

.user-alert img{
    width:58px;
    height:58px;
    border-radius:50%;
    object-fit:cover;
}

.user-alert strong{
    display:block;
    margin-bottom:6px;
}

.user-alert small{
    color:#d1d5db;
}

/* =========================================
MODAL
========================================= */

.overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    opacity:0;
    visibility:hidden;
    transition:.3s;
    z-index:100;
}

.overlay.active{
    opacity:1;
    visibility:visible;
}

.modal{
    position:fixed;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%) scale(.9);
    width:1200px;
    max-width:95%;
    background:white;
    border-radius:35px;
    padding:35px;
    z-index:101;
    opacity:0;
    visibility:hidden;
    transition:.3s;
    max-height:90vh;
    overflow-y:auto;
}

.modal.active{
    opacity:1;
    visibility:visible;
    transform:translate(-50%,-50%) scale(1);
}

.modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.close-modal{
    width:45px;
    height:45px;
    border:none;
    border-radius:12px;
    background:#f3f4f6;
    cursor:pointer;
}

/* =========================================
PRODUCTOS
========================================= */

.products-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.product-card{
    background:#fafafa;
    border-radius:24px;
    overflow:hidden;
    border:1px solid #ececec;
}

.product-image{
    width:100%;
    height:180px;
    object-fit:cover;
}

.product-content{
    padding:20px;
}

.product-content h3{
    margin-bottom:10px;
}

.qty-control{
    display:flex;
    align-items:center;
    gap:12px;
    margin-top:18px;
}

.qty-btn{
    width:35px;
    height:35px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    background:#e5e7eb;
}

.btn-add{
    width:100%;
    margin-top:18px;
    border:none;
    background:#111;
    color:white;
    padding:14px;
    border-radius:14px;
    cursor:pointer;
}

.modal-footer{
    margin-top:35px;
    text-align:right;
}

.btn-finish-report{
    border:none;
    background:#111;
    color:white;
    padding:18px 32px;
    border-radius:18px;
    cursor:pointer;
    font-weight:bold;
}

/* =========================================
RESPONSIVE
========================================= */

@media(max-width:1200px){

    .dashboard{
        grid-template-columns:1fr;
    }

}

@media(max-width:900px){

    .topbar{
        flex-direction:column;
        align-items:flex-start;
        gap:20px;
    }

    .report-card{
        flex-direction:column;
        align-items:flex-start;
    }

    .report-image{
        width:100%;
    }

    .products-grid{
        grid-template-columns:1fr;
    }

}

.flatpickr-calendar{
    background:#e5e7eb;
    border:none;
    border-radius:22px;
    box-shadow:none;
    width:100% !important;
    padding:15px;
}

.flatpickr-months{
    margin-bottom:10px;
}

.flatpickr-current-month{
    font-size:18px;
}

.flatpickr-weekdays{
    margin-bottom:8px;
}

.flatpickr-days{
    width:100% !important;
}

.dayContainer{
    width:100% !important;
    min-width:100% !important;
    max-width:100% !important;
}

.flatpickr-day{
    border-radius:12px;
    height:42px;
    line-height:42px;
}

.flatpickr-day.selected{
    background:#111;
    border-color:#111;
    color:white;
}

.inquilinos-scroll{
    max-height:400px;
    overflow-y:auto;
    padding-right:5px;
}

.inquilinos-scroll::-webkit-scrollbar{
    width:6px;
}

.inquilinos-scroll::-webkit-scrollbar-thumb{
    background:#555;
    border-radius:10px;
}

/* =========================================
BOTON LOGOUT
========================================= */

.logout-btn{

    width:58px;
    height:58px;

    background:white;

    border-radius:50%;

    display:flex;
    align-items:center;
    justify-content:center;

    box-shadow:0 12px 30px rgba(0,0,0,.08);

    text-decoration:none;

    transition:.3s ease;

}

.logout-btn:hover{

    transform:translateY(-3px);

}

.logout-btn img{

    width:24px;
    height:24px;
    object-fit:contain;

}

.footer{
    margin-top:35px;
    text-align:center;
    color:#6b7280;
}

</style>

</head>

<body>

<div class="overlay" id="overlay"></div>

<div class="dashboard">

        <aside class="sidebar">

        <!-- CALENDARIO -->
        <div class="dark-panel">

            <h2>
                Calendario
            </h2>

            <form method="GET">

                <?php if(!empty($filtroPrioridad)): ?>

                    <input
                        type="hidden"
                        name="prioridad"
                        value="<?= htmlspecialchars($filtroPrioridad) ?>"
                    >

                <?php endif; ?>

                <input
                type="text"
                id="calendarInput"
                style="display:none;"
            >

            </form>

        </div>

        <!-- INQUILINOS -->
        <div class="dark-panel">

            <h2>
                Inquilinos Urgentes
            </h2>

            <!-- FILTRO -->
            <div class="priority-filter">

                <form method="GET">

                    <?php if(!empty($filtroFecha)): ?>

                        <input
                            type="hidden"
                            name="fecha"
                            value="<?= htmlspecialchars($filtroFecha) ?>"
                        >

                    <?php endif; ?>

                    <select
                        name="prioridad"
                        onchange="this.form.submit()"
                    >

                        <option value="">
                            Filtrar prioridad
                        </option>

                        <option
                            value="Alta"
                            <?= ($filtroPrioridad == 'Alta')
                                ? 'selected' : '' ?>
                        >
                            Alta
                        </option>

                        <option
                            value="Media"
                            <?= ($filtroPrioridad == 'Media')
                                ? 'selected' : '' ?>
                        >
                            Media
                        </option>

                        <option
                            value="Baja"
                            <?= ($filtroPrioridad == 'Baja')
                                ? 'selected' : '' ?>
                        >
                            Baja
                        </option>

                    </select>

                </form>

            </div>

            <div class="inquilinos-scroll">

                <!-- LISTA -->
                <?php while($urgente = $resultUrgentes->fetch_assoc()): ?>

                    <div class="user-alert">

                    <img 
                        src="<?php echo !empty($urgente['ImagenUsuario']) 
                        ? '../images/person/' . $urgente['ImagenUsuario'] 
                        : '../images/icons/Usuario.png'; ?>"
                    >

                        <div>

                            <strong>

                                <?= htmlspecialchars(
                                    $urgente['NombreUsuario'] . ' ' .
                                    $urgente['ApellidoP']
                                ) ?>

                            </strong>

                            <small>

                                <?= htmlspecialchars(
                                    $urgente['Titulo']
                                ) ?>

                            </small>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>  

        </div>

    </aside>

    <div class="main-panel">

        <!-- TOPBAR -->
        <div class="topbar">

            <div class="topbar-left">

                <h1>
                    Reportes del Sistema
                </h1>

                <p class="subtitle">
                    Gestiona reportes de Inquilinos.
                </p>

            </div>

            <!-- USUARIO -->
            <!-- CONTENEDOR USER + LOGOUT -->
            <div style="display:flex; align-items:center; gap:15px;">

                <!-- BOTON LOGOUT -->
                <a href="Login.php" class="logout-btn">

                    <img
                        src="../images/icons/Cerrar_Oscuro.png"
                        alt="Cerrar sesión"
                    >

                </a>

                        <!-- USUARIO -->
                        <div class="user-box">

                            <img
                                src="<?= htmlspecialchars($imagenUsuario) ?>"
                                alt="Usuario"
                                class="user-avatar"
                            >

                            <div class="user-info">

                                <small>
                                    En uso por
                                </small>

                                <strong>
                                    <?= htmlspecialchars($nombreCompleto) ?>
                                </strong>

                            </div>

                        </div>

                    </div>

                    </div>

                    <!-- REPORTES -->
                    <div class="reports-container">

                        <?php while($reporte = $resultReportes->fetch_assoc()): ?>

                            <?php

                            $estadoClase = str_replace(
                                " ",
                                "",
                                $reporte['Estado']
                            );

                            $prioridadClase = $reporte['Prioridad'];

                            $evidencia = !empty($reporte['Evidencia'])
                                ? '../images/reports/' . $reporte['Evidencia']
                                : '../images/default/default-report.jpg';

                            ?>

                            <div class="report-card">

                                <!-- IMAGEN -->
                                <img
                                    src="<?= htmlspecialchars($evidencia) ?>"
                                    class="report-image"
                                >

                                <!-- INFO -->
                                <div class="report-info">

                                    <h2>
                                        <?= htmlspecialchars($reporte['Titulo']) ?>
                                    </h2>

                                    <p>
                                        <?= htmlspecialchars($reporte['Descripcion']) ?>
                                    </p>

                                    <p>

                                        <strong>
                                            Inquilino:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $reporte['NombreUsuario'] . ' ' .
                                            $reporte['ApellidoP']
                                        ) ?>

                                    </p>

                                    <p>

                                        <strong>
                                            Propiedad:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $reporte['NumeroIdentificador']
                                        ) ?>

                                    </p>

                                    <p>

                                        <strong>
                                            Prioridad:
                                        </strong>

                                        <span class="<?= $prioridadClase ?>">

                                            <?= htmlspecialchars(
                                                $reporte['Prioridad']
                                            ) ?>

                                        </span>

                                    </p>

                                    <div class="report-footer">

                                        <div class="status <?= $estadoClase ?>">

                                            <?= htmlspecialchars(
                                                $reporte['Estado']
                                            ) ?>

                                        </div>

                                        <button
                                            class="btn-attend openModalBtn"
                                            data-report="<?= $reporte['idReporte'] ?>"
                                        >

                                            Atender Reporte

                                        </button>

                            </div>

                        </div>

                    </div>

                 <?php endwhile; ?>

            </div>
            
            <!-- FOOTER -->
            <footer class="footer">

                <p>

                    © 2026 DiamondsCorporation.
                    Todos los derechos reservados.

                </p>

            </footer>

        </div>

</div>

<!-- =========================================
MODAL
========================================= -->

<form method="POST" id="formAtenderReporte">

<input
    type="hidden"
    name="idReporte"
    id="idReporteInput"
>

<div class="modal" id="productsModal">

    <div class="modal-header">

        <h2>
            Seleccionar Productos
        </h2>

        <button
            type="button"
            class="close-modal"
            id="closeModal"
        >
            ✕
        </button>

    </div>

    <div class="products-grid">

        <?php while($producto = $resultProductos->fetch_assoc()): ?>

            <?php

            $imagenProducto = !empty($producto['Imagen'])
                ? "../../" . $producto['Imagen']
                : '../images/default/default-product.png';

            ?>

            <div
                class="product-card"
                data-id="<?= $producto['idProducto'] ?>"
                data-stock="<?= $producto['CantidadDisponible'] ?>"
            >

                <img
                    src="<?= htmlspecialchars($imagenProducto) ?>"
                    class="product-image"
                >

                <div class="product-content">

                    <h3>
                        <?= htmlspecialchars(
                            $producto['NombreProducto']
                        ) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars(
                            $producto['Descripcion']
                        ) ?>
                    </p>

                    <p>

                        <strong>
                            Stock:
                        </strong>

                        <?= $producto['CantidadDisponible'] ?>

                    </p>

                    <div class="qty-control">

                        <button
                            type="button"
                            class="qty-btn minus"
                        >
                            -
                        </button>

                        <span class="qty-number">
                            1
                        </span>

                        <button
                            type="button"
                            class="qty-btn plus"
                        >
                            +
                        </button>

                    </div>

                    <button
                        type="button"
                        class="btn-add"
                    >

                        Agregar Producto

                    </button>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

    <div class="modal-footer">

        <button
            type="submit"
            name="atenderReporte"
            class="btn-finish-report"
        >

            Atender Reporte

        </button>

    </div>

</div>

</form>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

flatpickr("#calendarInput", {

    inline: true,
    dateFormat: "Y-m-d",
    defaultDate:
    "<?= !empty($filtroFecha)
        ? $filtroFecha
        : date('Y-m-d') ?>",

    onChange: function(selectedDates, dateStr){

        window.location.href =
            "?fecha=" + dateStr;

    }

});

/* =========================================
MODAL
========================================= */

const overlay =
    document.getElementById('overlay');

const modal =
    document.getElementById('productsModal');

const closeModal =
    document.getElementById('closeModal');

const openButtons =
    document.querySelectorAll('.openModalBtn');

const idReporteInput =
    document.getElementById('idReporteInput');

openButtons.forEach(button => {

    button.addEventListener('click', () => {

        idReporteInput.value =
            button.dataset.report;

        modal.classList.add('active');

        overlay.classList.add('active');

    });

});

function closeAll(){

    modal.classList.remove('active');

    overlay.classList.remove('active');

}

closeModal.addEventListener(
    'click',
    closeAll
);

overlay.addEventListener(
    'click',
    closeAll
);

/* =========================================
PRODUCTOS
========================================= */

const productosSeleccionados = [];

/* =========================================
RECORRER PRODUCTOS
========================================= */

document.querySelectorAll('.product-card').forEach(card => {

    const minusBtn = card.querySelector('.minus');
    const plusBtn = card.querySelector('.plus');
    const qtyNumber = card.querySelector('.qty-number');
    const addBtn = card.querySelector('.btn-add');

    const idProducto = Number(card.dataset.id);

    const stockDisponible = Number(card.dataset.stock);

    let cantidadActual = 1;

    /* =====================================
    SUMAR
    ===================================== */

    plusBtn.addEventListener('click', () => {

        if(cantidadActual < stockDisponible){

            cantidadActual++;

            qtyNumber.textContent = cantidadActual;

        }

    });

    /* =====================================
    RESTAR
    ===================================== */

    minusBtn.addEventListener('click', () => {

        if(cantidadActual > 1){

            cantidadActual--;

            qtyNumber.textContent = cantidadActual;

        }

    });

    /* =====================================
    AGREGAR PRODUCTO
    ===================================== */

    addBtn.addEventListener('click', () => {

        const productoExistente =
            productosSeleccionados.find(
                producto => producto.id === idProducto
            );

        let cantidadYaAgregada = 0;

        if(productoExistente){

            cantidadYaAgregada =
                productoExistente.cantidad;

        }

        /* VALIDAR STOCK */

        if(
            (cantidadYaAgregada + cantidadActual)
            > stockDisponible
        ){

            alert(
                `Solo existen ${stockDisponible} unidades disponibles`
            );

            return;

        }

        /* SI YA EXISTE -> SUMAR */

        if(productoExistente){

            productoExistente.cantidad += cantidadActual;

        }else{

            productosSeleccionados.push({

                id: idProducto,
                cantidad: cantidadActual

            });

        }

        /* OBTENER TOTAL FINAL */

        const totalFinal =
            productoExistente
            ? productoExistente.cantidad
            : cantidadActual;

        /* ESTADO VISUAL */

        addBtn.textContent =
            `Agregado (${totalFinal}) ✓`;

        addBtn.style.background = "#16a34a";

    });

});

/* =========================================
ENVIAR PRODUCTOS AL FORMULARIO
========================================= */


document.getElementById('formAtenderReporte').addEventListener('submit', function(e){

    if(productosSeleccionados.length === 0){

        e.preventDefault();

        alert(
            'Debes seleccionar al menos un producto'
        );

        return;

    }

    /* LIMPIAR INPUTS ANTERIORES */

    document.querySelectorAll('.producto-hidden').forEach(input => {

        input.remove();

    });

    /* CREAR INPUTS */

    productosSeleccionados.forEach((producto, index) => {

        const inputId = document.createElement('input');

        inputId.type = 'hidden';

        inputId.name =
            `productos[${index}][id]`;

        inputId.value = producto.id;

        inputId.classList.add('producto-hidden');

        this.appendChild(inputId);

        const inputCantidad = document.createElement('input');

        inputCantidad.type = 'hidden';

        inputCantidad.name =
            `productos[${index}][cantidad]`;

        inputCantidad.value = producto.cantidad;

        inputCantidad.classList.add('producto-hidden');

        this.appendChild(inputCantidad);

    });

});

</script>

</body>

</html>