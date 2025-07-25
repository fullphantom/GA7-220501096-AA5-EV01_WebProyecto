<?php
session_start(); // Inicia la sesión para usar variables de $_SESSION
require_once('Rutas.php'); // Importa la clase Rutas que contiene URLs para las APIs

// Verifica que el formulario se haya enviado mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $rutas = new Rutas(); // Instancia la clase Rutas para obtener la URL del login de la API

    // Sanitiza y recibe los datos del formulario
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL); // Elimina caracteres no válidos en el email
    $contrasena = $_POST["contrasena"] ?? ''; // Recibe la contraseña o una cadena vacía si no se envió
    $tipo_usuario = $_POST["tipo_usuario"] ?? ''; // Redirige al tipo correspondiente si hay error

    // Valida que el email tenga formato correcto
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email no válido."; // Guarda un mensaje de error en sesión
        // Redirige al formulario de inicio según el tipo de usuario (admin o cliente)
        header("Location: inicio.php?tipo=" . ($tipo_usuario == 1 ? "administrador" : "cliente"));
        exit;
    }

    // Prepara los datos para enviar a la API (email y contraseña) en formato JSON
    $data = json_encode([
        "email" => $email,
        "contrasena" => $contrasena
    ]);

    // Configura la llamada cURL a la API de login
    $ch = curl_init($rutas->getLoginApiUrl()); // Inicializa cURL con la URL de login desde Rutas
    curl_setopt($ch, CURLOPT_POST, 1); // Establece el método como POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Envía los datos como cuerpo del POST
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Espera una respuesta como string
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]); // Define que se envía JSON
    $res = curl_exec($ch); // Ejecuta la petición a la API
    curl_close($ch); // Cierra la sesión cURL

    // Si hubo un error de conexión o no se recibió respuesta
    if ($res === false) {
        $_SESSION['error'] = "Error de conexión con la API.";
        header("Location: inicio.php?tipo=" . ($tipo_usuario == 1 ? "administrador" : "cliente"));
        exit;
    }

    // Decodifica la respuesta JSON de la API en un array asociativo
    $response = json_decode($res, true);

    // Verifica si la respuesta fue exitosa y contiene la información esperada
    if ($response && isset($response['status']) && $response['status'] === 'success') {
        // Guarda los datos del usuario autenticado en la sesión
        $_SESSION["usuario"] = $response['user']['email'];
        $_SESSION["nombre"] = $response['user']['nombre'];
        $_SESSION["apellido"] = $response['user']['apellido'];
        $_SESSION["tipo_usuario"] = $response['user']['tipo_usuario'];

        // Redirige según el tipo de usuario
        if ($_SESSION["tipo_usuario"] == 1) {
            header("Location: datos_clientes.php"); // Admin
        } else {
            header("Location: index.html"); // Cliente
        }
        exit;
    } else {
        // Si las credenciales son incorrectas u ocurre otro error, muestra mensaje de la API o uno genérico
        $_SESSION['error'] = $response['message'] ?? "Datos incorrectos.";
        header("Location: inicio.php?tipo=" . ($tipo_usuario == 1 ? "administrador" : "cliente"));
        exit;
    }
}
