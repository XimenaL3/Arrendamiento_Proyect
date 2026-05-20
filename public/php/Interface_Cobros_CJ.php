<?php

ob_start();
session_start();

/* =========================
   VALIDACIÓN SESIÓN
========================= */

if (!isset($_SESSION['idUsuario']) || ($_SESSION['rol'] ?? 0) != 2) {
    header("Location: Login.php");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$idPersona = $_SESSION['idPersona'];

require_once "../../includes/Conexion.php";

if (!$conn) {
    die("Error de conexión a la base de datos");
}

/* =========================
   DATOS USUARIO
========================= */

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

$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($nombre, $apellidoP, $apellidoM, $imagen, $rol);
$stmt->fetch();
$stmt->close();

$nombre = $nombre ?? "Usuario";
$apellidoP = $apellidoP ?? "";
$apellidoM = $apellidoM ?? "";

$imagenUsuario = (!empty($imagen))
    ? "../images/person/" . $imagen
    : "../images/icons/Usuario.png";

$nombreCompleto = trim("$nombre $apellidoP $apellidoM");

/* =========================
   VIEW ABONOS
========================= */

$sql = "
    SELECT *
    FROM vw_abonos_adeudos
    WHERE MontoPendiente > 0
    AND Estado = 'Pendiente'
    ORDER BY idAdeudo DESC
";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error en consulta: " . mysqli_error($conn));
}

/* =========================
   VARIABLES
========================= */

$mensaje = "";
$tipoMensaje = "";

/* =========================
   POST UNIFICADO
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion === "solicitar_abono") {

        $idContrato = (int)($_POST['idContratoAbono'] ?? 0);
        $montoSolicitado = (float)($_POST['montoSolicitado'] ?? 0);
        $observaciones = trim($_POST['observaciones'] ?? '');

        $idInquilino = 0;

        $stmtInquilino = $conn->prepare("
            SELECT idInquilino
            FROM ContratosArrendamiento
            WHERE idContrato = ?
            LIMIT 1
        ");

        $stmtInquilino->bind_param("i", $idContrato);
        $stmtInquilino->execute();
        $stmtInquilino->bind_result($idInquilino);
        $stmtInquilino->fetch();
        $stmtInquilino->close();

        if ($idContrato <= 0 || $idInquilino <= 0 || $montoSolicitado <= 0) {

            $mensaje = "Completa todos los campos del abono";
            $tipoMensaje = "error";

        } else {

            try {

                $stmt = $conn->prepare("
                    CALL sp_SolicitarAbono(?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iiids",
                    $idUsuario,
                    $idContrato,
                    $idInquilino,
                    $montoSolicitado,
                    $observaciones
                );

                $stmt->execute();

                while ($stmt->more_results() && $stmt->next_result()) {
                    if ($r = $stmt->get_result()) $r->free();
                }

                $stmt->close();

                $mensaje = "Abono solicitado correctamente";
                $tipoMensaje = "success";

            } catch (Exception $e) {

                $mensaje = "Error abono: " . $e->getMessage();
                $tipoMensaje = "error";
            }
        }
    }

    elseif ($accion === "registrar_reporte") {

        try {

            $idInquilino = (int)($_POST['idInquilino'] ?? 0);
            $idPropiedad = (int)($_POST['idPropiedad'] ?? 0);

            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipoReporte = trim($_POST['tipoReporte'] ?? '');
            $prioridad = trim($_POST['prioridad'] ?? '');

            if ($idInquilino <= 0 || $idPropiedad <= 0 || $titulo === '') {
                throw new Exception("Faltan datos obligatorios");
            }

            if (!in_array($tipoReporte, ['Mantenimiento','Cobranza','Legal','Inventario','General'])) {
                throw new Exception("Tipo de reporte inválido");
            }

            if (!in_array($prioridad, ['Alta','Media','Baja'])) {
                throw new Exception("Prioridad inválida");
            }

            $evidencia = "";

            if (!empty($_FILES['evidencia']['name']) && $_FILES['evidencia']['error'] === 0) {

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

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $stmt = $conn->prepare("CALL sp_RegistrarReporte(?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new Exception("Error en prepare: " . $conn->error);
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

            $stmt->execute();

            while ($stmt->more_results() && $stmt->next_result()) {
                if ($res = $stmt->get_result()) $res->free();
            }

            $stmt->close();

            $mensaje = "Reporte enviado correctamente";
            $tipoMensaje = "success";

        } catch (Throwable $e) {

            $mensaje = "Error: " . $e->getMessage();
            $tipoMensaje = "error";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Control de Abonos</title>

<link rel="stylesheet" href="../css/style.css">

<style>

/* =========================
   GENERAL
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f7fb;
    font-family:'Segoe UI',sans-serif;
    color:#111;
}

.container{
    width:100%;
    min-height:100vh;
}

.main-content{
    padding:35px;
}

/* =========================
   HEADER
========================= */

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
}

