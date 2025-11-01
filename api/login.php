<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/Auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos']);
    exit;
}

try {
    $pdo = getConnection();
    
    // SOLUCIÓN: Usar UNION en lugar de OR para evitar el problema del parámetro duplicado
    $stmt = $pdo->prepare("
        SELECT * FROM usuarios 
        WHERE nombre_usuario = :username AND activo = 1
        UNION
        SELECT * FROM usuarios 
        WHERE email = :email AND activo = 1
        LIMIT 1
    ");
    
    $stmt->execute([
        ':username' => $username,
        ':email' => $username
    ]);
    
    $user = $stmt->fetch();
    
    if (!$user) {
        // Esperar un poco para prevenir ataques de fuerza bruta
        sleep(1);
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        exit;
    }
    
    // Verificar contraseña
    if (!password_verify($password, $user['password'])) {
        sleep(1);
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        exit;
    }
    
    // Iniciar sesión
    Auth::login($user);
    
    echo json_encode([
        'success' => true,
        'message' => 'Inicio de sesión exitoso',
        'user' => [
            'id' => $user['id'],
            'nombre_usuario' => $user['nombre_usuario'],
            'nombre_completo' => $user['nombre_completo'],
            'rol' => $user['rol'],
            'email' => $user['email']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error en login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor. Por favor, intente más tarde.']);
} catch (Exception $e) {
    error_log("Error en login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>