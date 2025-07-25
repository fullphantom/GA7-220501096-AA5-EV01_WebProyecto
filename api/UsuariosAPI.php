<?php

// Configura los encabezados HTTP para manejar respuestas JSON y CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite solicitudes desde cualquier origen
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Encabezados permitidos

// Si el método es OPTIONS (pre-flight CORS), simplemente salir sin hacer nada más
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'conexion.php'; // Incluye la conexión a la base de datos mediante PDO

// Obtiene la acción a realizar desde el parámetro GET 'action' (register, login, list)
$action = $_GET['action'] ?? '';

if ($action === 'register') {
    // Acción para registrar un nuevo usuario
    
    // Leer JSON enviado por POST y decodificarlo a arreglo asociativo
    $input = json_decode(file_get_contents('php://input'), true);

    // Campos obligatorios para registro
    $required = ['nombre_usuario', 'apellido', 'direccion', 'telefono', 'cedula', 'email', 'contrasena', 'tipo_usuario'];
    
    // Validar que todos los campos obligatorios estén presentes y no vacíos
    foreach ($required as $field) {
        if (empty($input[$field])) {
            echo json_encode(['status' => 'error', 'message' => "Falta campo: $field"]);
            exit; // Termina ejecución si falta algún campo
        }
    }

    // Sanitizar y asignar variables para evitar inyección o problemas de seguridad
    $nombre = htmlspecialchars(trim($input['nombre_usuario']));
    $apellido = htmlspecialchars(trim($input['apellido']));
    $direccion = htmlspecialchars(trim($input['direccion']));
    $telefono = htmlspecialchars(trim($input['telefono']));
    $cedula = htmlspecialchars(trim($input['cedula']));
    $email = filter_var($input['email'], FILTER_VALIDATE_EMAIL); // Validar email
    $password = $input['contrasena']; // Contraseña en texto plano (será hasheada luego)
    $tipo_usuario = (int)$input['tipo_usuario'];

    // Validar que el email tenga formato correcto
    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Email no válido']);
        exit;
    }

    // Verificar que no exista ya un usuario registrado con ese email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El email ya está registrado']);
        exit;
    }

    // Hashear la contraseña para almacenarla de forma segura en la base de datos
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Preparar consulta para insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuario (Id_tipo_usuario, Nombre, Apellido, Direccion, Telefono, Cedula, Email, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        // Ejecutar la inserción con los datos proporcionados
        $stmt->execute([$tipo_usuario, $nombre, $apellido, $direccion, $telefono, $cedula, $email, $password_hash]);
        
        // Responder con éxito si se inserta correctamente
        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
    } catch (PDOException $e) {
        // En caso de error en la BD, devolver mensaje con el error
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }

} elseif ($action === 'login') {
    // Acción para iniciar sesión
    
    // Leer JSON enviado por POST y decodificarlo
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar que email y contraseña no estén vacíos
    if (empty($input['email']) || empty($input['contrasena'])) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan email o contraseña']);
        exit;
    }

    // Validar formato de email
    $email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
    $password = $input['contrasena'];

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Email no válido']);
        exit;
    }

    // Buscar usuario en la BD por email
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar que exista el usuario y que la contraseña coincida con el hash guardado
    if ($user && password_verify($password, $user['Password'])) {
        // Login exitoso, devolver información básica del usuario
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
        // Usuario no encontrado o contraseña incorrecta
        echo json_encode(['status' => 'error', 'message' => 'Email o contraseña incorrectos']);
    }

} elseif ($action === 'list') {
    // Acción para listar todos los usuarios
    
    try {
        // Consulta simple para obtener datos relevantes de todos los usuarios
        $stmt = $pdo->query("SELECT Id_usuario, Nombre, Apellido, Direccion, Telefono, Cedula, Email, Id_tipo_usuario FROM usuario");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Responder con éxito y la lista de usuarios
        echo json_encode(['status' => 'success', 'message' => $usuarios]);
    } catch (PDOException $e) {
        // Captura error en consulta y devuelve mensaje
        echo json_encode(['status' => 'error', 'message' => 'Error al obtener usuarios: ' . $e->getMessage()]);
    }
} else {
    // Si la acción no coincide con ninguna esperada, devolver error
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
?>