.top-bar h1{
    font-size:40px;
    font-weight:800;
}

.subtitle{
    color:#777;
    margin-top:5px;
}

.header-right{
    display:flex;
    align-items:center;
    gap:20px;
}

.logged-user{
    display:flex;
    align-items:center;
    gap:12px;
    background:white;
    padding:8px 16px;
    border-radius:18px;
    box-shadow:0 4px 18px rgba(0,0,0,.06);
}

.logged-user img{
    border-radius:50%;
    object-fit:cover;
}

.logout-icon{
    width:48px;
    height:48px;
    padding:12px;
    background:white;
    border-radius:50%;
    cursor:pointer;
    box-shadow:0 4px 18px rgba(0,0,0,.08);
    transition:.2s;
}

.logout-icon:hover{
    transform:scale(1.05);
}

/* =========================
   CARRUSEL
========================= */

.carousel-wrapper{
    overflow-x:auto;
    padding-bottom:10px;
    margin-bottom:35px;
}

.carousel-wrapper::-webkit-scrollbar{
    height:8px;
}

.carousel-wrapper::-webkit-scrollbar-thumb{
    background:#d4d4d4;
    border-radius:20px;
}

.abonos-grid{
    display:flex;
    gap:25px;
    min-width:max-content;
}

/* =========================
   CARD
========================= */

.abono-card{
    min-width:360px;
    background:white;
    border-radius:28px;
    padding:24px;
    box-shadow:0 8px 25px rgba(0,0,0,.06);
    border:1px solid #ededed;
    transition:.25s ease;
}

.abono-card:hover{
    transform:translateY(-5px);
}

.abono-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:18px;
}

.tenant{
    display:flex;
    gap:15px;
}

.tenant img{
    width:65px;
    height:65px;
    border-radius:18px;
    object-fit:cover;
}

.tenant h3{
    font-size:19px;
}

.tenant p{
    color:#777;
    margin-top:4px;
}

.status{
    padding:7px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
}

.pending{
    background:#eef2ff;
    color:#4338ca;
}

.approved{
    background:#dcfce7;
    color:#166534;
}

.rejected{
    background:#fee2e2;
    color:#991b1b;
}

/* =========================
   INFO
========================= */

.abono-info{
    margin-top:15px;
}

.abono-info p{
    display:flex;
    justify-content:space-between;
    margin-bottom:12px;
    color:#666;
}

.abono-info b{
    color:#111;
}

/* =========================
   PROGRESS
========================= */

.progress-bar{
    width:100%;
    height:10px;
    background:#ececec;
    border-radius:999px;
    overflow:hidden;
    margin-top:15px;
}

