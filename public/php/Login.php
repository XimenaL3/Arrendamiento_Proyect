<?php
session_start();
ob_start();

require_once "../../includes/Conexion.php";

if (!$conn) {
    die("Error de conexión a la base de datos");
}

$mensajeLogin = "";
$mensajeRegister = "";

/* ================================
   LOGIN
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {

    $correo = $_POST['email'];
    $contrasena = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT 
            u.idUsuario,
            p.idPersona,
            p.Nombre,
            u.Usuario,
            u.Contrasena,
            u.idRol
        FROM Usuarios u
        INNER JOIN Personas p ON p.idPersona = u.idPersona
        WHERE p.Correo = ?
    ");

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->bind_result($idUsuario, $idPersona, $nombre, $usuarioDB, $hashDB, $rol);
        $stmt->fetch();

        $loginOk = false;

        if (password_verify($contrasena, $hashDB)) {
            $loginOk = true;
        }
        elseif ($contrasena === $hashDB) {
            $loginOk = true;

            $nuevoHash = password_hash($contrasena, PASSWORD_BCRYPT);

            $update = $conn->prepare("
                UPDATE Usuarios 
                SET Contrasena = ?
                WHERE idUsuario = ?
            ");

            $update->bind_param("si", $nuevoHash, $idUsuario);
            $update->execute();
            $update->close();
        }

        if ($loginOk) {

            $_SESSION['idUsuario'] = $idUsuario;
            $_SESSION['idPersona'] = $idPersona;
            $_SESSION['usuario'] = $usuarioDB;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['rol'] = $rol;

            ob_clean();

            switch ($rol) {
                case 1:
                    header("Location: Interface_Trabajadores.php");
                    exit();
                case 2:
                    header("Location: Interface_Cobros_CJ.php");
                    exit();
                case 3:
                    header("Location: InicioEmpleados.php");
                    exit();
                case 4:
                    header("Location: Interface_Cobros_C.php");
                    exit();
            }

        } else {
            $mensajeLogin = "Contraseña incorrecta.";
        }

    } else {

    // =========================================
    // LOGIN INQUILINO
    // =========================================

    $stmtInq = $conn->prepare("
        SELECT
            i.idInquilino,
            p.idPersona,
            p.Nombre,
            p.ApellidoP,
            p.Correo,
            p.Telefono,
            p.Imagen
        FROM Inquilinos i

        INNER JOIN Personas p
            ON p.idPersona = i.idPersona

        WHERE p.Correo = ?
    ");

    $stmtInq->bind_param("s", $correo);

    $stmtInq->execute();

    $stmtInq->store_result();

    if ($stmtInq->num_rows > 0) {

        $stmtInq->bind_result(
            $idInquilino,
            $idPersonaInq,
            $nombreInq,
            $apellidoPInq,
            $correoInq,
            $telefonoInq,
            $imagenInq
        );

        $stmtInq->fetch();

        // =========================================
        // CONTRASEÑA TEMPORAL
        // =========================================
        // USAREMOS EL TELEFONO COMO PASSWORD
        // =========================================

        if (trim($contrasena) === trim($telefonoInq)) {

            $_SESSION['idInquilino'] = $idInquilino;

            $_SESSION['idPersona'] = $idPersonaInq;

            $_SESSION['nombre'] = $nombreInq;

            $_SESSION['tipo_usuario'] = 'inquilino';

            $_SESSION['imagen'] = $imagenInq;

            ob_clean();

            header("Location: Interface_Resumen.php");
            exit();

        } else {

            $mensajeLogin = "Contraseña incorrecta.";
        }

    } else {

        $mensajeLogin = "Usuario no encontrado.";
    }

    $stmtInq->close();
}

    $stmt->close();
}

/* ================================
   REGISTER
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'register') {

    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = $_POST['apellidoM'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];

    $contrasena = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $imagen = "default.png";

    $stmt = $conn->prepare("CALL sp_RegistrarTrabajador(?,?,?,?,?,?,?,?)");

    $stmt->bind_param(
        "ssssssss",
        $nombre,
        $apellidoP,
        $apellidoM,
        $telefono,
        $correo,
        $imagen,
        $usuario,
        $contrasena
    );

    if ($stmt->execute()) {
        $mensajeRegister = "Cuenta creada correctamente.";
    } else {
        $mensajeRegister = "Error al registrar usuario.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sunlight Gardens | Login</title>

<link rel="stylesheet" href="../css/Login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- LEFT -->
    <aside class="left-panel">

        <div class="brand">
            <h2>Sunlight<br>Gardens</h2>
            <p>Plataforma administrativa para la gestión residencial.</p>
        </div>

        <div class="menu-box">

            <button class="menu-btn login active-btn" id="loginToggle">
                <i class="fa-solid fa-right-to-bracket"></i>
                Iniciar Sesión
            </button>

            <button class="menu-btn signin" id="registerToggle">
                <i class="fa-regular fa-user"></i>
                Crear Cuenta
            </button>

        </div>

    </aside>

    <!-- RIGHT -->
    <main class="right-panel">

        <!-- LOGIN -->
        <div class="form-container active-form" id="loginForm">

            <h1>Bienvenido</h1>

            <?php if ($mensajeLogin): ?>
                <p style="color:red;text-align:center;"><?php echo $mensajeLogin; ?></p>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="action" value="login">

                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="Correo electrónico" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Contraseña / Telefono" required>
                </div>

                <button class="login-btn" type="submit">Ingresar</button>

            </form>

        </div>

        <!-- REGISTER -->
        <div class="form-container" id="registerForm">

            <h1>Crear Cuenta</h1>

            <?php if ($mensajeRegister): ?>
                <p style="color:green;text-align:center;"><?php echo $mensajeRegister; ?></p>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="action" value="register">

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="apellidoP" placeholder="Apellido paterno" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="apellidoM" placeholder="Apellido materno" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-phone"></i>
                    <input type="text" name="telefono" placeholder="Teléfono" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="usuario" placeholder="Usuario" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>

                <button class="login-btn" type="submit">Crear Cuenta</button>

            </form>

        </div>

    </main>

</div>

<script>

const loginToggle = document.getElementById("loginToggle");
const registerToggle = document.getElementById("registerToggle");

const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");

/* =========================
   LOGIN
========================= */

loginToggle.addEventListener("click", () => {

    loginForm.classList.add("active-form");
    registerForm.classList.remove("active-form");

    loginToggle.classList.add("active-btn");
    registerToggle.classList.remove("active-btn");

});

/* =========================
   REGISTER
========================= */

registerToggle.addEventListener("click", () => {

    registerForm.classList.add("active-form");
    loginForm.classList.remove("active-form");

    registerToggle.classList.add("active-btn");
    loginToggle.classList.remove("active-btn");

});

</script>

</body>
</html>