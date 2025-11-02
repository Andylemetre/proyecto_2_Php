<?php
// Incluye la clase de autenticación
require_once __DIR__ . '/config/Auth.php';

// Si el usuario ya está autenticado, lo redirige al inicio
if (Auth::check()) {
    header('Location: index.php');
    exit;
}

// Verifica si la sesión expiró por inactividad
$expired = isset($_GET['expired']) && $_GET['expired'] == '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestión de Cocina</title>
    <!-- Tailwind CSS para estilos rápidos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins para mejor apariencia -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Iconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos generales del body */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        /* Animación de shake para mensajes de error */
        .shake {
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Contenedor principal del login -->
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <!-- Encabezado con icono y título -->
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-utensils text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Bienvenido</h1>
            <p class="text-gray-600">Sistema de Gestión de Cocina</p>
        </div>

        <!-- Contenedor para mostrar mensajes dinámicos -->
        <div id="message-container"></div>

        <!-- Mensaje si la sesión expiró -->
        <?php if ($expired): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-clock mr-2"></i>Tu sesión expiró por inactividad. Por favor, inicia sesión nuevamente.
        </div>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form id="login-form" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Usuario o Email
                </label>
                <input type="text" id="login-username" required autocomplete="username"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Ingresa tu usuario o email">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Contraseña
                </label>
                <div class="relative">
                    <!-- Campo de contraseña con botón para mostrar/ocultar -->
                    <input type="password" id="login-password" required autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-12"
                        placeholder="Ingresa tu contraseña">
                    <button type="button" onclick="togglePassword()" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i id="password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Botón para enviar el formulario -->
            <button type="submit" id="login-btn"
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
            </button>
        </form>

        <!-- Enlace para registro de nuevos usuarios -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">¿No tienes cuenta? 
                <a href="registro.php" class="text-indigo-600 font-semibold hover:text-indigo-800">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <script>
        // Referencias a elementos del DOM
        const loginForm = document.getElementById('login-form');
        const loginBtn = document.getElementById('login-btn');
        const messageContainer = document.getElementById('message-container');

        // Función para mostrar/ocultar la contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('login-password');
            const passwordIcon = document.getElementById('password-icon');
            
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Muestra mensajes de error o éxito en el contenedor
        function showMessage(message, type = 'error') {
            const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            messageContainer.innerHTML = `
                <div class="shake ${bgColor} border px-4 py-3 rounded-lg mb-4">
                    <i class="fas ${icon} mr-2"></i>${message}
                </div>
            `;
            
            // Oculta automáticamente los mensajes de éxito después de 3 segundos
            if (type === 'success') {
                setTimeout(() => {
                    messageContainer.innerHTML = '';
                }, 3000);
            }
        }

        // Cambia el estado del botón de login (cargando o normal)
        function setLoading(isLoading) {
            loginBtn.disabled = isLoading;
            if (isLoading) {
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Iniciando sesión...';
            } else {
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión';
            }
        }

        // Maneja el envío del formulario de login
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('login-username').value.trim();
            const password = document.getElementById('login-password').value;
            
            // Validaciones del lado del cliente
            if (!username || !password) {
                showMessage('Por favor, completa todos los campos');
                return;
            }
            
            if (password.length < 6) {
                showMessage('La contraseña debe tener al menos 6 caracteres');
                return;
            }
            
            setLoading(true);
            messageContainer.innerHTML = '';
            
            try {
                // Envía los datos al backend usando fetch
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ username, password })
                });
                
                const data = await response.json();
                
                // Si el login es exitoso, muestra mensaje y redirige
                if (data.success) {
                    showMessage(data.message || 'Inicio de sesión exitoso', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    // Si hay error, muestra mensaje y habilita el botón
                    showMessage(data.message || 'Error al iniciar sesión');
                    setLoading(false);
                }
            } catch (error) {
                // Error de conexión
                console.error('Error:', error);
                showMessage('Error de conexión. Por favor, intenta de nuevo.');
                setLoading(false);
            }
        });

        // Pone el foco automáticamente en el campo de usuario al cargar la página
        document.getElementById('login-username').focus();
    </script>
</body>
</html>