<?php
// Configurar sesión segura
if (session_status() === PHP_SESSION_NONE) {
    // Solo accesible vía HTTP (no JavaScript)
    ini_set('session.cookie_httponly', 1);
    // Solo usar cookies para sesiones (no URL)
    ini_set('session.use_only_cookies', 1);
    // Solo enviar cookie por HTTPS (cambiar a 1 si usas HTTPS)
    ini_set('session.cookie_secure', 0);
    // Iniciar la sesión
    session_start();
}

// Clase para gestionar autenticación de usuarios
class Auth {
    
    // Verificar si el usuario está autenticado
    public static function check() {
        // Retorna true si existen los datos básicos en la sesión
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['user_rol']) && 
               isset($_SESSION['user_name']);
    }
    
    // Verificar si el usuario es administrador
    public static function isAdmin() {
        // Retorna true si está autenticado y el rol es 'admin'
        return self::check() && $_SESSION['user_rol'] === 'admin';
    }
    
    // Obtener el ID del usuario actual
    public static function getUserId() {
        // Retorna el ID de usuario o null si no existe
        return $_SESSION['user_id'] ?? null;
    }
    
    // Obtener el nombre del usuario actual
    public static function getUserName() {
        // Retorna el nombre de usuario o null si no existe
        return $_SESSION['user_name'] ?? null;
    }
    
    // Obtener el nombre completo del usuario actual
    public static function getUserFullName() {
        // Retorna el nombre completo o null si no existe
        return $_SESSION['user_fullname'] ?? null;
    }
    
    // Obtener el rol del usuario actual
    public static function getUserRole() {
        // Retorna el rol o null si no existe
        return $_SESSION['user_rol'] ?? null;
    }
    
    // Obtener el email del usuario actual
    public static function getUserEmail() {
        // Retorna el email o null si no existe
        return $_SESSION['user_email'] ?? null;
    }
    
    // Iniciar sesión
    public static function login($user) {
        // Regenerar ID de sesión para prevenir fijación de sesión
        session_regenerate_id(true);
        
        // Guardar datos del usuario en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre_usuario'];
        $_SESSION['user_fullname'] = $user['nombre_completo'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['login_time'] = time(); // Guardar hora de login
        $_SESSION['last_activity'] = time(); // Guardar última actividad
        
        // Guardar IP para seguridad adicional (opcional)
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    // Cerrar sesión
    public static function logout() {
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión si existe
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir la sesión
        session_destroy();
    }
    
    // Requerir autenticación (para páginas PHP)
    public static function requireAuth() {
        // Si no está autenticado
        if (!self::check()) {
            // Si es una petición AJAX/API, devolver JSON
            if (self::isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'message' => 'No autenticado',
                    'redirect' => 'login.php'
                ]);
                exit;
            }
            
            // Si es una petición normal, redirigir al login
            header('Location: ' . self::getBaseUrl() . 'login.php');
            exit;
        }
        
        // Verificar tiempo de inactividad (opcional: 30 minutos)
        $inactivity_limit = 1800; // 30 minutos
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $inactivity_limit) {
            // Si la sesión expiró, cerrar sesión
            self::logout();
            
            // Si es AJAX, devolver JSON
            if (self::isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Sesión expirada por inactividad',
                    'redirect' => 'login.php'
                ]);
                exit;
            }
            
            // Si es normal, redirigir al login con parámetro de expiración
            header('Location: ' . self::getBaseUrl() . 'login.php?expired=1');
            exit;
        }
        
        // Actualizar tiempo de última actividad
        $_SESSION['last_activity'] = time();
    }
    
    // Requerir rol de administrador
    public static function requireAdmin() {
        // Requiere autenticación primero
        self::requireAuth();
        
        // Si no es admin
        if (!self::isAdmin()) {
            // Si es AJAX, devolver JSON
            if (self::isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Acceso denegado. Se requieren permisos de administrador.'
                ]);
                exit;
            }
            
            // Si es normal, redirigir al inicio
            header('Location: ' . self::getBaseUrl() . 'index.php');
            exit;
        }
    }
    
    // Verificar si es una petición AJAX
    private static function isAjaxRequest() {
        // Retorna true si la cabecera HTTP indica AJAX
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    // Obtener la URL base del proyecto
    private static function getBaseUrl() {
        // Detecta protocolo (http/https)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        // Obtiene el host (dominio)
        $host = $_SERVER['HTTP_HOST'];
        // Obtiene el script actual
        $script = $_SERVER['SCRIPT_NAME'];
        // Obtiene el path base del proyecto
        $path = str_replace(basename($script), '', $script);
        // Retorna la URL base
        return $protocol . '://' . $host . $path;
    }
    
    // Obtener información completa del usuario actual
    public static function getUserInfo() {
        // Si no está autenticado, retorna null
        if (!self::check()) {
            return null;
        }
        
        // Retorna array con toda la información relevante del usuario
        return [
            'id' => self::getUserId(),
            'nombre_usuario' => self::getUserName(),
            'nombre_completo' => self::getUserFullName(),
            'rol' => self::getUserRole(),
            'email' => self::getUserEmail(),
            'is_admin' => self::isAdmin()
        ];
    }
}
?>