.progress{
    height:100%;
    background:linear-gradient(90deg,#111,#555);
}

/* =========================
   BUTTONS
========================= */

.card-actions{
    display:flex;
    gap:12px;
    margin-top:22px;
}

.btn-custom{
    flex:1;
    border:none;
    height:46px;
    border-radius:15px;
    font-weight:700;
    cursor:pointer;
    transition:.2s;
}

.btn-custom:hover{
    transform:translateY(-2px);
}

.btn-primary{
    background:#111;
    color:white;
}

.btn-secondary{
    background:#eceff5;
}

/* =========================
   PANEL INFERIOR
========================= */

.dashboard-panels{
    display:grid;
    grid-template-columns:1.4fr .8fr;
    gap:25px;
    margin-top:10px;
}

.panel{
    background:white;
    border-radius:30px;
    padding:28px;
    box-shadow:0 8px 25px rgba(0,0,0,.06);
}

/* =========================
   TABLA
========================= */

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:22px;
}

.table-header h2{
    font-size:28px;
}

.custom-table{
    width:100%;
    border-collapse:collapse;
}

.custom-table th{
    text-align:left;
    padding-bottom:15px;
    color:#777;
    font-size:14px;
}

.custom-table td{
    padding:16px 0;
    border-top:1px solid #eee;
    font-weight:500;
}

.table-user{
    display:flex;
    align-items:center;
    gap:12px;
}

.table-user img{
    width:45px;
    height:45px;
    border-radius:14px;
    object-fit:cover;
}

/* =========================
   FILTROS TABLA
========================= */

.table-filters{
    display:flex;
    gap:12px;
    align-items:center;
}

.table-filters input{
    padding:12px 14px;
    border:none;
    outline:none;
    border-radius:14px;
    background:#f3f4f6;
    font-size:14px;
    transition:.2s;
}

.table-filters input:focus{
    background:white;
    box-shadow:0 0 0 2px #111;
}

/* =========================
   SCROLL TABLA
========================= */

.table-scroll{
    max-height:500px;
    overflow-y:auto;
    overflow-x:hidden;
    padding-right:6px;
}

/* SCROLL MODERNO */

.table-scroll::-webkit-scrollbar{
    width:8px;
}

.table-scroll::-webkit-scrollbar-thumb{
    background:#d1d5db;
    border-radius:20px;
}

.table-scroll::-webkit-scrollbar-track{
    background:transparent;
}

/* HEADER FIJO */

.custom-table thead{
    position:sticky;
    top:0;
    background:white;
    z-index:10;
}

/* EFECTO HOVER */

.custom-table tbody tr{
    transition:.2s;
}

.custom-table tbody tr:hover{
    background:#f8fafc;
}

/* =========================
   LOGO PANEL
========================= */

.logo-panel{
    background:#111;
    color:white;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    min-height:420px;
    overflow:hidden;
    position:relative;
}

.logo-panel img{
    width:220px;
    margin-bottom:25px;
    object-fit:contain;
}

.logo-panel h2{
    font-size:34px;
    margin-bottom:10px;
}

.logo-panel p{
    color:#d1d5db;
    line-height:1.6;
    max-width:320px;
}

/* =========================
   MODAL
========================= */

.modal{
    display:none;
    position:fixed;
    inset:0;

    justify-content:center;
    align-items:center;

    z-index:10000;
}

.modal-content{
    width:430px;
    background:#ffffff;

    border-radius:24px;
    padding:30px;

    position:relative;
    z-index:10001;

    box-shadow:
        0 25px 60px rgba(0,0,0,.35),
        0 0 0 1px rgba(255,255,255,.4);

    animation: modalScale .25s ease;
}

.modal-content h2{
    margin-bottom:18px;
}

.modal-content input,
.modal-content textarea,
.modal-content select{
    width:100%;
    margin:10px 0;
    padding:13px;
    border-radius:14px;
    border:1px solid #ddd;
    outline:none;
}

.modal-buttons{
    display:flex;
    gap:12px;
    margin-top:20px;
}

