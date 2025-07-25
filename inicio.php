<?php
session_start();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$tipo_usuario = '';
if (isset($_GET['tipo'])) {
    if ($_GET['tipo'] === 'cliente') {
        $tipo_usuario = 3;
    } elseif ($_GET['tipo'] === 'administrador') {
        $tipo_usuario = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulario de registro e inicio de sesión</title>

    <link rel="stylesheet" href="style.css" />
</head>
<body class="body_inicio">

    <!-- FORMULARIO DE LOGIN -->
    <div class="container-form login">
        <div class="information">
            <div class="info-childs">
                <img class="logo" src="img/Presentación1.png" alt="Logo del restaurante" />
                <h2>¡¡Bienvenido Nuevamente!!</h2>
                <p>Si aún no tienes una cuenta, regístrate aquí</p>
                <button type="button" id="sign-up">Registrarse</button>
            </div>
        </div>
        <div class="form-information">
            <div class="form-information-childs">
                <h2>Iniciar Sesión</h2>

                <?php if (!empty($error)): ?>
                    <div class="mensaje-error"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form class="form" action="login.php" method="POST" autocomplete="on">
                    <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($tipo_usuario); ?>" />
                    <label>
                        <input type="email" placeholder="Ingresa tu email" name="email" autocomplete="username" required />
                    </label>
                    <label>
                        <input type="password" placeholder="Contraseña" name="contrasena" autocomplete="current-password" required />
                    </label>
                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <!-- FORMULARIO DE REGISTRO -->
    <div class="container-form register hide">
        <div class="information">
            <div class="info-childs">
                <img class="logo" src="img/Presentación1.png" alt="Logo restaurante" />
                <h2>Bienvenido</h2>
                <p>Para unirte al mejor servicio, regístrate con tus datos</p>
                <button type="button" id="sign-in">Iniciar sesión</button>
            </div>
        </div>
        <div class="form-information">
            <div class="form-information-childs">
                <h2>Crear una Cuenta</h2>

                <form class="form form-register" action="register.php" method="POST" autocomplete="on">
                    <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($tipo_usuario ?: 3); ?>">

                    <label>
                        <input type="text" placeholder="Nombre" name="nombre_usuario" autocomplete="given-name" required>
                    </label>

                    <label>
                        <input type="text" placeholder="Apellido" name="apellido" autocomplete="family-name" required>
                    </label>

                    <label>
                        <input type="text" placeholder="Dirección" name="direccion" autocomplete="street-address" required>
                    </label>

                    <label>
                        <input type="tel" placeholder="Teléfono" name="telefono" autocomplete="tel" required>
                    </label>

                    <label>
                        <input type="text" placeholder="Cédula" name="cedula" required>
                    </label>

                    <label>
                        <input type="email" placeholder="Email" name="email" autocomplete="email" required>
                    </label>

                    <label>
                        <input type="password" placeholder="Contraseña" name="contrasena" autocomplete="new-password" required>
                    </label>

                    <button type="submit">Registrarse</button>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
