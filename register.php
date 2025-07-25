<?php
// Incluye la clase Rutas que contiene las URLs necesarias para acceder a la API
require_once('Rutas.php');

// Inicializa una variable para almacenar posibles errores
$error = null;

// Verifica si el formulario fue enviado usando el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitiza y recoge los datos del formulario para prevenir inyecciones y XSS
    $nombre_usuario = isset($_POST["nombre_usuario"]) ? htmlspecialchars($_POST["nombre_usuario"]) : '';
    $apellido = isset($_POST["apellido"]) ? htmlspecialchars($_POST["apellido"]) : '';
    $direccion = isset($_POST["direccion"]) ? htmlspecialchars($_POST["direccion"]) : '';
    $telefono = isset($_POST["telefono"]) ? htmlspecialchars($_POST["telefono"]) : '';
    $cedula = isset($_POST["cedula"]) ? htmlspecialchars($_POST["cedula"]) : '';
    $email = isset($_POST["email"]) ? filter_var($_POST["email"], FILTER_SANITIZE_EMAIL) : '';
    $contrasena = isset($_POST["contrasena"]) ? $_POST["contrasena"] : '';
    $tipo_usuario = isset($_POST["tipo_usuario"]) ? (int)$_POST["tipo_usuario"] : 3; // Por defecto, tipo 3 (cliente)

    // Crea una instancia de la clase Rutas para obtener la URL del API de registro
    $rutas = new Rutas();

    // Crea un array con los datos del formulario
    $dataArray = [
        "nombre_usuario" => $nombre_usuario,
        "apellido" => $apellido,
        "direccion" => $direccion,
        "telefono" => $telefono,
        "cedula" => $cedula,
        "email" => $email,
        "contrasena" => $contrasena,
        "tipo_usuario" => $tipo_usuario
    ];

    // Codifica los datos como JSON para enviarlos a la API
    $data = json_encode($dataArray);

    // Inicia la sesión cURL para enviar la petición a la API
    $ch = curl_init($rutas->getRegisterApiUrl());

    // Configura cURL para enviar datos por POST
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    // Ejecuta la solicitud cURL y guarda la respuesta
    $res = curl_exec($ch);

    // Si hubo un error en la conexión con la API, se almacena
    if ($res === false) {
        $error = "Error en la conexión con la API: " . curl_error($ch);
    }

    // Cierra la sesión cURL
    curl_close($ch);

    // Decodifica la respuesta JSON de la API
    $response = json_decode($res, true);

    // Si la respuesta es exitosa, redirige al usuario
    if ($response && isset($response["status"]) && $response["status"] === "success") {
        header("Location: inicio.php?tipo=administrador");
        exit;
    } else {
        // Si hubo error, muestra el mensaje de la API o uno genérico
        $error = isset($response['message']) ? $response['message'] : "Error en el registro";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registro</title>
    <!-- Ruta del archivo de estilos (ajustada para acceder correctamente desde localhost) -->
    <link rel="stylesheet" href="/evidencia/style.css" />
</head>
<body>
    <h2>Registro</h2>

    <!-- Formulario de registro con los campos-->
    <form method="POST" action="" autocomplete="on">
        <label>Nombre:</label>
        <input type="text" name="nombre_usuario" autocomplete="given-name" required />
        <br />

        <label>Apellido:</label>
        <input type="text" name="apellido" autocomplete="family-name" required />
        <br />

        <label>Dirección:</label>
        <input type="text" name="direccion" autocomplete="street-address" required />
        <br />

        <label>Teléfono:</label>
        <input type="tel" name="telefono" autocomplete="tel" required />
        <br />

        <label>Cédula:</label>
        <input type="text" name="cedula" required />
        <br />

        <label>Email:</label>
        <input type="email" name="email" autocomplete="email" required />
        <br />

        <label>Contraseña:</label>
        <input type="password" name="contrasena" autocomplete="new-password" required />
        <br />

        <!-- Campo oculto con el tipo de usuario por defecto (3 = cliente) -->
        <input type="hidden" name="tipo_usuario" value="3" />

        <button type="submit">Registrarse</button>
    </form>

    <!-- Muestra un mensaje de error si ocurre alguno -->
    <?php if ($error): ?>
        <div style="color:red; background:#fdd; padding:1em; margin-top:1em;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
</body>
</html>