.modal-buttons button{
    flex:1;
    border:none;
    padding:13px;
    border-radius:14px;
    cursor:pointer;
    font-weight:700;
}

.modal-buttons button[type="submit"]{
    background:#111;
    color:white;
}

.modal-buttons button[type="button"]{
    background:#eef2f7;
}

.overlay{

    position:fixed;
    inset:0;

    background:rgba(0,0,0,.55);

    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);

    opacity:0;
    visibility:hidden;

    transition:.3s ease;

    z-index:9998;
}

.overlay.active{

    opacity:1;
    visibility:visible;
}

@keyframes fadeIn{

    from{
        opacity:0;
    }

    to{
        opacity:1;
    }
}

@keyframes modalScale{

    from{
        transform:scale(.9);
        opacity:0;
    }

    to{
        transform:scale(1);
        opacity:1;
    }
}

/* =========================
   PRIORIDAD
========================= */

.priority-box{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    margin-top:15px;
}

.priority-card{
    padding:14px;
    border-radius:16px;
    cursor:pointer;
    border:1px solid #e5e7eb;
}

.priority-high{
    background:#fee2e2;
}

.priority-medium{
    background:#fef9c3;
}

.priority-low{
    background:#dcfce7;
}

.hidden-radio{
    display:none;
}

/* =========================
   MENSAJES
========================= */

.message{
    padding:15px 18px;
    border-radius:15px;
    margin-bottom:25px;
    font-weight:600;
}

.success{
    background:#dcfce7;
    color:#166534;
}

.error{
    background:#fee2e2;
    color:#991b1b;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:1100px){

    .dashboard-panels{
        grid-template-columns:1fr;
    }

    .logo-panel{
        min-height:320px;
    }
}

.user-info{
    display:flex;
    flex-direction:column;
    line-height:1.2;
}

.user-label{
    font-size:12px;
    color:#777;
    font-weight:500;
}

.user-name{
    font-size:15px;
    color:#111;
}


</style>

</head>

<body>

<div class="container">

<main class="main-content">

<header class="top-bar">

    <div>
        <h1>Gestion de Cobros</h1>
        <p class="subtitle">Gestión de abonos e inquilinos</p>
    </div>

    <div class="header-right">

        <img 
            src="../images/icons/Cerrar_Oscuro.png"
            alt="Cerrar Sesión"
            class="logout-icon"
            onclick="window.location.href='Login.php'"
        >

        <div class="logged-user">

            <img 
                src="<?= htmlspecialchars($imagenUsuario) ?>" 
                width="48" 
                height="48"
            >

            <div class="user-info">

                <span class="user-label">
                    En uso por
                </span>

                <strong class="user-name">
                    <?= htmlspecialchars($nombreCompleto) ?>
                </strong>

            </div>

        </div>

    </div>

</header>

<?php if($mensaje): ?>
<div class="message <?= $tipoMensaje ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<!-- =========================
     CARRUSEL SUPERIOR
========================= -->

<div class="carousel-wrapper">

<div class="abonos-grid">

<?php
mysqli_data_seek($result, 0);

while($row = mysqli_fetch_assoc($result)):

$porcentaje = ($row['MontoTotal'] > 0)
    ? (($row['MontoTotal'] - $row['MontoPendiente']) / $row['MontoTotal']) * 100
    : 0;

$img = !empty($row['ImagenInquilino'])
    ? "../images/person/".$row['ImagenInquilino']
    : "../images/icons/Usuario.png";
?>

