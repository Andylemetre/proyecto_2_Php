<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/Auth.php';

// Requerir autenticación
Auth::requireAuth();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();
    
    switch ($method) {
        case 'GET':
            // Obtener todos los insumos
            $stmt = $pdo->query("SELECT * FROM insumos ORDER BY nombre");
            $insumos = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $insumos]);
            break;
            
        case 'POST':
            // Solo admin puede crear insumos
            Auth::requireAdmin();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("
                INSERT INTO insumos (nombre, cantidad, unidad, stock_minimo, usuario_creacion) 
                VALUES (:nombre, :cantidad, :unidad, :stock_minimo, :usuario_creacion)
            ");
            
            $stmt->execute([
                'nombre' => $data['nombre'],
                'cantidad' => $data['cantidad'],
                'unidad' => $data['unidad'],
                'stock_minimo' => $data['stock_minimo'],
                'usuario_creacion' => Auth::getUserId()
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Insumo agregado exitosamente',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'PUT':
            // Solo admin puede actualizar insumos
            Auth::requireAdmin();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("
                UPDATE insumos 
                SET cantidad = :cantidad, 
                    unidad = :unidad, 
                    stock_minimo = :stock_minimo 
                WHERE id = :id
            ");
            
            $stmt->execute([
                'cantidad' => $data['cantidad'],
                'unidad' => $data['unidad'],
                'stock_minimo' => $data['stock_minimo'],
                'id' => $data['id']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Insumo actualizado exitosamente'
            ]);
            break;
            
        case 'DELETE':
            // Solo admin puede eliminar insumos
            Auth::requireAdmin();
            
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }
            
            $stmt = $pdo->prepare("DELETE FROM insumos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Insumo eliminado exitosamente'
            ]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>