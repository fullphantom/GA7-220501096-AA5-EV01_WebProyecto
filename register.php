<?php
require_once('Rutas.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = isset($_POST["nombre_usuario"]) ? htmlspecialchars($_POST["nombre_usuario"]) : '';
    $apellido = isset($_POST["apellido"]) ? htmlspecialchars($_POST["apellido"]) : '';
    $direccion = isset($_POST["direccion"]) ? htmlspecialchars($_POST["direccion"]) : '';
    $telefono = isset($_POST["telefono"]) ? htmlspecialchars($_POST["telefono"]) : '';
    $cedula = isset($_POST["cedula"]) ? htmlspecialchars($_POST["cedula"]) : '';
    $email = isset($_POST["email"]) ? filter_var($_POST["email"], FILTER_SANITIZE_EMAIL) : '';
    $contrasena = isset($_POST["contrasena"]) ? $_POST["contrasena"] : '';
    $tipo_usuario = isset($_POST["tipo_usuario"]) ? (int)$_POST["tipo_usuario"] : 3;

    $rutas = new Rutas();

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

    $data = json_encode($dataArray);

    $ch = curl_init($rutas->getRegisterApiUrl());
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    $res = curl_exec($ch);

    if ($res === false) {
        $error = "Error en la conexión con la API: " . curl_error($ch);
    }

    curl_close($ch);

    $response = json_decode($res, true);

    // Aquí está la modificación:
    if ($response && isset($response["status"]) && $response["status"] === "success") {
        // Redireccionar al formulario de inicio que quieres
        header("Location: inicio.php?tipo=administrador");
        exit;
    } else {
        $error = isset($response) ? json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : "Error en el registro";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Registro</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <h2>Registro</h2>
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
        <input type="hidden" name="tipo_usuario" value="3" />
        <button type="submit">Registrarse</button>
    </form>
    <?php if (isset($error)): ?>
        <pre class="error" style="color:red; background:#fdd; padding:1em;"><?= $error ?></pre>
    <?php endif; ?>
</body>

</html>