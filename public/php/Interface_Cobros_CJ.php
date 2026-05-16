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

$sql = "SELECT * FROM vw_abonos_adeudos ORDER BY idAdeudo DESC";
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

    /* =========================
       ABONO (NO TOCADO)
    ========================= */
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

        /* ===== SUBIR ARCHIVO ===== */
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

        /* ===== DEBUG MYSQL REAL ===== */
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

        /* limpiar resultados del SP */
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
   LAYOUT GENERAL
========================= */

.container{
    display:block;
    width:100%;
}

.main-content{
    width:100%;
    padding:40px 40px 20px 40px;
}

/* =========================
   HEADER
========================= */

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header-right{
    display:flex;
    align-items:center;
    gap:16px;
}

/* USUARIO */
.logged-user{
    display:flex;
    align-items:center;
    gap:10px;
}

.logged-user img{
    border-radius:50%;
}

/* BOTÓN CERRAR SESIÓN */
.logout-icon{
    width:42px;
    height:42px;
    padding:10px;
    background:white;
    border-radius:50%;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.12);
    transition:0.2s ease;
    object-fit:contain;
}

.logout-icon:hover{
    transform:scale(1.05);
    box-shadow:0 4px 14px rgba(0,0,0,0.18);
}

/* =========================
   GRID ABONOS
========================= */

.abonos-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:28px;
}

/* =========================
   CARD
========================= */

.abono-card{
    background:white;
    border-radius:28px;
    padding:26px;
    box-shadow:var(--shadow);
    transition:.25s ease;
    border:1px solid #f0f0f0;
}

.abono-card:hover{
    transform:translateY(-6px);
}

/* HEADER CARD */
.abono-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.tenant{
    display:flex;
    align-items:center;
    gap:14px;
}

.tenant img{
    width:60px;
    height:60px;
    border-radius:16px;
}

/* =========================
   ESTADO
========================= */

.status{
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
}

