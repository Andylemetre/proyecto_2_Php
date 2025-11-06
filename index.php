<?php
// Incluye la clase de autenticación
require_once __DIR__ . '/config/Auth.php';

// Requiere que el usuario esté autenticado antes de mostrar la página
Auth::requireAuth();

// Obtiene el nombre de usuario actual
$userName = Auth::getUserName();
// Obtiene el nombre completo del usuario desde la sesión
$userFullName = $_SESSION['user_fullname'];
// Obtiene el rol del usuario (por ejemplo, admin o usuario)
$userRole = Auth::getUserRole();
// Verifica si el usuario es administrador
$isAdmin = Auth::isAdmin();
?>
<!DOCTYPE html>
<html lang="es">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Cocina</title>
    
    <!-- Incluye Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Poppins desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Incluye Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Hoja de estilos personalizada -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="min-h-screen">
    <!-- Encabezado de la página -->
    <header class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white p-6 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <!-- Título principal -->
                <h1 class="text-4xl font-bold flex items-center gap-3">
                    <i class="fas fa-utensils"></i> Gestión de Cocina
                </h1>
                <!-- Subtítulo -->
                <p class="text-indigo-200 mt-2">Sistema completo de inventario y herramientas</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <!-- Muestra el nombre completo del usuario -->
                    <p class="font-semibold"><?php echo htmlspecialchars($userFullName); ?></p>
                    <!-- Muestra el rol del usuario con un icono -->
                    <p class="text-sm text-indigo-200">
                        <i class="fas fa-<?php echo $isAdmin ? 'shield-alt' : 'user'; ?> mr-1"></i>
                        <?php echo $isAdmin ? 'Administrador' : 'Usuario'; ?>
                    </p>
                </div> 
                <!-- Botón para cerrar sesión -->
                <button onclick="logout()" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </button>
                <?php if ($isAdmin): ?>
                 <a href="admin_usuarios.php" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-home mr-2"></i> Administrar Usuarios
                    </a>
                    <?php endif; ?>
                                
                                
            </div>
        </div>
    </header>

    <main class="container mx-auto p-6 max-w-7xl">
        <!-- Tabs de navegación -->
         
        <div class="flex gap-4 mb-8 border-b-2 border-white bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur">
            <!-- Botón para la pestaña de insumos -->
            <button class="tab-btn active text-white font-semibold px-6 py-2" onclick="switchTab('insumos')">
                <i class="fas fa-boxes mr-2"></i>Insumos
            </button>
            <!-- Botón para la pestaña de herramientas -->
            <button class="tab-btn text-white font-semibold px-6 py-2" onclick="switchTab('herramientas')">
                <i class="fas fa-tools mr-2"></i>Herramientas
            </button>
            <!-- Botón para la pestaña de historial -->
            <button class="tab-btn text-white font-semibold px-6 py-2" onclick="switchTab('historial')">
                <i class="fas fa-history mr-2"></i>Historial
            </button>
        </div>
        
        <!-- TAB: INSUMOS -->
        <div id="tab-insumos" class="tab-content">
            <!-- Sección para agregar nuevos insumos (SOLO ADMIN) -->
            <?php if ($isAdmin): ?>
            <section class="bg-white p-8 rounded-xl card-shadow mb-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-indigo-600"></i>Añadir Nuevo Insumo
                </h2>
                <form id="inventory-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <input type="text" id="item-name" placeholder="Nombre del insumo" class="input-field p-3 rounded-lg" required>
                    <input type="number" id="item-quantity" placeholder="Cantidad" class="input-field p-3 rounded-lg" min="1" step="1" required>
                    <select id="item-unit" class="input-field p-3 rounded-lg" required>
                        <option value="">Unidad de medida</option>
                        <option value="unid">Unidades</option>
                        <option value="kg">Kilogramos</option>
                        <option value="lt">Litros</option>
                        <option value="gr">Gramos</option>
                    </select>
                    <input type="number" id="item-min" placeholder="Stock mínimo" class="input-field p-3 rounded-lg" min="1" step="1" required>
                    <button type="submit" class="btn-primary text-white p-3 rounded-lg font-semibold">
                        <i class="fas fa-check mr-2"></i>Añadir
                    </button>
                </form>
            </section>
       
            <?php endif; ?>

            <!-- Tabla de inventario -->
            <section class="bg-white p-8 rounded-xl card-shadow">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-warehouse text-indigo-600"></i>Inventario Actual
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-indigo-50 to-purple-50">
                                <th class="p-4 text-left font-semibold text-gray-700">Insumo</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Cantidad</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Unidad</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Mínimo</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Estado</th>
                                <?php if ($isAdmin): ?>
                                <th class="p-4 text-center font-semibold text-gray-700">Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="inventory-table"></tbody>
                    </table>
                </div>
            </section>

            <!-- Formulario para registrar movimientos de insumos -->
            <section class="bg-white p-8 rounded-xl card-shadow mt-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-indigo-600"></i>Registrar Movimiento de Insumo
                </h2>
                <form id="movement-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <select id="movement-item" class="input-field p-3 rounded-lg" required></select>
                    <select id="movement-type" class="input-field p-3 rounded-lg" required>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                    <input type="number" id="movement-quantity" placeholder="Cantidad" class="input-field p-3 rounded-lg" min="1" step="1" required>
                    <input type="text" id="movement-reason" placeholder="Motivo" class="input-field p-3 rounded-lg" required>
                    <button type="submit" class="btn-primary text-white p-3 rounded-lg font-semibold">
                        <i class="fas fa-arrow-right mr-2"></i>Registrar
                    </button>
                </form>
            </section>
        </div>

        <!-- TAB: HERRAMIENTAS -->
        <div id="tab-herramientas" class="tab-content hidden">
            <!-- Formulario para añadir nuevas herramientas (SOLO ADMIN) -->
            <?php if ($isAdmin): ?>
            <section class="bg-white p-8 rounded-xl card-shadow mb-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-indigo-600"></i>Añadir Nueva Herramienta
                </h2>
                <form id="tools-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <input type="text" id="tool-name" placeholder="Nombre de la herramienta" class="input-field p-3 rounded-lg" required>
                    <input type="number" id="tool-quantity" placeholder="Cantidad" class="input-field p-3 rounded-lg" min="0" required>
                    <select id="tool-category" class="input-field p-3 rounded-lg" required>
                        <option value="">Categoría</option>
                        <option value="cubiertos">Cubiertos</option>
                        <option value="utensilios">Utensilios</option>
                        <option value="ollas">Ollas y Sartenes</option>
                        <option value="otros">Otros</option>
                    </select>
                    <select id="tool-location" class="input-field p-3 rounded-lg" required>
                        <option value="">Ubicación</option>
                        <option value="bodega">Bodega</option>
                        <option value="taller">Taller</option>
                        <option value="pañol">Pañol</option>
                    </select>
                    <button type="submit" class="btn-primary text-white p-3 rounded-lg font-semibold">
                        <i class="fas fa-check mr-2"></i>Añadir
                    </button>
                </form>
            </section>
            <?php endif; ?>

            <!-- Sección para filtrar herramientas por ubicación -->
            <section class="bg-white p-8 rounded-xl card-shadow mb-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-indigo-600"></i>Filtrar por Ubicación
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="location-card bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl text-center selected" onclick="filterByLocation('todas')">
                        <i class="fas fa-globe text-4xl text-blue-600 mb-3"></i>
                        <h3 class="font-bold text-lg text-gray-800">Todas</h3>
                        <p class="text-sm text-gray-600 mt-2">Ver todo el inventario</p>
                        <p class="text-2xl font-bold text-blue-600 mt-2" id="count-todas">0</p>
                    </div>
                    <div class="location-card bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl text-center" onclick="filterByLocation('bodega')">
                        <i class="fas fa-warehouse text-4xl text-green-600 mb-3"></i>
                        <h3 class="font-bold text-lg text-gray-800">Bodega</h3>
                        <p class="text-sm text-gray-600 mt-2">Almacenamiento principal</p>
                        <p class="text-2xl font-bold text-green-600 mt-2" id="count-bodega">0</p>
                    </div>
                    <div class="location-card bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl text-center" onclick="filterByLocation('taller')">
                        <i class="fas fa-tools text-4xl text-orange-600 mb-3"></i>
                        <h3 class="font-bold text-lg text-gray-800">Taller</h3>
                        <p class="text-sm text-gray-600 mt-2">Área de trabajo</p>
                        <p class="text-2xl font-bold text-orange-600 mt-2" id="count-taller">0</p>
                    </div>
                    <div class="location-card bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl text-center" onclick="filterByLocation('pañol')">
                        <i class="fas fa-box text-4xl text-purple-600 mb-3"></i>
                        <h3 class="font-bold text-lg text-gray-800">Pañol</h3>
                        <p class="text-sm text-gray-600 mt-2">Almacén de equipos</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2" id="count-pañol">0</p>
                    </div>
                </div>
            </section>

            <!-- Tabla de herramientas -->
            <section class="bg-white p-8 rounded-xl card-shadow">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-wrench text-indigo-600"></i>Herramientas Disponibles
                    <span id="location-filter-text" class="text-lg font-normal text-gray-600"></span>
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-indigo-50 to-purple-50">
                                <th class="p-4 text-left font-semibold text-gray-700">Herramienta</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Cantidad</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Categoría</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Ubicación</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Estado</th>
                                <?php if ($isAdmin): ?>
                                <th class="p-4 text-center font-semibold text-gray-700">Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="tools-table"></tbody>
                    </table>
                </div>
            </section>

            <!-- Formulario para registrar movimientos de herramientas -->
            <section class="bg-white p-8 rounded-xl card-shadow mt-8">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-indigo-600"></i>Registrar Movimiento de Herramienta
                </h2>
                <form id="tool-movement-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <select id="tool-movement-item" class="input-field p-3 rounded-lg" required></select>
                    <select id="tool-movement-type" class="input-field p-3 rounded-lg" required>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                    <input type="number" id="tool-movement-quantity" placeholder="Cantidad" class="input-field p-3 rounded-lg" min="1" required>
                    <input type="text" id="tool-movement-reason" placeholder="Motivo" class="input-field p-3 rounded-lg" required>
                    <button type="submit" class="btn-primary text-white p-3 rounded-lg font-semibold">
                        <i class="fas fa-arrow-right mr-2"></i>Registrar
                    </button>
                </form>
            </section>
        </div>

        <!-- TAB: HISTORIAL -->
        <div id="tab-historial" class="tab-content hidden">
            <section class="bg-white p-8 rounded-xl card-shadow">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-600"></i>Historial de Movimientos
                </h2>
                <div class="mb-4 flex gap-2">
                    <button onclick="filterMovements('todos')" class="px-4 py-2 rounded-lg bg-indigo-100 text-indigo-600 font-semibold">Todos</button>
                    <button onclick="filterMovements('insumos')" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-semibold hover:bg-indigo-100 hover:text-indigo-600">Insumos</button>
                    <button onclick="filterMovements('herramientas')" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-semibold hover:bg-indigo-100 hover:text-indigo-600">Herramientas</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-indigo-50 to-purple-50">
                                <th class="p-4 text-left font-semibold text-gray-700">Fecha</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Tipo</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Elemento</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Movimiento</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Cantidad</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Motivo</th>
                                <?php if ($isAdmin): ?>
                                <th class="p-4 text-left font-semibold text-gray-700">Usuario</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="movement-table"></tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Variable global para el rol del usuario
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
        
        // Función para cerrar sesión
        async function logout() {
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                try {
                    await fetch('api/logout.php');
                    window.location.href = 'login.php';
                } catch (error) {
                    console.error('Error al cerrar sesión:', error);
                    window.location.href = 'login.php';
                }
            }
        }
    </script>
    <script src="assets/js/app.js"></script>
</body>

</html>