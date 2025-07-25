<?php 
session_start();
require_once('Rutas.php');

// Verificar sesión y tipo usuario administrador (1)
if (!isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: login.php");
    exit;
}

$rutas = new Rutas();

$ch = curl_init($rutas->getUrlApi() . '/UsuariosAPI.php?action=list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
$res = curl_exec($ch);
curl_close($ch);

$usuarios = json_decode($res, true);

// Verificar errores
if (!$usuarios || !isset($usuarios['status']) || $usuarios['status'] !== 'success') {
    $error = "No se pudieron obtener los usuarios: " . ($usuarios['message'] ?? 'Error desconocido');
    $usuarios_list = [];
} else {
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
    <h1 class="titulo mensaje-exito">Usuario Autenticado !!!!!!!!!!</h1>
    <div class="header-right">
        <form method="POST" action="logout.php">
            <button type="submit" class="btn-cerrar-sesion">Cerrar sesión</button>
        </form>
    </div>
</header>

<main>
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <h2 class="titulo-tabla">Listado de Usuarios</h2>

    <?php if (!empty($usuarios_list)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th><th>Nombre</th><th>Apellido</th><th>Dirección</th><th>Teléfono</th><th>Cédula</th><th>Email</th><th>Tipo Usuario</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios_list as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['Id_usuario'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Nombre'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Apellido'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Direccion'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Telefono'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Cedula'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($usuario['Id_tipo_usuario'] ?? '') ?></td>
                        <td>
                            <a href="editar_cliente.php?id=<?= urlencode($usuario['Id_usuario']) ?>">Editar</a>
                            <a href="eliminar_cliente.php?id=<?= urlencode($usuario['Id_usuario']) ?>" onclick="return confirm('¿Seguro que quieres eliminar este cliente?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</main>

<footer>
    <p>Puerto Broaster Britalia - Todos los Derechos Reservados 2025</p>
</footer>

</body>
</html>
