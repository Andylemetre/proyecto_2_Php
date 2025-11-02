<?php
/**
 * Panel de administración de usuarios
 * Solo accesible para administradores autenticados
 */

session_start();
require_once __DIR__ . '/config/db.php';

// Verificar que el usuario esté autenticado y sea administrador

$mensaje = '';
$tipo = '';
$usuarios = [];

// Obtener lista de usuarios normales
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT id, nombre_usuario, email, nombre_completo, activo, fecha_creacion 
        FROM usuarios 
        WHERE rol = 'usuario' 
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mensaje = 'Error al cargar usuarios: ' . $e->getMessage();
    $tipo = 'error';
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_id'])) {
    $usuarioId = (int)$_POST['usuario_id'];
    $confirmacion = trim($_POST['confirmacion'] ?? '');
    
    if (empty($confirmacion)) {
        $mensaje = 'Debes escribir "ELIMINAR" para confirmar';
        $tipo = 'error';
    } elseif (strtoupper($confirmacion) !== 'ELIMINAR') {
        $mensaje = 'Confirmación incorrecta. Debes escribir exactamente "ELIMINAR"';
        $tipo = 'error';
    } else {
        try {
            $pdo = getConnection();
            
            // Verificar que el usuario existe y no es admin
            $stmt = $pdo->prepare("SELECT nombre_usuario, email, rol FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $usuarioId]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                $mensaje = 'El usuario no existe';
                $tipo = 'error';
            } elseif ($usuario['rol'] === 'admin') {
                $mensaje = '⚠️ No puedes eliminar administradores desde este panel';
                $tipo = 'error';
            } else {
                // Eliminar el usuario
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmt->execute(['id' => $usuarioId]);
                
                $mensaje = '✅ Usuario eliminado exitosamente!<br><br>';
                $mensaje .= '<strong>Usuario eliminado:</strong><br>';
                $mensaje .= 'Usuario: ' . htmlspecialchars($usuario['nombre_usuario']) . '<br>';
                $mensaje .= 'Email: ' . htmlspecialchars($usuario['email']);
                $tipo = 'success';
                
                // Recargar lista de usuarios
                $stmt = $pdo->prepare("
                    SELECT id, nombre_usuario, email, nombre_completo, activo, fecha_creacion 
                    FROM usuarios 
                    WHERE rol = 'usuario' 
                    ORDER BY fecha_creacion DESC
                ");
                $stmt->execute();
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
        } catch (Exception $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipo = 'error';
        }
    }
}

// Procesar cambio de estado (activar/desactivar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_activo'])) {
    $usuarioId = (int)$_POST['toggle_activo'];
    
    try {
        $pdo = getConnection();
        
        // Verificar que el usuario existe y no es admin
        $stmt = $pdo->prepare("SELECT activo, rol FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $usuarioId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && $usuario['rol'] !== 'admin') {
            $nuevoEstado = $usuario['activo'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE usuarios SET activo = :activo WHERE id = :id");
            $stmt->execute(['activo' => $nuevoEstado, 'id' => $usuarioId]);
            
            $mensaje = $nuevoEstado ? '✅ Usuario activado correctamente' : '⚠️ Usuario desactivado correctamente';
            $tipo = 'success';
            
            // Recargar lista
            $stmt = $pdo->prepare("
                SELECT id, nombre_usuario, email, nombre_completo, activo, fecha_creacion 
                FROM usuarios 
                WHERE rol = 'usuario' 
                ORDER BY fecha_creacion DESC
            ");
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipo = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
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
<body class="min-h-screen p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <div class="bg-gradient-to-r from-red-600 to-orange-700 w-12 h-12 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-users-cog text-white"></i>
                        </div>
                        Administración de Usuarios
                    </h1>
                    <p class="text-gray-600 mt-2 ml-15">
                        <i class="fas fa-user-shield text-indigo-600 mr-2"></i>
                        Bienvenido, Administrador
                    </p>
                </div>
                <div class="flex gap-3 mt-4 md:mt-0">

                    <a href="index.php" 
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Salir
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if ($mensaje): ?>
        <div class="bg-white rounded-2xl shadow-2xl p-4 mb-6">
            <div class="<?php echo $tipo === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?> p-4 rounded-lg">
                <?php echo $mensaje; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Panel principal -->
        <div class="bg-white rounded-2xl shadow-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-users text-indigo-600 mr-2"></i>
                    Usuarios Registrados (<?php echo count($usuarios); ?>)
                </h2>
                
                <!-- Filtros/Búsqueda -->
                <div class="flex items-center gap-2">
                    <input type="text" id="buscarUsuario" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Buscar usuario...">
                    <button onclick="buscar()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <?php if (empty($usuarios)): ?>
            <div class="text-center py-12">
                <i class="fas fa-user-slash text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg">No hay usuarios registrados</p>
            </div>
            <?php else: ?>
            
            <!-- Tabla de usuarios -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left rounded-tl-lg">Usuario</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Nombre Completo</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Registro</th>
                            <th class="px-4 py-3 text-center rounded-tr-lg">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios">
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition usuario-row">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="bg-indigo-100 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                    <span class="font-semibold text-gray-800 usuario-nombre">
                                        <?php echo htmlspecialchars($usuario['nombre_usuario']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600 usuario-email">
                                <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                <?php echo htmlspecialchars($usuario['email']); ?>
                            </td>
                            <td class="px-4 py-4 text-gray-600 usuario-fullname">
                                <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="toggle_activo" value="<?php echo $usuario['id']; ?>">
                                    <button type="submit" class="<?php echo $usuario['activo'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> px-3 py-1 rounded-full text-sm font-semibold hover:shadow-md transition">
                                        <?php echo $usuario['activo'] ? '✓ Activo' : '✗ Inactivo'; ?>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-4 text-center text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button onclick="mostrarConfirmacion(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre_usuario'], ENT_QUOTES); ?>')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-semibold transition">
                                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Usuarios</p>
                            <p class="text-3xl font-bold"><?php echo count($usuarios); ?></p>
                        </div>
                        <i class="fas fa-users text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Usuarios Activos</p>
                            <p class="text-3xl font-bold">
                                <?php echo count(array_filter($usuarios, function($u) { return $u['activo']; })); ?>
                            </p>
                        </div>
                        <i class="fas fa-user-check text-4xl opacity-50"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Usuarios Inactivos</p>
                            <p class="text-3xl font-bold">
                                <?php echo count(array_filter($usuarios, function($u) { return !$u['activo']; })); ?>
                            </p>
                        </div>
                        <i class="fas fa-user-times text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>
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
                    Vas a eliminar al usuario:<br>
                    <strong id="nombreUsuarioEliminar" class="text-red-600"></strong>
                </p>
            </div>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="usuario_id" id="usuarioIdEliminar">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Escribe <strong class="text-red-600">ELIMINAR</strong> para confirmar
                    </label>
                    <input type="text" name="confirmacion" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="ELIMINAR"
                        autocomplete="off">
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Esta acción eliminará permanentemente al usuario y no se puede deshacer.
                    </p>
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

    <script>
        function mostrarConfirmacion(usuarioId, nombreUsuario) {
            document.getElementById('usuarioIdEliminar').value = usuarioId;
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

        // Función de búsqueda en tiempo real
        document.getElementById('buscarUsuario').addEventListener('input', function(e) {
            buscar();
        });

        function buscar() {
            const termino = document.getElementById('buscarUsuario').value.toLowerCase();
            const filas = document.querySelectorAll('.usuario-row');
            
            filas.forEach(fila => {
                const nombre = fila.querySelector('.usuario-nombre').textContent.toLowerCase();
                const email = fila.querySelector('.usuario-email').textContent.toLowerCase();
                const fullname = fila.querySelector('.usuario-fullname').textContent.toLowerCase();
                
                if (nombre.includes(termino) || email.includes(termino) || fullname.includes(termino)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>