<div class="abono-card">

    <div class="abono-header">

        <div class="tenant">

            <img src="<?= htmlspecialchars($img) ?>">

            <div>
                <h3><?= htmlspecialchars($row['Inquilino']) ?></h3>
                <p><?= htmlspecialchars($row['Propiedad']) ?></p>
            </div>

        </div>

        <span class="status <?= strtolower($row['Estado']) ?>">
            <?= $row['Estado'] ?>
        </span>

    </div>

    <div class="abono-info">

        <p>
            <span>Total</span>
            <b>$<?= number_format($row['MontoTotal'],2) ?></b>
        </p>

        <p>
            <span>Pendiente</span>
            <b>$<?= number_format($row['MontoPendiente'],2) ?></b>
        </p>

        <p>
            <span>Fecha límite</span>
            <b><?= $row['FechaLimite'] ?></b>
        </p>

    </div>

    <div class="progress-bar">
        <div 
            class="progress"
            style="width:<?= $porcentaje ?>%">
        </div>
    </div>

    <div class="card-actions">

        <button 
            class="btn-custom btn-primary"
            onclick="abrirModalAbono(<?= $row['idContrato'] ?>)">
            Abonar
        </button>

        <button 
            class="btn-custom btn-secondary"
            onclick="abrirModalReporte(<?= $row['idInquilino'] ?>, <?= $row['idPropiedad'] ?>)">
            Reporte
        </button>

    </div>

</div>

<?php endwhile; ?>

</div>

</div>

<!-- =========================
     PANEL INFERIOR
========================= -->

<div class="dashboard-panels">

    <!-- TABLA -->
    <div class="panel">

    <div class="table-header">

        <h2>Inquilinos</h2>

        <div class="table-filters">

            <input 
                type="text"
                id="buscarInquilino"
                placeholder="Buscar inquilino..."
            >

            <input 
                type="date"
                id="filtroFecha"
            >

        </div>

    </div>

        <div class="table-scroll">

            <table class="custom-table">

                <thead>

                    <tr 
                        class="fila-inquilino"
                        data-fecha="<?= htmlspecialchars($row['FechaLimite']) ?>"
                    >
                        <th>Inquilino</th>
                        <th>Propiedad</th>
                        <th>Pendiente</th>
                        <th>Estado</th>
                    </tr>

                </thead>

                <tbody>

                <?php
                mysqli_data_seek($result, 0);

                while($row = mysqli_fetch_assoc($result)):

                $img = !empty($row['ImagenInquilino'])
                    ? "../images/person/".$row['ImagenInquilino']
                    : "../images/icons/Usuario.png";
                ?>

                <tr 
                    class="fila-inquilino"
                    data-fecha="<?= htmlspecialchars($row['FechaLimite']) ?>"
                >
                    <td>

                        <div class="table-user">

                            <img src="<?= htmlspecialchars($img) ?>">

                            <span><?= htmlspecialchars($row['Inquilino']) ?></span>

                        </div>

                    </td>

                    <td><?= htmlspecialchars($row['Propiedad']) ?></td>

                    <td>$<?= number_format($row['MontoPendiente'],2) ?></td>

                    <td><?= htmlspecialchars($row['Estado']) ?></td>

                </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- LOGO -->
    <div class="panel logo-panel">

        <img src="../images/icons/Logo_Oscuro.png">

        <h2>Sunlight Garden</h2>

        <p>
            Sistema administrativo moderno para la gestión
            de propiedades, inquilinos y control de pagos.
        </p>

    </div>

</div>

<!-- =========================
     MODAL ABONO
========================= -->

<div id="modalAbono" class="modal">

    <div class="modal-content">

        <h2>Registrar Abono</h2>

        <form method="POST">

            <input type="hidden" name="accion" value="solicitar_abono">

            <input 
                type="hidden" 
                id="idContratoAbono" 
                name="idContratoAbono"
            >

            <input 
                type="number"
                step="0.01"
                name="montoSolicitado"
                placeholder="Monto"
                required
            >

            <textarea 
                name="observaciones"
                placeholder="Observaciones">
            </textarea>

            <div class="modal-buttons">

                <button type="button" onclick="cerrarModal()">
                    Cancelar
                </button>

                <button type="submit">
                    Confirmar
                </button>

            </div>

        </form>

    </div>

