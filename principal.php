<?php
// Incluye la clase de autenticación
require_once __DIR__ . '/config/Auth.php';

// Verifica si el usuario ya está autenticado y lo redirige al sistema
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
    <title>Sistema de Gestión de Inventario - Cocina Escolar</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            animation: wave 15s ease-in-out infinite;
        }
        
        @keyframes wave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .feature-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="scroll-smooth">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 glass-card">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-utensils text-white text-3xl"></i>
                    <span class="text-white text-2xl font-bold">GestiónCocina</span>
                </div>
                <div class="hidden md:flex gap-4 items-center">
                    <a href="#caracteristicas" class="text-blue hover:text-indigo-400 transition px-4 py-2">Características</a>
                    <a href="#beneficios" class="text-blue hover:text-indigo-400 transition px-4 py-2">Beneficios</a>
                    <a href="login.php" class="bg-white text-indigo-600 px-6 py-2 rounded-lg font-semibold hover:bg-opacity-90 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </a>
                </div>
                <!-- Mobile menu button -->
                <button id="mobile-menu-btn" class="md:hidden text-white text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
                <a href="#caracteristicas" class="block text-white hover:text-indigo-200 transition px-4 py-2">Características</a>
                <a href="#beneficios" class="block text-white hover:text-indigo-200 transition px-4 py-2">Beneficios</a>
                <a href="login.php" class="block bg-white text-indigo-600 px-6 py-2 rounded-lg font-semibold hover:bg-opacity-90 transition text-center mt-2">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container mx-auto px-6 z-10">
            <div class="text-center fade-in">
                <div class="floating mb-8">
                    <i class="fas fa-warehouse text-white text-8xl opacity-90"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Sistema de Gestión de Inventario
                </h1>
                <p class="text-lg md:text-2xl text-white opacity-90 mb-8 max-w-3xl mx-auto">
                    Controla y administra el inventario de la cocina escolar de manera eficiente, 
                    organizada y en tiempo real
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="login.php" class="btn-primary text-white px-8 py-4 rounded-lg font-bold text-lg inline-block">
                        <i class="fas fa-rocket mr-2"></i>Comenzar Ahora
                    </a>
                    <a href="#caracteristicas" class="glass-card text-white px-8 py-4 rounded-lg font-bold text-lg inline-block hover:bg-white hover:bg-opacity-20 transition">
                        <i class="fas fa-info-circle mr-2"></i>Conocer Más
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                <?php
                $stats = [
                    ['number' => '2', 'title' => 'Tipos de Inventario', 'subtitle' => 'Insumos y Herramientas'],
                    ['number' => '100%', 'title' => 'Control en Tiempo Real', 'subtitle' => 'Actualización instantánea'],
                    ['number' => '3', 'title' => 'Ubicaciones', 'subtitle' => 'Bodega, Taller y Pañol'],
                    ['number' => '∞', 'title' => 'Historial Completo', 'subtitle' => 'Seguimiento de movimientos']
                ];
                
                foreach ($stats as $stat): ?>
                    <div class="text-center">
                        <div class="stat-number"><?php echo htmlspecialchars($stat['number']); ?></div>
                        <p class="text-gray-600 text-lg font-semibold"><?php echo htmlspecialchars($stat['title']); ?></p>
                        <p class="text-gray-500 mt-2"><?php echo htmlspecialchars($stat['subtitle']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="caracteristicas" class="py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Características Principales</h2>
                <p class="text-xl text-gray-600">Todo lo que necesitas para gestionar tu inventario</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $features = [
                    [
                        'icon' => 'fa-boxes',
                        'color' => 'blue',
                        'title' => 'Gestión de Insumos',
                        'description' => 'Registra y controla todos los insumos de la cocina con alertas de stock mínimo para evitar desabastecimientos.',
                        'items' => ['Control de cantidades', 'Múltiples unidades de medida', 'Alertas de stock bajo']
                    ],
                    [
                        'icon' => 'fa-tools',
                        'color' => 'orange',
                        'title' => 'Control de Herramientas',
                        'description' => 'Administra herramientas y utensilios organizados por categorías y ubicaciones para un acceso rápido.',
                        'items' => ['Organización por categorías', 'Ubicación específica', 'Estado en tiempo real']
                    ],
                    [
                        'icon' => 'fa-history',
                        'color' => 'purple',
                        'title' => 'Historial de Movimientos',
                        'description' => 'Registro completo de todas las entradas y salidas con información del usuario y motivo del movimiento.',
                        'items' => ['Trazabilidad completa', 'Registro de usuarios', 'Filtros avanzados']
                    ],
                    [
                        'icon' => 'fa-shield-alt',
                        'color' => 'green',
                        'title' => 'Sistema de Permisos',
                        'description' => 'Control de acceso con roles de administrador y usuario para mantener la seguridad de la información.',
                        'items' => ['Roles diferenciados', 'Autenticación segura', 'Permisos granulares']
                    ],
                    [
                        'icon' => 'fa-chart-line',
                        'color' => 'red',
                        'title' => 'Interfaz Intuitiva',
                        'description' => 'Diseño moderno y fácil de usar con navegación por pestañas y visualización clara de la información.',
                        'items' => ['Diseño responsive', 'Navegación intuitiva', 'Visualización clara']
                    ],
                    [
                        'icon' => 'fa-exchange-alt',
                        'color' => 'indigo',
                        'title' => 'Registro de Movimientos',
                        'description' => 'Sistema completo para registrar entradas y salidas de forma rápida con validación automática de stock.',
                        'items' => ['Validación automática', 'Registro rápido', 'Control de stock']
                    ]
                ];
                
                foreach ($features as $feature): ?>
                    <div class="feature-card bg-white p-8 rounded-xl shadow-lg">
                        <div class="bg-gradient-to-r from-<?php echo $feature['color']; ?>-500 to-<?php echo $feature['color']; ?>-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <i class="fas <?php echo $feature['icon']; ?> text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($feature['title']); ?></h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo htmlspecialchars($feature['description']); ?>
                        </p>
                        <ul class="text-gray-600 space-y-2">
                            <?php foreach ($feature['items'] as $item): ?>
                                <li><i class="fas fa-check text-green-500 mr-2"></i><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="beneficios" class="py-20 bg-gradient-to-r from-indigo-600 to-purple-700">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Beneficios para tu Institución</h2>
                <p class="text-xl text-white opacity-90">Optimiza la gestión de recursos</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php
                $benefits = [
                    [
                        'icon' => 'fa-clock',
                        'title' => 'Ahorro de Tiempo',
                        'description' => 'Reduce el tiempo dedicado al control de inventario con registros automáticos y acceso rápido a la información.'
                    ],
                    [
                        'icon' => 'fa-dollar-sign',
                        'title' => 'Reducción de Costos',
                        'description' => 'Evita pérdidas y compras innecesarias con alertas de stock mínimo y control preciso de cantidades.'
                    ],
                    [
                        'icon' => 'fa-users',
                        'title' => 'Trabajo en Equipo',
                        'description' => 'Múltiples usuarios pueden acceder y registrar movimientos de forma coordinada con trazabilidad completa.'
                    ],
                    [
                        'icon' => 'fa-chart-bar',
                        'title' => 'Toma de Decisiones',
                        'description' => 'Información en tiempo real para tomar decisiones informadas sobre compras y distribución de recursos.'
                    ]
                ];
                
                foreach ($benefits as $benefit): ?>
                    <div class="glass-card p-8 rounded-xl">
                        <i class="fas <?php echo $benefit['icon']; ?> text-white text-4xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-white mb-3"><?php echo htmlspecialchars($benefit['title']); ?></h3>
                        <p class="text-white opacity-90">
                            <?php echo htmlspecialchars($benefit['description']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">¿Listo para comenzar?</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Únete al sistema de gestión de inventario más completo y eficiente 
                para instituciones educativas
            </p>
            <a href="login.php" class="btn-primary text-white px-12 py-5 rounded-lg font-bold text-xl inline-block">
                <i class="fas fa-sign-in-alt mr-3"></i>Acceder al Sistema
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-utensils text-3xl"></i>
                        <span class="text-2xl font-bold">GestiónCocina</span>
                    </div>
                    <p class="text-gray-400">
                        Sistema completo de gestión de inventario para la ocina.
                    </p>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#caracteristicas" class="text-gray-400 hover:text-white transition">Características</a></li>
                        <li><a href="#beneficios" class="text-gray-400 hover:text-white transition">Beneficios</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition">Iniciar Sesión</a></li>
                    </ul>
                </div>
                
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> GestiónCocina.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scroll para los enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Animación de aparición al hacer scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>