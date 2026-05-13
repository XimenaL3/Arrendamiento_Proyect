<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Sunlight Gardens | Login
    </title>

    <!-- GOOGLE FONTS -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <!-- CSS -->
    <link
        rel="stylesheet"
        href="../css/Login.css"
    >

</head>

<body>

    <div class="container">

        <!-- =====================================
        PANEL IZQUIERDO
        ====================================== -->

        <aside class="left-panel">

            <div class="brand">

                <h2>
                    Sunlight
                    <br>
                    Gardens
                </h2>

                <p>
                    Plataforma administrativa para la gestión
                    residencial, mantenimiento y control interno.
                </p>

            </div>

            <!-- BOTONES -->
            <div class="menu-box">

                <button
                    class="menu-btn login active-btn"
                    id="loginToggle"
                >

                    <i class="fa-solid fa-right-to-bracket"></i>

                    Iniciar Sesión

                </button>

                <button
                    class="menu-btn signin"
                    id="registerToggle"
                >

                    <i class="fa-regular fa-user"></i>

                    Crear Cuenta

                </button>

            </div>

        </aside>

        <!-- =====================================
        PANEL DERECHO
        ====================================== -->

        <main class="right-panel">

            <!-- =====================================
            LOGIN FORM
            ====================================== -->

            <div
                class="form-container active-form"
                id="loginForm"
            >

                <div class="icon-circle">

                    <i class="fa-regular fa-user"></i>

                </div>

                <h1>
                    Bienvenido
                </h1>

                <form>

                    <!-- EMAIL -->
                    <div class="input-group">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            placeholder="Correo electrónico"
                            required
                        >

                    </div>

                    <!-- PASSWORD -->
                    <div class="input-group">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            placeholder="Contraseña"
                            required
                        >

                    </div>

                    <!-- OPCIONES -->
                    <div class="options-row">

                        <label class="remember-me">

                            <input type="checkbox">

                            Recordarme

                        </label>

                        <a
                            href="#"
                            class="forgot"
                        >
                            ¿Olvidaste tu contraseña?
                        </a>

                    </div>

                    <!-- BOTON -->
                    <button
                        type="submit"
                        class="login-btn"
                    >

                        Ingresar

                    </button>

                </form>

                <!-- REDES -->
                <div class="social">

                    <div>

                        <i class="fa-brands fa-google"></i>

                        Google

                    </div>

                    <div>

                        <i class="fa-brands fa-facebook-f"></i>

                        Facebook

                    </div>

                    <div>

                        <i class="fa-brands fa-microsoft"></i>

                        Microsoft

                    </div>

                </div>

            </div>

            <!-- =====================================
            REGISTER FORM
            ====================================== -->

            <div
                class="form-container"
                id="registerForm"
            >

                <div class="icon-circle">

                    <i class="fa-solid fa-user-plus"></i>

                </div>

                <h1>
                    Crear Cuenta
                </h1>

                <form>

                    <!-- NAME -->
                    <div class="input-group">

                        <i class="fa-solid fa-user"></i>

                        <input
                            type="text"
                            placeholder="Nombre completo"
                            required
                        >

                    </div>

                    <!-- EMAIL -->
                    <div class="input-group">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            placeholder="Correo electrónico"
                            required
                        >

                    </div>

                    <!-- PASSWORD -->
                    <div class="input-group">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            placeholder="Crear contraseña"
                            required
                        >

                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="input-group">

                        <i class="fa-solid fa-shield-halved"></i>

                        <input
                            type="password"
                            placeholder="Confirmar contraseña"
                            required
                        >

                    </div>

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        class="login-btn"
                    >

                        Crear Cuenta

                    </button>

                </form>

            </div>

        </main>

    </div>

    <!-- =====================================
    SCRIPT
    ====================================== -->

    <script>

        const loginToggle = document.getElementById("loginToggle");

        const registerToggle = document.getElementById("registerToggle");

        const loginForm = document.getElementById("loginForm");

        const registerForm = document.getElementById("registerForm");

        loginToggle.addEventListener("click", () => {

            loginForm.classList.add("active-form");

            registerForm.classList.remove("active-form");

            loginToggle.classList.add("active-btn");

            registerToggle.classList.remove("active-btn");

        });

        registerToggle.addEventListener("click", () => {

            registerForm.classList.add("active-form");

            loginForm.classList.remove("active-form");

            registerToggle.classList.add("active-btn");

            loginToggle.classList.remove("active-btn");

        });

    </script>

</body>

</html>