</div>

<div id="overlay" class="overlay"></div>
<!-- =========================
     MODAL REPORTE
========================= -->

<div id="modalReporte" class="modal">

    <div class="modal-content">

        <h2>Registrar Reporte</h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="accion" value="registrar_reporte">

            <input type="hidden" id="idInquilino" name="idInquilino">

            <input type="hidden" id="idPropiedad" name="idPropiedad">

            <input 
                type="text"
                name="titulo"
                placeholder="Título"
                required
            >

            <textarea 
                name="descripcion"
                placeholder="Descripción">
            </textarea>

            <select name="tipoReporte" required>

                <option selected disabled>
                    Tipo de reporte
                </option>

                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Cobranza">Cobranza</option>
                <option value="Legal">Legal</option>
                <option value="Inventario">Inventario</option>
                <option value="General">General</option>

            </select>

            <h3 style="margin-top:15px;">Prioridad</h3>

            <div class="priority-box">

                <label class="priority-card priority-high">

                    <input 
                        type="radio"
                        name="prioridad"
                        value="Alta"
                        required
                        class="hidden-radio"
                    >

                    <h4>Alta</h4>

                </label>

                <label class="priority-card priority-medium">

                    <input 
                        type="radio"
                        name="prioridad"
                        value="Media"
                        class="hidden-radio"
                    >

                    <h4>Media</h4>

                </label>

                <label class="priority-card priority-low">

                    <input 
                        type="radio"
                        name="prioridad"
                        value="Baja"
                        class="hidden-radio"
                    >

                    <h4>Baja</h4>

                </label>

            </div>

            <input type="file" name="evidencia">

            <div class="modal-buttons">

                <button type="button" onclick="cerrarReporte()">
                    Cancelar
                </button>

                <button type="submit">
                    Enviar
                </button>

            </div>

        </form>

    </div>

</div>

</main>

</div>

<script>

function abrirModalAbono(idContrato){

    document.getElementById("modalAbono").style.display = "flex";

    document
        .getElementById("overlay")
        .classList.add("active");

    document.getElementById("idContratoAbono").value = idContrato;
}

function cerrarModal(){

    document.getElementById("modalAbono").style.display = "none";

    document
        .getElementById("overlay")
        .classList.remove("active");
}

function abrirModalReporte(idInquilino, idPropiedad){

    if(!idInquilino || !idPropiedad){
        alert("Error: datos incompletos");
        return;
    }

    document.getElementById("modalReporte").style.display = "flex";

    document
        .getElementById("overlay")
        .classList.add("active");

    document.getElementById("idInquilino").value = idInquilino;
    document.getElementById("idPropiedad").value = idPropiedad;
}

function cerrarReporte(){

    document.getElementById("modalReporte").style.display = "none";

    document
        .getElementById("overlay")
        .classList.remove("active");
}

document.getElementById("overlay").onclick = function(){

    cerrarModal();
    cerrarReporte();
};

/* =========================
   FILTRO EN TIEMPO REAL
========================= */

const buscador = document.getElementById("buscarInquilino");
const filtroFecha = document.getElementById("filtroFecha");

function filtrarTabla(){

    const texto = buscador.value.toLowerCase();
    const fecha = filtroFecha.value;

    const filas = document.querySelectorAll(".fila-inquilino");

    filas.forEach(fila => {

        const contenido = fila.innerText.toLowerCase();
        const fechaFila = fila.dataset.fecha;

        let mostrar = true;

        /* FILTRO TEXTO */
        if(!contenido.includes(texto)){
            mostrar = false;
        }

        /* FILTRO FECHA */
        if(fecha && fechaFila !== fecha){
            mostrar = false;
        }

        fila.style.display = mostrar ? "" : "none";
    });
}

buscador.addEventListener("keyup", filtrarTabla);

filtroFecha.addEventListener("change", filtrarTabla);

</script>

</body>
</html>