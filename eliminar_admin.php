<?php
/**
 * Script temporal para eliminar usuario administrador
 * IMPORTANTE: Elimina este archivo después de usarlo por seguridad
 */

require_once __DIR__ . '/config/db.php';

// Configuración de seguridad: cambiar a false después de eliminar el admin
define('ALLOW_ADMIN_DELETION', true);

if (!ALLOW_ADMIN_DELETION) {
    die('⛔ Eliminación de admin deshabilitada por seguridad. Cambia ALLOW_ADMIN_DELETION a true para habilitar.');
}

$mensaje = '';
$tipo = '';
$administradores = [];

// Obtener lista de administradores
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, nombre_usuario, email, nombre_completo, fecha_creacion FROM usuarios WHERE rol = 'admin' ORDER BY fecha_creacion DESC");
    $stmt->execute();
    $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mensaje = 'Error al cargar administradores: ' . $e->getMessage();
    $tipo = 'error';
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
    $adminId = (int)$_POST['admin_id'];
    $confirmacion = trim($_POST['confirmacion'] ?? '');
    
    // Validaciones
    if (empty($confirmacion)) {
        $mensaje = 'Debes escribir "ELIMINAR" para confirmar';
        $tipo = 'error';
    } elseif (strtoupper($confirmacion) !== 'ELIMINAR') {
        $mensaje = 'Confirmación incorrecta. Debes escribir exactamente "ELIMINAR"';
        $tipo = 'error';
    } else {
        try {
            $pdo = getConnection();
            
            // Verificar que el usuario existe y es admin
            $stmt = $pdo->prepare("SELECT nombre_usuario, email, rol FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $adminId]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                $mensaje = 'El usuario no existe';
                $tipo = 'error';
            } elseif ($usuario['rol'] !== 'admin') {
                $mensaje = 'El usuario seleccionado no es administrador';
                $tipo = 'error';
            } else {
                // Contar administradores activos
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin' AND activo = 1");
                $stmt->execute();
                $totalAdmins = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                if ($totalAdmins <= 1) {
                    $mensaje = '⚠️ No se puede eliminar el único administrador activo del sistema';
                    $tipo = 'error';
                } else {
                    // Eliminar el administrador
                    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
                    $stmt->execute(['id' => $adminId]);
                    
                    $mensaje = '✅ Administrador eliminado exitosamente!<br><br>';
                    $mensaje .= '<strong>Usuario eliminado:</strong><br>';
                    $mensaje .= 'Usuario: ' . htmlspecialchars($usuario['nombre_usuario']) . '<br>';
                    $mensaje .= 'Email: ' . htmlspecialchars($usuario['email']) . '<br><br>';
                    $mensaje .= '⚠️ Esta acción no se puede deshacer.';
                    $tipo = 'success';
                    
                    // Recargar lista de administradores
                    $stmt = $pdo->prepare("SELECT id, nombre_usuario, email, nombre_completo, fecha_creacion FROM usuarios WHERE rol = 'admin' ORDER BY fecha_creacion DESC");
                    $stmt->execute();
                    $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipo = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-red-600 to-orange-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-slash text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Eliminar Administrador</h1>
            <p class="text-red-600 text-sm font-semibold">⚠️ Solo para uso gerencial del colegio</p>
        </div>

        <?php if ($mensaje): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $tipo === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($administradores)): ?>
        <div class="text-center py-8">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-600 text-lg">No hay administradores en el sistema</p>
            <a href="crear_admin.php" 
                class="inline-block mt-4 bg-gradient-to-r from-indigo-600 to-purple-700 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-plus mr-2"></i>Crear Administrador
            </a>
        </div>
        <?php else: ?>
        
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-users-cog mr-2"></i>Administradores Actuales (<?php echo count($administradores); ?>)
            </h2>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php foreach ($administradores as $admin): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-user-shield text-indigo-600 mr-2"></i>
                                <?php echo htmlspecialchars($admin['nombre_completo']); ?>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-at text-gray-400 mr-1"></i>
                                <?php echo htmlspecialchars($admin['nombre_usuario']); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                <?php echo htmlspecialchars($admin['email']); ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                Creado: <?php echo date('d/m/Y H:i', strtotime($admin['fecha_creacion'])); ?>
                            </p>
                        </div>
                        <button onclick="mostrarConfirmacion(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['nombre_usuario'], ENT_QUOTES); ?>')"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <div id="modalConfirmacion" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="text-center mb-4">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">¿Estás seguro?</h3>
                    <p class="text-gray-600">
                        Vas a eliminar al administrador:<br>
                        <strong id="nombreUsuarioEliminar" class="text-red-600"></strong>
                    </p>
                </div>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="admin_id" id="adminIdEliminar">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Escribe <strong class="text-red-600">ELIMINAR</strong> para confirmar
                        </label>
                        <input type="text" name="confirmacion" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="ELIMINAR"
                            autocomplete="off">
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="cerrarModal()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold transition">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-600 to-orange-700 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                            <i class="fas fa-trash-alt mr-2"></i>Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Advertencia de Seguridad:</strong><br>
                Este archivo permite eliminar administradores sin autenticación. 
                La eliminación es permanente y no se puede deshacer.
                
        </div>

        <div class="mt-4 text-center">
            <a href="login.php" 
                class="inline-block bg-gradient-to-r from-indigo-600 to-purple-700 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-sign-in-alt mr-2"></i>ir a iniciar sesión
            </a>
        </div>
        <div class="mt-4 text-center">
            <a href="crear_admin.php" 
                class="inline-block bg-gradient-to-r from-indigo-600 to-purple-700 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Ir a Crear Admin
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function mostrarConfirmacion(adminId, nombreUsuario) {
            document.getElementById('adminIdEliminar').value = adminId;
            document.getElementById('nombreUsuarioEliminar').textContent = nombreUsuario;
            document.getElementById('modalConfirmacion').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modalConfirmacion').classList.add('hidden');
            document.querySelector('input[name="confirmacion"]').value = '';
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalConfirmacion').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    </script>
</body>
</html>