<?php
/**
 * Script temporal para crear usuario administrador
 * IMPORTANTE: Elimina este archivo después de usarlo por seguridad
 */

require_once __DIR__ .'/../config/db.php';

// Configuración de seguridad: cambiar a false después de crear el admin
define('ALLOW_ADMIN_CREATION', true);

if (!ALLOW_ADMIN_CREATION) {
    die('⛔ Creación de admin deshabilitada por seguridad. Cambia ALLOW_ADMIN_CREATION a true para habilitar.');
}

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
    $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones
    if (empty($nombreCompleto) || empty($nombreUsuario) || empty($email) || empty($password)) {
        $mensaje = 'Todos los campos son requeridos';
        $tipo = 'error';
    } elseif (strlen($password) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres';
        $tipo = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El email no es válido';
        $tipo = 'error';
    } else {
        try {
            $pdo = getConnection();
            
            // Verificar si el usuario ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :username OR email = :email");
            $stmt->execute(['username' => $nombreUsuario, 'email' => $email]);
            
            if ($stmt->fetch()) {
                $mensaje = 'El nombre de usuario o email ya existe';
                $tipo = 'error';
            } else {
                // Crear el usuario administrador
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nombre_usuario, email, password, nombre_completo, rol, activo) 
                    VALUES (:username, :email, :password, :fullname, 'admin', 1)
                ");
                
                $stmt->execute([
                    'username' => $nombreUsuario,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'fullname' => $nombreCompleto
                ]);
                
                $mensaje = '✅ Administrador creado exitosamente!<br><br>';
                $mensaje .= '<strong>Credenciales:</strong><br>';
                $mensaje .= 'Usuario: ' . htmlspecialchars($nombreUsuario) . '<br>';
                $mensaje .= 'Email: ' . htmlspecialchars($email) . '<br>';
                $mensaje .= 'Contraseña: (la que ingresaste)<br><br>';
                $mensaje .= '⚠️ <strong>IMPORTANTE:</strong> no olvides tu contraseña.';
                $tipo = 'success';
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
    <title>Crear Administrador</title>
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
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-red-600 to-orange-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear Administrador</h1>
            <p class="text-red-600 text-sm font-semibold">⚠️ Solo para uso gerencial del colegio</p>
        </div>

        <?php if ($mensaje): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $tipo === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>

        <?php if ($tipo !== 'success'): ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-id-card mr-2"></i>Nombre Completo
                </label>
                <input type="text" name="nombre_completo" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Ej: Juan Administrador">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Nombre de Usuario
                </label>
                <input type="text" name="nombre_usuario" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Ej: adminprincipal">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input type="email" name="email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="admin@ejemplo.com">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Contraseña
                </label>
                <input type="password" name="password" required minlength="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Mínimo 6 caracteres">
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-red-600 to-orange-700 text-white py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-shield-alt mr-2"></i>Crear Administrador
            </button>
        </form>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Advertencia de Seguridad:</strong><br>
                Este archivo permite crear administradores sin autenticación. 
                <strong>Necesitas eliminar un admin ?:</strong><br>
                Este archivo permite eliminar sin autenticación. 
                <strong> has Click aqui: </strong>
                <a href="eliminar_admin.php" class="text-yellow-800 underline">Eliminar Admin</a> 
            </p>
        </div>
        <?php else: ?>
        <div class="text-center space-y-4">
            <a href="/../login.php" 
                class="block w-full bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Ir al Login
            </a>
            
        </div>
        <?php endif; ?>
    </div>
</body>

</html>