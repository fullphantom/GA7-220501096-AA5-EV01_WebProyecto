<?php
// conexion.php
$host = "localhost";
$dbname = "restaurante";
$username = "root"; // cambia si usas otro usuario
$password = "";     // cambia si usas otra contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Para manejar errores como excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()]));
}
?>
