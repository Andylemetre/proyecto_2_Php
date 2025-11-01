<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    // Obtener datos JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Verificar si hay datos
    if (!$data) {
        throw new Exception('No se recibieron datos válidos');
    }
    
    // Validar datos requeridos
    if (empty($data['nombre_completo']) || empty($data['nombre_usuario']) || 
        empty($data['email']) || empty($data['password'])) {
        throw new Exception('Todos los campos son requeridos');
    }
    
    $nombreCompleto = trim($data['nombre_completo']);
    $nombreUsuario = trim($data['nombre_usuario']);
    $email = trim($data['email']);
    $password = $data['password'];
    
    // Validaciones
    if (strlen($nombreCompleto) < 3) {
        throw new Exception('El nombre completo debe tener al menos 3 caracteres');
    }
    
    if (strlen($nombreUsuario) < 3) {
        throw new Exception('El nombre de usuario debe tener al menos 3 caracteres');
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nombreUsuario)) {
        throw new Exception('El nombre de usuario solo puede contener letras, números y guión bajo');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email es inválido');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Conectar a la base de datos
    $pdo = getConnection();
    
    // Verificar si el nombre de usuario ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :nombre_usuario");
    $stmt->execute(['nombre_usuario' => $nombreUsuario]);
    
    if ($stmt->fetch()) {
        throw new Exception('El nombre de usuario ya está registrado');
    }
    
    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        throw new Exception('El email ya está registrado');
    }
    
    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nombre_usuario, nombre_completo, email, password, rol, activo) 
        VALUES (:nombre_usuario, :nombre_completo, :email, :password, 'usuario', 1)
    ");
    
    $resultado = $stmt->execute([
        'nombre_usuario' => $nombreUsuario,
        'nombre_completo' => $nombreCompleto,
        'email' => $email,
        'password' => $passwordHash
    ]);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => '¡Registro exitoso! Redirigiendo al login...'
        ]);
    } else {
        throw new Exception('Error al registrar el usuario');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    
    // Manejar error de clave duplicada
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'message' => 'El nombre de usuario o email ya están registrados'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>