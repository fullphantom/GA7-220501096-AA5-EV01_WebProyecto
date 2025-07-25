<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'conexion.php'; // Conexión a la base de datos

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    $input = json_decode(file_get_contents('php://input'), true);

    $required = ['nombre_usuario', 'apellido', 'direccion', 'telefono', 'cedula', 'email', 'contrasena', 'tipo_usuario'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            echo json_encode(['status' => 'error', 'message' => "Falta campo: $field"]);
            exit;
        }
    }

    // Sanear y asignar
    $nombre = htmlspecialchars(trim($input['nombre_usuario']));
    $apellido = htmlspecialchars(trim($input['apellido']));
    $direccion = htmlspecialchars(trim($input['direccion']));
    $telefono = htmlspecialchars(trim($input['telefono']));
    $cedula = htmlspecialchars(trim($input['cedula']));
    $email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    $password = $input['contrasena'];
    $tipo_usuario = (int)$input['tipo_usuario'];

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Email no válido']);
        exit;
    }

    // Verificar si email ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El email ya está registrado']);
        exit;
    }

    // Hashear contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario
    $sql = "INSERT INTO usuario (Id_tipo_usuario, Nombre, Apellido, Direccion, Telefono, Cedula, Email, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$tipo_usuario, $nombre, $apellido, $direccion, $telefono, $cedula, $email, $password_hash]);
        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }

} elseif ($action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['email']) || empty($input['contrasena'])) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan email o contraseña']);
        exit;
    }

    $email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    $password = $input['contrasena'];

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Email no válido']);
        exit;
    }

    // Buscar usuario
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        // Login exitoso - Puedes generar token o iniciar sesión aquí
        echo json_encode([
            'status' => 'success',
            'message' => 'Login correcto',
            'user' => [
                'id' => $user['Id_usuario'],
                'nombre' => $user['Nombre'],
                'apellido' => $user['Apellido'],
                'tipo_usuario' => $user['Id_tipo_usuario'],
                'email' => $user['Email']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email o contraseña incorrectos']);
    }

} elseif ($action === 'list') {
    // Obtener todos los usuarios
    try {
        $stmt = $pdo->query("SELECT Id_usuario, Nombre, Apellido, Direccion, Telefono, Cedula, Email, Id_tipo_usuario FROM usuario");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'message' => $usuarios]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al obtener usuarios: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
?>