.pending{background:#f3f4f6;color:#4b5563;}
.approved{background:#dcfce7;color:#166534;}
.rejected{background:#fee2e2;color:#991b1b;}

/* =========================
   INFO
========================= */

.abono-info p{
    display:flex;
    justify-content:space-between;
    margin-bottom:10px;
    color:var(--text-muted);
}

/* =========================
   PROGRESS BAR
========================= */

.progress-bar{
    height:9px;
    background:#eee;
    border-radius:999px;
    overflow:hidden;
    margin-top:10px;
}

.progress{
    height:100%;
    background:linear-gradient(90deg,#111,#444);
}

/* =========================
   BOTONES CARD
========================= */

.card-actions{
    display:flex;
    gap:10px;
    margin-top:15px;
}

.btn-custom{
    flex:1;
    height:44px;
    border:none;
    border-radius:14px;
    font-weight:600;
    cursor:pointer;
}

.btn-primary{background:#111;color:white;}
.btn-secondary{background:#f3f4f6;}

/* =========================
   MODAL
========================= */

.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    z-index:9999;
    justify-content:center;
    align-items:center;
}

.modal-content{
    background:white;
    padding:28px;
    border-radius:20px;
    width:420px;
    box-shadow:0 20px 60px rgba(0,0,0,0.25);
}

.modal-content h2{
    margin-bottom:15px;
}

.modal-content input,
.modal-content textarea{
    width:100%;
    margin:10px 0;
    padding:12px;
    border-radius:12px;
    border:1px solid #ddd;
    outline:none;
}

/* BOTONES MODAL */
.modal-buttons{
    display:flex;
    justify-content:space-between;
    margin-top:15px;
    gap:10px;
}

.modal-buttons button{
    flex:1;
    padding:10px;
    border:none;
    border-radius:12px;
    font-weight:600;
    cursor:pointer;
}

.modal-buttons button[type="submit"]{
    background:#111;
    color:white;
}

.modal-buttons button[type="button"]{
    background:#f3f4f6;
}

/* =========================
   OVERLAY
========================= */

.overlay{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    z-index:9998;
}

/* =========================
   FORM ELEMENTS
========================= */

.input-group{
    display:flex;
    flex-direction:column;
    margin-bottom:15px;
}

.input-group label{
    font-weight:600;
    margin-bottom:6px;
}

.input-group select{
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
    outline:none;
}

/* =========================
   PRIORIDAD
========================= */

.priority-box{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
    margin-top:10px;
}

.priority-card{
    padding:12px;
    border-radius:14px;
    border:1px solid #ddd;
    cursor:pointer;
    transition:.2s;
    background:#fafafa;
}

.priority-card:hover{
    transform:translateY(-2px);
}

.priority-card h4{
    margin:0;
}

.priority-card p{
    font-size:12px;
    color:#666;
}

/* radio oculto */
.hidden-radio{
    display:none;
}

/* colores prioridad */
.priority-high{background:#fee2e2;}
.priority-medium{background:#fef9c3;}
.priority-low{background:#dcfce7;}

</style>

</head>

<body>

<div class="container">

<main class="main-content">

<header class="top-bar">

    <div>
        <h1>Control de Abonos</h1>
        <p class="subtitle">Gestión de deudas en tiempo real</p>
    </div>

    <div class="header-right">

        <!-- CERRAR SESIÓN -->
        <img 
            src="../images/icons/Cerrar_Oscuro.png"
            alt="Cerrar Sesión"
            class="top-icon logout-icon"
            onclick="window.location.href='Login.php'"
        >

        <!-- USUARIO -->
        <div class="logged-user">

            <img src="<?= htmlspecialchars($imagenUsuario) ?>" width="45" height="45" style="border-radius:50%">

            <strong><?= htmlspecialchars($nombreCompleto) ?></strong>

        </div>

    </div>

</header>

<?php if($mensaje): ?>
<div class="message <?= $tipoMensaje ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<section>

<div class="abonos-grid">

<?php while($row = mysqli_fetch_assoc($result)):

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
        <p><span>Total</span><b>$<?= number_format($row['MontoTotal'],2) ?></b></p>
        <p><span>Pendiente</span><b>$<?= number_format($row['MontoPendiente'],2) ?></b></p>
        <p><span>Límite</span><b><?= $row['FechaLimite'] ?></b></p>
    </div>

    <!-- PROGRESO RESTAURADO -->
    <div class="progress-bar">
        <div class="progress" style="width:<?= $porcentaje ?>%"></div>
    </div>

    <div class="card-actions">

        <button class="btn-custom btn-primary"
            onclick="abrirModalAbono(<?= $row['idContrato'] ?>)">
            Abonar
        </button>

        <button class="btn-custom btn-secondary"
            onclick="abrirModalReporte(<?= $row['idInquilino'] ?>, <?= $row['idPropiedad'] ?>)">
            Reporte
        </button>

    </div>

</div>

<?php endwhile; ?>

</div>

</section>

<!-- MODAL BONITO RESTAURADO -->
<div id="modalAbono" class="modal">

    <div class="modal-content">

        <h2>Registrar Abono</h2>

        <form method="POST">

            <input type="hidden" name="accion" value="solicitar_abono">
            <input type="hidden" id="idContratoAbono" name="idContratoAbono">

            <input type="number" step="0.01" name="montoSolicitado" placeholder="Monto" required>

            <textarea name="observaciones" placeholder="Observaciones"></textarea>

            <div class="modal-buttons">

                <button type="button" onclick="cerrarModal()">Cancelar</button>
                <button type="submit">Confirmar</button>

            </div>

        </form>

    </div>

</div>

<div id="modalReporte" class="modal">

    <div class="modal-content">

        <h2>Registrar Reporte</h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="accion" value="registrar_reporte">

            <input type="hidden" id="idInquilino" name="idInquilino">
            <input type="hidden" id="idPropiedad" name="idPropiedad">

            <input type="text" name="titulo" placeholder="Título" required>

            <textarea name="descripcion" placeholder="Descripción"></textarea>

            <!-- TIPO DE REPORTE -->
            <div class="input-group">

                <label>Tipo de Reporte</label>

                <select name="tipoReporte" required>

                    <option selected disabled>
                        Selecciona una opción
                    </option>

                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Cobranza">Cobranza</option>
                    <option value="Legal">Legal</option>
                    <option value="Inventario">Inventario</option>
                    <option value="General">General</option>

                </select>

            </div>

            <!-- PRIORIDAD -->
            <h3 class="section-subtitle">Nivel de Prioridad</h3>

            <div class="priority-box">

                <label class="priority-card priority-high">
                    <input type="radio" name="prioridad" value="Alta" required class="hidden-radio">
                    <h4>Alta</h4>
                    <p>Problemas urgentes.</p>
                </label>

                <label class="priority-card priority-medium">
                    <input type="radio" name="prioridad" value="Media" class="hidden-radio">
                    <h4>Media</h4>
                    <p>Situaciones importantes.</p>
                </label>

                <label class="priority-card priority-low">
                    <input type="radio" name="prioridad" value="Baja" class="hidden-radio">
                    <h4>Baja</h4>
                    <p>Solicitudes menores.</p>
                </label>

            </div>

            <input type="file" name="evidencia">

            <div class="modal-buttons">

                <button type="button" onclick="cerrarReporte()">Cancelar</button>
                <button type="submit">Enviar</button>

            </div>

        </form>

    </div>

</div>

<div id="overlay" class="overlay"></div>

</main>

</div>

<script>

function abrirModalAbono(idContrato){
    document.getElementById("modalAbono").style.display = "flex";
    document.getElementById("overlay").style.display = "block";
    document.getElementById("idContratoAbono").value = idContrato;
}

function cerrarModal(){
    document.getElementById("modalAbono").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

/* REPORTE */
function abrirModalReporte(idInquilino, idPropiedad){

    if(!idInquilino || !idPropiedad){
        alert("Error: datos de propiedad incompletos");
        return;
    }

    document.getElementById("modalReporte").style.display = "flex";
    document.getElementById("overlay").style.display = "block";

    document.getElementById("idInquilino").value = idInquilino;
    document.getElementById("idPropiedad").value = idPropiedad;
}

function cerrarReporte(){
    document.getElementById("modalReporte").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

document.getElementById("overlay").onclick = function(){
    cerrarModal();
    cerrarReporte();
};

</script>

</body>
</html>