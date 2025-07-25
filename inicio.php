<?php
session_start();  // Inicia para usar $_SESSION

// Inicializa la variable de error
$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];      // Si hay un mensaje de error guardado en la sesión, se asigna a la variable
    unset($_SESSION['error']);        // Luego se elimina de la sesión para que no se muestre de nuevo
}

// Determina el tipo de usuario (cliente o administrador) según el parámetro 'tipo' recibido por GET
$tipo_usuario = '';
if (isset($_GET['tipo'])) {
    if ($_GET['tipo'] === 'cliente') {
        $tipo_usuario = 3;  // Cliente
    } elseif ($_GET['tipo'] === 'administrador') {
        $tipo_usuario = 1;  // Administrador
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" /> <!-- Define codificación de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulario de registro e inicio de sesión</title>

    <link rel="stylesheet" href="style.css" /> <!-- Vinculacion a la hoja de estilos css -->
</head>
<body class="body_inicio">

    <!-- FORMULARIO DE LOGIN -->
    <div class="container-form login">
        <div class="information">
            <div class="info-childs">
                <img class="logo" src="img/Presentación1.png" alt="Logo del restaurante" />
                <h2>¡¡Bienvenido Nuevamente!!</h2>
                <p>Si aún no tienes una cuenta, regístrate aquí</p>
                <button type="button" id="sign-up">Registrarse</button> <!-- Cambia al formulario de registro -->
            </div>
        </div>

        <div class="form-information">
            <div class="form-information-childs">
                <h2>Iniciar Sesión</h2>

                <!-- Muestra mensaje de error si existe y lo muestra en pantalla -->
                <?php if (!empty($error)): ?>
                    <div class="mensaje-error"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Formulario de inicio de sesión -->

                <form class="form" action="login.php" method="POST" autocomplete="on">

                    <!-- Campo oculto para enviar el tipo de usuario -->

                    <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($tipo_usuario); ?>" />

                    <!-- Campo para email -->

                    <label>
                        <input type="email" placeholder="Ingresa tu email" name="email" autocomplete="username" required />
                    </label>

                    <!-- Campo para contraseña -->

                    <label>
                        <input type="password" placeholder="Contraseña" name="contrasena" autocomplete="current-password" required />
                    </label>

                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <!-- FORMULARIO DE REGISTRO -->

    <div class="container-form register hide"> <!-- Clase "hide" oculta el formulario de inicio -->
        <div class="information">
            <div class="info-childs">
                <img class="logo" src="img/Presentación1.png" alt="Logo restaurante" />
                <h2>Bienvenido</h2>
                <p>Para unirte al mejor servicio, regístrate con tus datos</p>
                <button type="button" id="sign-in">Iniciar sesión</button> <!-- Cambia al formulario de login -->
            </div>
        </div>

        <div class="form-information">
            <div class="form-information-childs">
                <h2>Crear una Cuenta</h2>

                <!-- campos formulario de registro -->

                <form class="form form-register" action="register.php" method="POST" autocomplete="on">

                    <!-- Campo oculto para tipo de usuario. Si no se definió, se asume cliente (3) -->

                    <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($tipo_usuario ?: 3); ?>">

                    <!-- Campo nombre -->
                    <label>
                        <input type="text" placeholder="Nombre" name="nombre_usuario" autocomplete="given-name" required>
                    </label>

                    <!-- Campo apellido -->
                    <label>
                        <input type="text" placeholder="Apellido" name="apellido" autocomplete="family-name" required>
                    </label>

                    <!-- Campo dirección -->
                    <label>
                        <input type="text" placeholder="Dirección" name="direccion" autocomplete="street-address" required>
                    </label>

                    <!-- Campo teléfono -->
                    <label>
                        <input type="tel" placeholder="Teléfono" name="telefono" autocomplete="tel" required>
                    </label>

                    <!-- Campo cédula -->
                    <label>
                        <input type="text" placeholder="Cédula" name="cedula" required>
                    </label>

                    <!-- Campo email -->
                    <label>
                        <input type="email" placeholder="Email" name="email" autocomplete="email" required>
                    </label>

                    <!-- Campo contraseña -->
                    <label>
                        <input type="password" placeholder="Contraseña" name="contrasena" autocomplete="new-password" required>
                    </label>

                    <button type="submit">Registrarse</button>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script> <!-- Script que seguramente maneja la transición entre formularios y los oculta con hide -->
</body>
</html>
