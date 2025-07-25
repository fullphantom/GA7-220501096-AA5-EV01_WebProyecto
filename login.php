<?php
session_start();
require_once('Rutas.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rutas = new Rutas();

    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST["contrasena"] ?? '';
    $tipo_usuario = $_POST["tipo_usuario"] ?? ''; // para mantener el tipo (cliente o admin)

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email no válido.";
        header("Location: inicio.php?tipo=" . ($tipo_usuario == 1 ? "administrador" : "cliente"));
        exit;
    }

    $data = json_encode([
        "email" => $email,
        "contrasena" => $contrasena
    ]);

    $ch = curl_init($rutas->getLoginApiUrl());
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    $res = curl_exec($ch);
    curl_close($ch);

    if ($res === false) {
        $_SESSION['error'] = "Error de conexión con la API.";
        header("Location: inicio.php?tipo=" . ($tipo_usuario == 1 ? "administrador" : "cliente"));
        exit;
    }

    $response = json_decode($res, true);

    if ($response && isset($response['status']) && $response['status'] === 'success') {
        $_SESSION["usuario"] = $response['user']['email'];
        $_SESSION["nombre"] = $response['user']['nombre'];
        $_SESSION["apellido"] = $response['user']['apellido'];
        $_SESSION["tipo_usuario"] = $response['user']['tipo_usuario'];

        if ($_SESSION["tipo_usuario"] == 1) {
            header("Location: datos_clientes.php");
        } else {
            header("Location: cliente_dashboard.php");
        }
        exit;
    } else {
        $_SESSION['error'] = $response['message'] ?? "Credenciales incorrectas.";
        header("Location: inicio.php?tipo=" . ($tipo_usuario == 1 ? "administrador" : "cliente"));
        exit;
    }
}
