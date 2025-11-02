/*
   
    loadTools();
   
    loadMovements();
   
    setupForms();
});


function switchTab(tabName) {
    
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

   
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

   
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');

   
    event.target.classList.add('active');

    
    if (tabName === 'insumos') {
        loadInventory(); 
    } else if (tabName === 'herramientas') {
        loadTools(); // Recarga las herramientas
    } else if (tabName === 'historial') {
        loadMovements(); // Recarga el historial de movimientos
    }
}

// Función para configurar los eventos de todos los formularios
function setupForms() {
   
    const inventoryForm = document.getElementById('inventory-form');
    if (inventoryForm) {
        
        inventoryForm.addEventListener('submit', handleAddItem);
    }

    
    const movementForm = document.getElementById('movement-form');
    if (movementForm) {
        // Agrega el manejador de eventos para el envío del formulario de movimientos
        movementForm.addEventListener('submit', handleMovement);
    }

    // Obtiene referencia al formulario de herramientas (solo para administradores)
    const toolsForm = document.getElementById('tools-form');
    if (toolsForm) {
        // Agrega el manejador de eventos para el envío del formulario de herramientas
        toolsForm.addEventListener('submit', handleAddTool);
    }

    // Obtiene referencia al formulario de movimientos de herramientas
    const toolMovementForm = document.getElementById('tool-movement-form');
    if (toolMovementForm) {
        // Agrega el manejador de eventos para el envío del formulario de movimientos de herramientas
        toolMovementForm.addEventListener('submit', handleToolMovement);
    }
}*/

// Variables globales
let currentLocationFilter = 'todas';
let currentMovementFilter = 'todos';

// Cargar datos al iniciar
// Espera a que el DOM esté completamente cargado antes de ejecutar las funciones
document.addEventListener('DOMContentLoaded', () => {
    // Carga los datos del inventario inicial
    loadInventory();
    // Carga las herramientas disponibles
    loadTools();
    // Carga el historial de movimientos
    loadMovements();
    // Configura los eventos de los formularios
    setupForms();
});

// ========================================
// FUNCIONES DE TABS
// ========================================
// Función para cambiar entre diferentes pestañas de la interfaz
function switchTab(tabName) {
    // Ocultar todos los tabs
    // Selecciona todos los elementos con clase 'tab-content' y los oculta
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Remover clase active de todos los botones
    // Remueve la clase 'active' de todos los botones de pestañas
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Mostrar tab seleccionado
    // Muestra el contenido de la pestaña seleccionada removiendo la clase 'hidden'
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');

    // Agregar clase active al botón seleccionado
    // Marca como activo el botón de la pestaña seleccionada
    event.target.classList.add('active');

    // Recargar datos según el tab
    // Recarga los datos según la pestaña seleccionada
    if (tabName === 'insumos') {
        loadInventory();// Recarga los insumos
    } else if (tabName === 'herramientas') {
        loadTools();// Recarga las herramientas
    } else if (tabName === 'historial') {
        loadMovements();// Recarga el historial de movimientos
    }
}

// ========================================
// CONFIGURAR FORMULARIOS
// ========================================
// Función para configurar los eventos de todos los formularios
function setupForms() {
    // Formulario de insumos (solo si existe - admin)
    // Obtiene referencia al formulario de inventario (solo para administradores)
    const inventoryForm = document.getElementById('inventory-form');
    // Agrega el manejador de eventos para el envío del formulario de inventario
    if (inventoryForm) {
        inventoryForm.addEventListener('submit', handleAddItem);
    }
    // Formulario de movimientos de insumos
    // Obtiene referencia al formulario de movimientos de insumos
    const movementForm = document.getElementById('movement-form');
    if (movementForm) {
        // Agrega el manejador de eventos para el envío del formulario de movimientos de insumos
        movementForm.addEventListener('submit', handleMovement);
    }

    // Formulario de herramientas (solo si existe - admin)

    const toolsForm = document.getElementById('tools-form');
    if (toolsForm) {
        // Agrega el manejador de eventos para el envío del formulario de herramientas
        toolsForm.addEventListener('submit', handleAddTool);
    }

    // Formulario de movimientos de herramientas
    // Obtiene referencia al formulario de movimientos de herramientas
    const toolMovementForm = document.getElementById('tool-movement-form');
    if (toolMovementForm) {// Agrega el manejador de eventos para el envío del formulario de movimientos de herramientas
        toolMovementForm.addEventListener('submit', handleToolMovement);
    }
}

