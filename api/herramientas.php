<?php
// Incluye el archivo de configuración de la base de datos
require_once __DIR__ . '/../config/db.php';
// Incluye el archivo de autenticación
require_once __DIR__ . '/../config/Auth.php';

// Requiere que el usuario esté autenticado antes de continuar
Auth::requireAuth();

// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Obtiene el método HTTP de la solicitud (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Obtiene la conexión PDO a la base de datos
    $pdo = getConnection();
    
    // Maneja la solicitud según el método HTTP
    switch ($method) {
        case 'GET':
            // Obtiene el parámetro 'ubicacion' si existe en la URL
            $ubicacion = $_GET['ubicacion'] ?? null;
            
            // Si se especifica una ubicación y no es 'todas', filtra por ubicación
            if ($ubicacion && $ubicacion !== 'todas') {
                $stmt = $pdo->prepare("SELECT * FROM herramientas WHERE ubicacion = :ubicacion ORDER BY nombre");
                $stmt->execute(['ubicacion' => $ubicacion]);
            } else {
                // Si no, obtiene todas las herramientas
                $stmt = $pdo->query("SELECT * FROM herramientas ORDER BY nombre");
            }
            
            // Obtiene todos los resultados como un array
            $herramientas = $stmt->fetchAll();
            // Devuelve los datos en formato JSON
            echo json_encode(['success' => true, 'data' => $herramientas]);
            break;
            
        case 'POST':
            // Solo los administradores pueden crear herramientas
            Auth::requireAdmin();
            
            // Obtiene los datos enviados en el cuerpo de la solicitud (JSON)
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Prepara la consulta para insertar una nueva herramienta
            $stmt = $pdo->prepare("
                INSERT INTO herramientas (nombre, cantidad, categoria, ubicacion, usuario_creacion) 
                VALUES (:nombre, :cantidad, :categoria, :ubicacion, :usuario_creacion)
            ");
            
            // Ejecuta la consulta con los datos proporcionados
            $stmt->execute([
                'nombre' => $data['nombre'],
                'cantidad' => $data['cantidad'],
                'categoria' => $data['categoria'],
                'ubicacion' => $data['ubicacion'],
                // Obtiene el ID del usuario autenticado
                'usuario_creacion' => Auth::getUserId()
            ]);
            
            // Devuelve una respuesta de éxito con el ID de la nueva herramienta
            echo json_encode([
                'success' => true,
                'message' => 'Herramienta agregada exitosamente',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'PUT':
            // Solo los administradores pueden actualizar herramientas
            Auth::requireAdmin();
            
            // Obtiene los datos enviados en el cuerpo de la solicitud (JSON)
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Prepara la consulta para actualizar una herramienta existente
            $stmt = $pdo->prepare("
                UPDATE herramientas 
                SET cantidad = :cantidad, 
                    categoria = :categoria, 
                    ubicacion = :ubicacion 
                WHERE id = :id
            ");
            
            // Ejecuta la consulta con los datos proporcionados
            $stmt->execute([
                'cantidad' => $data['cantidad'],
                'categoria' => $data['categoria'],
                'ubicacion' => $data['ubicacion'],
                'id' => $data['id']
            ]);
            
            // Devuelve una respuesta de éxito
            echo json_encode([
                'success' => true,
                'message' => 'Herramienta actualizada exitosamente'
            ]);
            break;
            
        case 'DELETE':
            // Solo los administradores pueden eliminar herramientas
            Auth::requireAdmin();
            
            // Obtiene el parámetro 'id' de la URL
            $id = $_GET['id'] ?? null;
            
            // Si no se proporciona un ID, lanza una excepción
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            // Prepara la consulta para eliminar la herramienta por ID
            $stmt = $pdo->prepare("DELETE FROM herramientas WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            // Devuelve una respuesta de éxito
            echo json_encode([
                'success' => true,
                'message' => 'Herramienta eliminada exitosamente'
            ]);
            break;
            
        default:
            // Si el método HTTP no está permitido, responde con error 405
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
    }
    
} catch (PDOException $e) {
    // Si ocurre un error de base de datos, responde con error 500 y el mensaje
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Si ocurre cualquier otra excepción, responde con error 403 y el mensaje
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>