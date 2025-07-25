<?php 
session_start(); // Inicia la sesión o la continúa para poder acceder a variables de sesión
require_once('Rutas.php'); // Incluye la clase Rutas para obtener URLs de la API

// Verifica que el usuario esté autenticado y sea administrador (tipo_usuario  1)
// Si no, redirige a la página de login para evitar acceso no autorizado
if (!isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: login.php");
    exit;
}

$rutas = new Rutas(); // Instancia la clase Rutas para usar sus métodos

// Inicializa cURL para hacer una petición GET a la API que devuelve la lista de usuarios
$ch = curl_init($rutas->getUrlApi() . '/UsuariosAPI.php?action=list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Configura para recibir la respuesta como string
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json" // Indica que espera JSON 
]);
$res = curl_exec($ch); // Ejecuta la petición a la API
curl_close($ch); // Cierra la sesión cURL

// Decodifica la respuesta JSON en un array asociativo de PHP
$usuarios = json_decode($res, true);

// Verifica si la respuesta fue correcta y si contiene el listado de usuarios
if (!$usuarios || !isset($usuarios['status']) || $usuarios['status'] !== 'success') {
    // Si hubo error, prepara mensaje y deja lista vacía para no romper el HTML
    $error = "No se pudieron obtener los usuarios: " . ($usuarios['message'] ?? 'Error desconocido');
    $usuarios_list = [];
} else {
    // Si todo bien, asigna el listado que viene en 'message'
    $usuarios_list = $usuarios['message'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Listado de Usuarios</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
    <!-- Mensaje de usuario autenticado -->
    <h1 class="titulo mensaje-exito">Usuario Autenticado !!!!!!!!!!</h1>

    <!-- Botón para cerrar sesión -->
    <div class="header-right">
        <form method="POST" action="logout.php">
            <button type="submit" class="btn-cerrar-sesion">Cerrar sesión</button>
        </form>
    </div>
</header>

<main>
    <!-- Si hay un error, se muestra -->
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <h2 class="titulo-tabla">Listado de Usuarios</h2>

    <!-- Si hay usuarios, mostrar tabla -->
    <?php if (!empty($usuarios_list)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <!-- Encabezados de columnas -->
                    <th>ID</th><th>Nombre</th><th>Apellido</th><th>Dirección</th><th>Teléfono</th><th>Cédula</th><th>Email</th><th>Tipo Usuario</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Recorre la lista de usuarios y muestra cada fila -->
                <?php foreach ($usuarios_list as $usuario): ?>
                    <tr>
                        <!-- Muestra cada dato del usuario con protección contra XSS -->
                        <td><?= htmlspecialchars($usuario['Id_usuario'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Apellido'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Direccion'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Telefono'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Cedula'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Id_tipo_usuario'] ?? '') ?></td>
                        <td>
                            <!-- Enlaces para editar y eliminar usuario, pasando ID por URL -->
                            <a href="editar_cliente.php?id=<?= urlencode($usuario['Id_usuario']) ?>">Editar</a>
                            <a href="eliminar_cliente.php?id=<?= urlencode($usuario['Id_usuario']) ?>" onclick="return confirm('¿Seguro que quieres eliminar este cliente?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Si no hay usuarios, muestra mensaje -->
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</main>

<footer>
    <p>Puerto Broaster Britalia - Todos los Derechos Reservados 2025</p>
</footer>

</body>
</html>
