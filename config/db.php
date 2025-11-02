<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_cocina2');//nombre de la base de datos
define('DB_USER', 'root');//usuario de la base de datos
define('DB_PASS', '');//contraseña de la base de datos
define('DB_CHARSET', 'utf8mb4');//conjunto de caracteres, versión utf8mb4 para soporte completo de Unicode

// Función para obtener conexión PDO
function getConnection() {
    // Crear conexión PDO
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        //establecer la conexión 
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        //retornar la conexión
    } catch (PDOException $e) {
        http_response_code(500);
        // Manejo de error de conexión
        echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
        exit;
    }
}
?>