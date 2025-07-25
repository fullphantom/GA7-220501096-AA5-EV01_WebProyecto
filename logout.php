<?php
session_start(); // Inicia o reanuda la sesión para acceder a variables de sesión

require_once('Rutas.php'); // Importa la clase Rutas, que contiene las URLs de la API

// Verifica si existe un token de sesión (usuario logueado)
if (isset($_SESSION["token"])) {
    $rutas = new Rutas(); // Crea instancia de la clase Rutas para obtener URLs

    // Inicializa una petición cURL para llamar a la API de logout, pasando el token como parámetro en la URL
    $ch = curl_init($rutas->getLogoutApiUrl($_SESSION["token"]));

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Define que la petición HTTP es DELETE (para cerrar sesión en la API)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Espera respuesta como string 

    curl_exec($ch);   // Ejecuta la petición
    curl_close($ch);  // Cierra la sesión cURL
}

// Destruye la sesión localmente, eliminando todas las variables y datos del usuario
session_destroy();

// Redirige al usuario a la página principal o de inicio (index.html)
header("Location: index.html");
exit; // Termina la ejecución del script
?>