// ========================================
// INSUMOS
// ========================================
// Función para cargar y mostrar el inventario de insumos
async function loadInventory() {
    try {// Realiza una solicitud a la API para obtener los insumos
        const response = await fetch('api/insumos.php');// Parsea la respuesta JSON
        const result = await response.json();// Si la solicitud fue exitosa, muestra los insumos y actualiza el select de movimientos

        if (result.success) {
            displayInventory(result.data);
            updateMovementSelect(result.data);
        }// Maneja errores en la solicitud
    } catch (error) {
        console.error('Error al cargar inventario:', error);
        showNotification('Error al cargar el inventario', 'error');
    }
}
// Función para mostrar los insumos en la tabla
function displayInventory(items) {// Obtiene referencia al cuerpo de la tabla de inventario
    const tbody = document.getElementById('inventory-table');
    tbody.innerHTML = '';// Limpia el contenido existente en la tabla
    // Recorre cada insumo y crea una fila en la tabla
    items.forEach(item => {
        const stockStatus = item.cantidad <= item.stock_minimo;// Determina el estado del stock
        const statusClass = stockStatus ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';// Asigna clases CSS según el estado del stock
        const statusText = stockStatus ? 'Stock Bajo' : 'Stock OK';// Crea una nueva fila en la tabla

        const row = document.createElement('tr');// Asigna clases a la fila para estilos
        row.className = 'border-b hover:bg-gray-50';// Llena la fila con los datos del insumo
        // Agrega un botón de eliminar si el usuario es administrador
        row.innerHTML = `
            <td class="p-4 font-semibold">${item.nombre}</td>
            <td class="p-4">${parseFloat(item.cantidad).toFixed(2)}</td>
            <td class="p-4">${item.unidad}</td>
            <td class="p-4">${parseFloat(item.stock_minimo).toFixed(2)}</td>
            <td class="p-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold ${statusClass}">
                    ${statusText}
                </span>
            </td>
            ${isAdmin ? `
            <td class="p-4 text-center">
                <button onclick="deleteItem(${item.id}, '${item.nombre}')" 
                    class="text-red-600 hover:text-red-800 font-semibold">
                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
                </button>
            </td>
            ` : ''}
        `;
        tbody.appendChild(row);// Agrega la fila a la tabla
    });
}
// Función para actualizar el select de movimientos con los insumos disponibles
function updateMovementSelect(items) {
    // Obtiene referencia al select de movimientos
    const select = document.getElementById('movement-item');
    select.innerHTML = '<option value="">Seleccionar insumo</option>';// Limpia las opciones existentes en el select
    // Recorre cada insumo y crea una opción en el select

    items.forEach(item => {// Crea una nueva opción para el insumo
        const option = document.createElement('option');// Asigna el valor y los datos adicionales a la opción
        option.value = item.nombre;// Muestra el nombre, cantidad y unidad en el texto de la opción
        option.dataset.unidad = item.unidad;// Asigna el texto de la opción
        option.textContent = `${item.nombre} (${item.cantidad} ${item.unidad})`;// Agrega la opción al select
        select.appendChild(option);// Agrega la opción al select
    });
}
// Función para manejar el envío del formulario de agregar insumo
async function handleAddItem(e) {
    e.preventDefault();// Previene el comportamiento por defecto del formulario
    // Recopila los datos del formulario
    const data = {
        nombre: document.getElementById('item-name').value,
        cantidad: parseFloat(document.getElementById('item-quantity').value),
        unidad: document.getElementById('item-unit').value,
        stock_minimo: parseFloat(document.getElementById('item-min').value)
    };
    // Envía los datos a la API para agregar el insumo
    try {
        const response = await fetch('api/insumos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        // Parsea la respuesta JSON
        const result = await response.json();
        // Maneja la respuesta de la API
        if (result.success) {
            showNotification('Insumo agregado exitosamente', 'success');
            e.target.reset();
            loadInventory();
        } else {
            showNotification(result.message, 'error');// Muestra un mensaje de error si la operación falla
        }
    } catch (error) {
        console.error('Error:', error);// Maneja errores en la solicitud
        showNotification('Error al agregar el insumo', 'error');// Muestra un mensaje de error si ocurre una excepción
    }
}
// Función para eliminar un insumo
async function deleteItem(id, nombre) {
    // Confirma la acción con el usuario
    if (!confirm(`¿Estás seguro de eliminar el insumo "${nombre}"?`)) return;
    // Envía la solicitud de eliminación a la API
    try {
        const response = await fetch(`api/insumos.php?id=${id}`, {
            method: 'DELETE'
        });
        // Parsea la respuesta JSON
        const result = await response.json();

        if (result.success) {
            showNotification('Insumo eliminado exitosamente', 'success');
            loadInventory();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al eliminar el insumo', 'error');
    }
}

// ========================================
// HERRAMIENTAS
// ========================================
// Función para cargar y mostrar las herramientas
async function loadTools(ubicacion = 'todas') {
    try {
        const url = ubicacion !== 'todas'
            ? `api/herramientas.php?ubicacion=${ubicacion}`
            : 'api/herramientas.php';

        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            displayTools(result.data);
            updateToolMovementSelect(result.data);
            updateLocationCounts(result.data);
        }
    } catch (error) {
        console.error('Error al cargar herramientas:', error);
        showNotification('Error al cargar las herramientas', 'error');
    }
}
// Función para mostrar las herramientas en la tabla
function displayTools(tools) {
    const tbody = document.getElementById('tools-table');// Limpia el contenido existente en la tabla
    tbody.innerHTML = '';
    // Recorre cada herramienta y crea una fila en la tabla
    tools.forEach(tool => {
        const statusClass = tool.cantidad === 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
        const statusText = tool.cantidad === 0 ? 'Agotado' : 'Disponible';
        // Crea una nueva fila en la tabla
        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
        row.innerHTML = `
            <td class="p-4 font-semibold">${tool.nombre}</td>
            <td class="p-4">${tool.cantidad}</td>
            <td class="p-4 capitalize">${tool.categoria}</td>
            <td class="p-4 capitalize">${tool.ubicacion}</td>
            <td class="p-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold ${statusClass}">
                    ${statusText}
                </span>
            </td>
            ${isAdmin ? `
            <td class="p-4 text-center">
                <button onclick="deleteTool(${tool.id}, '${tool.nombre}')" 
                    class="text-red-600 hover:text-red-800 font-semibold">
                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
                </button>
            </td>
            ` : ''}
        `;
        tbody.appendChild(row);
    });
}
// Función para actualizar el select de movimientos con las herramientas disponibles
function updateToolMovementSelect(tools) {
    const select = document.getElementById('tool-movement-item');
    select.innerHTML = '<option value="">Seleccionar herramienta</option>';
    // Recorre cada herramienta y crea una opción en el select
    tools.forEach(tool => {
        const option = document.createElement('option');
        option.value = tool.nombre;
        option.textContent = `${tool.nombre} (${tool.cantidad} unidades)`;
        select.appendChild(option);
    });
}
// Función para actualizar los contadores de herramientas por ubicación
function updateLocationCounts(tools) {
    // Contar por ubicación
    const counts = {
        todas: tools.length,
        bodega: 0,
        taller: 0,
        pañol: 0
    };
    // Recorre las herramientas y cuenta por ubicación
    tools.forEach(tool => {// Incrementa el contador correspondiente
        if (counts.hasOwnProperty(tool.ubicacion)) {// Incrementa el contador correspondiente
            counts[tool.ubicacion]++;//
        }
    });

    // Actualizar contadores en las tarjetas
    // Actualiza el texto de los contadores en la interfaz
    Object.keys(counts).forEach(ubicacion => {
        const element = document.getElementById(`count-${ubicacion}`);// Actualiza el texto del contador si el elemento existe
        if (element) {
            element.textContent = counts[ubicacion];
        }
    });
}
// Función para filtrar herramientas por ubicación
function filterByLocation(ubicacion) {
    currentLocationFilter = ubicacion;

    // Actualizar UI de las tarjetas
    // Remueve la clase 'selected' de todas las tarjetas y agrega a la seleccionada
    document.querySelectorAll('.location-card').forEach(card => {
        card.classList.remove('selected');
    });
    // Agrega la clase 'selected' a la tarjeta seleccionada
    event.currentTarget.classList.add('selected');

    // Actualizar texto del filtro
    // Actualiza el texto que indica la ubicación filtrada
    const filterText = document.getElementById('location-filter-text');
    if (filterText) {
        filterText.textContent = ubicacion !== 'todas' ? `- Ubicación: ${ubicacion}` : '';
    }

    // Cargar herramientas filtradas
    loadTools(ubicacion);
}
// Función para manejar el envío del formulario de agregar herramienta
async function handleAddTool(e) {
    e.preventDefault();
    // Recopila los datos del formulario
    const data = {
        nombre: document.getElementById('tool-name').value,
        cantidad: parseInt(document.getElementById('tool-quantity').value),
        categoria: document.getElementById('tool-category').value,
        ubicacion: document.getElementById('tool-location').value
    };
    // Envía los datos a la API para agregar la herramienta
    try {
        const response = await fetch('api/herramientas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        // Parsea la respuesta JSON
        const result = await response.json();

        if (result.success) {
            showNotification('Herramienta agregada exitosamente', 'success');
            e.target.reset();
            loadTools(currentLocationFilter);
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al agregar la herramienta', 'error');
    }
}
// Función para eliminar una herramienta
async function deleteTool(id, nombre) {
    if (!confirm(`¿Estás seguro de eliminar la herramienta "${nombre}"?`)) return;
    // Envía la solicitud de eliminación a la API
    try {
        const response = await fetch(`api/herramientas.php?id=${id}`, {
            method: 'DELETE'
        });
        // Parsea la respuesta JSON
        const result = await response.json();
        // Maneja la respuesta de la API
        if (result.success) {
            showNotification('Herramienta eliminada exitosamente', 'success');
            loadTools(currentLocationFilter);
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al eliminar la herramienta', 'error');
    }
}

// ========================================
// MOVIMIENTOS
// ========================================
// Función para manejar el envío del formulario de movimientos de insumos
async function handleMovement(e) {
    e.preventDefault();
    // Obtiene el insumo seleccionado y sus datos asociados
    const select = document.getElementById('movement-item');
    const selectedOption = select.options[select.selectedIndex];

    const data = {
        elemento: select.value,
        tipo_movimiento: document.getElementById('movement-type').value,
        categoria: 'insumo',
        cantidad: parseFloat(document.getElementById('movement-quantity').value),
        unidad: selectedOption.dataset.unidad,
        motivo: document.getElementById('movement-reason').value
    };

    try {
        const response = await fetch('api/movimientos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Movimiento registrado exitosamente', 'success');
            e.target.reset();
            loadInventory();
            loadMovements();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al registrar el movimiento', 'error');
    }
}
// Función para manejar el envío del formulario de movimientos de herramientas
async function handleToolMovement(e) {
    e.preventDefault();

    const data = {
        elemento: document.getElementById('tool-movement-item').value,
        tipo_movimiento: document.getElementById('tool-movement-type').value,
        categoria: 'herramienta',
        cantidad: parseInt(document.getElementById('tool-movement-quantity').value),
        unidad: 'unid',
        motivo: document.getElementById('tool-movement-reason').value
    };

    try {
        const response = await fetch('api/movimientos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Movimiento registrado exitosamente', 'success');
            e.target.reset();
            loadTools(currentLocationFilter);
            loadMovements();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al registrar el movimiento', 'error');
    }
}
// Función para cargar y mostrar el historial de movimientos
async function loadMovements(filter = 'todos') {
    currentMovementFilter = filter;
    // Realiza una solicitud a la API para obtener los movimientos según el filtro
    try {
        const url = filter !== 'todos'
            ? `api/movimientos.php?categoria=${filter}`
            : 'api/movimientos.php';

        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            displayMovements(result.data);
        }
    } catch (error) {
        console.error('Error al cargar movimientos:', error);
        showNotification('Error al cargar el historial', 'error');
    }
}
// Función para mostrar los movimientos en la tabla
function displayMovements(movements) {
    const tbody = document.getElementById('movement-table');
    tbody.innerHTML = '';
    // Recorre cada movimiento y crea una fila en la tabla
    movements.forEach(mov => {
        const typeClass = mov.tipo_movimiento === 'entrada'
            ? 'bg-green-100 text-green-700'
            : 'bg-red-100 text-red-700';
        const typeIcon = mov.tipo_movimiento === 'entrada' ? 'fa-arrow-down' : 'fa-arrow-up';
        // Asigna clases según la categoría del movimiento
        const categoryClass = mov.categoria === 'insumo'
            ? 'bg-blue-100 text-blue-700'
            : 'bg-purple-100 text-purple-700';
        // Formatea la fecha del movimiento
        const fecha = new Date(mov.fecha_movimiento);
        const fechaFormateada = fecha.toLocaleString('es-CL');
        // Crea una nueva fila en la tabla
        const row = document.createElement('tr');// Asigna clases a la fila para estilos
        row.className = 'border-b hover:bg-gray-50';
        // Llena la fila con los datos del movimiento
        row.innerHTML = `
            <td class="p-4 text-sm">${fechaFormateada}</td>
            <td class="p-4">
                <span class="px-3 py-1 rounded-full text-xs font-semibold ${categoryClass}">
                    ${mov.categoria}
                </span>
            </td>
            <td class="p-4 font-semibold">${mov.elemento}</td>
            <td class="p-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold ${typeClass}">
                    <i class="fas ${typeIcon} mr-1"></i>${mov.tipo_movimiento}
                </span>
            </td>
            <td class="p-4">${parseFloat(mov.cantidad).toFixed(2)} ${mov.unidad}</td>
            <td class="p-4 text-sm">${mov.motivo}</td>
            ${isAdmin ? `
            <td class="p-4 text-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user text-gray-400"></i>
                    <span>${mov.nombre_completo || mov.nombre_usuario || 'N/A'}</span>
                </div>
            </td>
            ` : ''}
        `;
        tbody.appendChild(row);
    });
}
// Función para filtrar movimientos por categoría
function filterMovements(filter) {
    // Actualizar botones activos
    // Actualiza las clases de los botones de filtro según la selección
    const buttons = document.querySelectorAll('#tab-historial button');
    buttons.forEach(btn => {// Reinicia las clases de los botones
        btn.className = 'px-4 py-2 rounded-lg font-semibold';
        if (btn.textContent.toLowerCase().includes(filter) ||
            (filter === 'todos' && btn.textContent === 'Todos')) {
            btn.className += ' bg-indigo-100 text-indigo-600';
        } else {
            btn.className += ' bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-600';
        }
    });

    loadMovements(filter);
}

// ========================================
// UTILIDADES
// ========================================
// Función para mostrar notificaciones en la pantalla
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    // Crea el contenedor de la notificación
    const notification = document.createElement('div');// Asigna clases para estilos y animaciones
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-xl"></i>
            <span class="font-semibold">${message}</span>
        </div>
    `;
    // Agrega la notificación al cuerpo del documento
    document.body.appendChild(notification);
    // Remueve la notificación después de 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}