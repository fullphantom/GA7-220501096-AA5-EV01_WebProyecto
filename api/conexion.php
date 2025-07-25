<?php
// conexion base de datos restaurante
$host = "localhost";
$dbname = "restaurante";
$username = "root"; 
$password = "";    

try {
    // Crear una nueva conexión PDO a la base de datos usando los parámetros definidos
    // Se establece el charset utf8 para evitar problemas de lenguaje y codificación
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configura PDO para que lance excepciones en caso de error 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En caso de error de conexión, termina el script y envía un JSON con el mensaje de error
    die(json_encode([
        'status' => 'error', 
        'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
    ]));
}

