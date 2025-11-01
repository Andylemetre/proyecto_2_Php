<?php
require_once __DIR__ . '/config/Auth.php';

// Si ya está autenticado, redirigir al inicio
if (Auth::check()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Gestión de Cocina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .shake {
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Crear Cuenta</h1>
            <p class="text-gray-600">Regístrate en el sistema</p>
        </div>

        <div id="message-container"></div>

        <form id="registro-form" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-id-card mr-2"></i>Nombre Completo
                </label>
                <input type="text" id="nombre-completo" required autocomplete="name"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Ej: Juan Pérez García" minlength="3">
                <p class="text-xs text-gray-500 mt-1">Mínimo 3 caracteres</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Nombre de Usuario
                </label>
                <input type="text" id="nombre-usuario" required autocomplete="username"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Ej: juanperez" minlength="3" pattern="[a-zA-Z0-9_]+">
                <p class="text-xs text-gray-500 mt-1">Solo letras, números y guión bajo. Mínimo 3 caracteres</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input type="email" id="email" required autocomplete="email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    placeholder="Ej: juan@ejemplo.com">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Contraseña
                </label>
                <div class="relative">
                    <input type="password" id="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-12"
                        placeholder="Mínimo 6 caracteres" minlength="6">
                    <button type="button" onclick="togglePassword('password', 'password-icon')" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i id="password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <div id="password-strength" class="password-strength bg-gray-200"></div>
                    <p id="password-strength-text" class="text-xs text-gray-500 mt-1">Ingresa una contraseña</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Confirmar Contraseña
                </label>
                <div class="relative">
                    <input type="password" id="confirm-password" required autocomplete="new-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-12"
                        placeholder="Repite tu contraseña">
                    <button type="button" onclick="togglePassword('confirm-password', 'confirm-password-icon')" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i id="confirm-password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="registro-btn"
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition mt-6 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <i class="fas fa-user-plus mr-2"></i>Registrarse
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">¿Ya tienes cuenta? 
                <a href="login.php" class="text-indigo-600 font-semibold hover:text-indigo-800">Inicia sesión aquí</a>
            </p>
        </div>
    </div>

    <script>
        const registroForm = document.getElementById('registro-form');
        const registroBtn = document.getElementById('registro-btn');
        const messageContainer = document.getElementById('message-container');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm-password');

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function showMessage(message, type = 'error') {
            const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            messageContainer.innerHTML = `
                <div class="shake ${bgColor} border px-4 py-3 rounded-lg mb-4">
                    <i class="fas ${icon} mr-2"></i>${message}
                </div>
            `;
        }

        function setLoading(isLoading) {
            registroBtn.disabled = isLoading;
            if (isLoading) {
                registroBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Registrando...';
            } else {
                registroBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Registrarse';
            }
        }

        // Validación de fortaleza de contraseña
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('password-strength-text');
            
            let strength = 0;
            let text = '';
            let color = '';
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    text = 'Débil';
                    color = 'bg-red-500';
                    break;
                case 2:
                case 3:
                    text = 'Media';
                    color = 'bg-yellow-500';
                    break;
                case 4:
                case 5:
                    text = 'Fuerte';
                    color = 'bg-green-500';
                    break;
            }
            
            strengthBar.style.width = (strength * 20) + '%';
            strengthBar.className = 'password-strength ' + color;
            strengthText.textContent = 'Fortaleza: ' + text;
        });

        // Validación de coincidencia de contraseñas
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        registroForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const nombreCompleto = document.getElementById('nombre-completo').value.trim();
            const nombreUsuario = document.getElementById('nombre-usuario').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Validaciones del lado del cliente
            if (nombreCompleto.length < 3) {
                showMessage('El nombre completo debe tener al menos 3 caracteres');
                return;
            }
            
            if (nombreUsuario.length < 3) {
                showMessage('El nombre de usuario debe tener al menos 3 caracteres');
                return;
            }
            
            if (!/^[a-zA-Z0-9_]+$/.test(nombreUsuario)) {
                showMessage('El nombre de usuario solo puede contener letras, números y guión bajo');
                return;
            }
            
            if (password.length < 6) {
                showMessage('La contraseña debe tener al menos 6 caracteres');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Las contraseñas no coinciden');
                return;
            }
            
            setLoading(true);
            messageContainer.innerHTML = '';
            
            try {
                const response = await fetch('api/registro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    body: JSON.stringify({
                        nombre_completo: nombreCompleto,
                        nombre_usuario: nombreUsuario,
                        email: email,
                        password: password
                    })
                });
                
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage(data.message || 'Usuario registrado exitosamente', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showMessage(data.message || 'Error al registrar usuario');
                    setLoading(false);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión. Por favor, intenta de nuevo.');
                setLoading(false);
            }
        });

        // Focus automático en el primer campo
        document.getElementById('nombre-completo').focus();
    </script>
</body>
</html>