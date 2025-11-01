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
            // Obtener historial de movimientos con información del usuario
            $categoria = $_GET['categoria'] ?? null;
            
            $sql = "
                SELECT 
                    m.*,
                    u.nombre_usuario,
                    u.nombre_completo
                FROM movimientos m
                LEFT JOIN usuarios u ON m.usuario_id = u.id
            ";
            
            if ($categoria && $categoria !== 'todos') {
                $tipo = $categoria === 'insumos' ? 'insumo' : 'herramienta';
                $sql .= " WHERE m.categoria = :categoria";
            }
            
            $sql .= " ORDER BY m.fecha_movimiento DESC LIMIT 100";
            
            $stmt = $pdo->prepare($sql);
            
            if ($categoria && $categoria !== 'todos') {
                $stmt->execute(['categoria' => $tipo]);
            } else {
                $stmt->execute();
            }
            
            $movimientos = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $movimientos]);
            break;
            
        case 'POST':
            // Registrar nuevo movimiento (todos los usuarios autenticados)
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Iniciar transacción
            $pdo->beginTransaction();
            
            try {
                // Registrar el movimiento con el usuario que lo realizó
                $stmt = $pdo->prepare("
                    INSERT INTO movimientos 
                    (elemento, tipo_movimiento, categoria, cantidad, unidad, motivo, usuario_id) 
                    VALUES (:elemento, :tipo, :categoria, :cantidad, :unidad, :motivo, :usuario_id)
                ");
                
                $stmt->execute([
                    'elemento' => $data['elemento'],
                    'tipo' => $data['tipo_movimiento'],
                    'categoria' => $data['categoria'],
                    'cantidad' => $data['cantidad'],
                    'unidad' => $data['unidad'],
                    'motivo' => $data['motivo'],
                    'usuario_id' => Auth::getUserId()
                ]);
                
                // Actualizar cantidad en la tabla correspondiente
                $tabla = $data['categoria'] === 'insumo' ? 'insumos' : 'herramientas';
                $operador = $data['tipo_movimiento'] === 'entrada' ? '+' : '-';
                
                $stmt = $pdo->prepare("
                    UPDATE $tabla 
                    SET cantidad = cantidad $operador :cantidad 
                    WHERE nombre = :nombre
                ");
                
                $stmt->execute([
                    'cantidad' => $data['cantidad'],
                    'nombre' => $data['elemento']
                ]);
                
                // Verificar que no haya cantidad negativa
                if ($data['tipo_movimiento'] === 'salida') {
                    $stmt = $pdo->prepare("SELECT cantidad FROM $tabla WHERE nombre = :nombre");
                    $stmt->execute(['nombre' => $data['elemento']]);
                    $result = $stmt->fetch();
                    
                    if ($result && $result['cantidad'] < 0) {
                        throw new Exception('No hay suficiente stock para realizar esta salida');
                    }
                }
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Movimiento registrado exitosamente'
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
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
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>