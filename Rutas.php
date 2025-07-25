<?php
class Rutas {
    protected $urlBase;

    // Cambia la URL base según la ruta de tu proyecto
    public function __construct(string $urlBase = 'http://localhost/evidencia') {
        $this->urlBase = rtrim($urlBase, '/');
    }

    // Retorna la URL base del proyecto
    public function getUrlBase(): string {
        return $this->urlBase;
    }

    // Retorna la URL del frontend (ajusta si tienes carpeta frontend diferente)
    public function getUrlFront(): string {
        return $this->urlBase;
    }

    // Retorna la URL base de la API
    public function getUrlApi(): string {
        return $this->urlBase . '/api';
    }

    // URL para el endpoint de registro
    public function getRegisterApiUrl(): string {
        return $this->getUrlApi() . '/UsuariosAPI.php?action=register';
    }

    // URL para el endpoint de login
    public function getLoginApiUrl(): string {
        return $this->getUrlApi() . '/UsuariosAPI.php?action=login';
    }

    // URL para logout, pasando el token de sesión
    public function getLogoutApiUrl(string $token): string {
        return $this->getUrlApi() . '/UsuariosAPI.php?action=logout&token=' . urlencode($token);
    }
}
?>
