// Variables globales
let currentLocationFilter = 'todas';
let currentMovementFilter = 'todos';

// Cargar datos al iniciar
document.addEventListener('DOMContentLoaded', () => {
    loadInventory();
    loadTools();
    loadMovements();
    setupForms();
});

// ========================================
// FUNCIONES DE TABS
// ========================================
function switchTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Remover clase active de todos los botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Mostrar tab seleccionado
    document.getElementById(`tab-${tabName}`).classList.remove('hidden');

    // Agregar clase active al botón seleccionado
    event.target.classList.add('active');

    // Recargar datos según el tab
    if (tabName === 'insumos') {
        loadInventory();
    } else if (tabName === 'herramientas') {
        loadTools();
    } else if (tabName === 'historial') {
        loadMovements();
    }
}

// ========================================
// CONFIGURAR FORMULARIOS
// ========================================
function setupForms() {
    // Formulario de insumos (solo si existe - admin)
    const inventoryForm = document.getElementById('inventory-form');
    if (inventoryForm) {
        inventoryForm.addEventListener('submit', handleAddItem);
    }

    // Formulario de movimientos de insumos
    const movementForm = document.getElementById('movement-form');
    if (movementForm) {
        movementForm.addEventListener('submit', handleMovement);
    }

    // Formulario de herramientas (solo si existe - admin)
    const toolsForm = document.getElementById('tools-form');
    if (toolsForm) {
        toolsForm.addEventListener('submit', handleAddTool);
    }

    // Formulario de movimientos de herramientas
    const toolMovementForm = document.getElementById('tool-movement-form');
    if (toolMovementForm) {
        toolMovementForm.addEventListener('submit', handleToolMovement);
    }
}

// ========================================
// INSUMOS
// ========================================
async function loadInventory() {
    try {
        const response = await fetch('api/insumos.php');
        const result = await response.json();

        if (result.success) {
            displayInventory(result.data);
            updateMovementSelect(result.data);
        }
    } catch (error) {
        console.error('Error al cargar inventario:', error);
        showNotification('Error al cargar el inventario', 'error');
    }
}

function displayInventory(items) {
    const tbody = document.getElementById('inventory-table');
    tbody.innerHTML = '';

    items.forEach(item => {
        const stockStatus = item.cantidad <= item.stock_minimo;
        const statusClass = stockStatus ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
        const statusText = stockStatus ? 'Stock Bajo' : 'Stock OK';

        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
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
        tbody.appendChild(row);
    });
}

function updateMovementSelect(items) {
    const select = document.getElementById('movement-item');
    select.innerHTML = '<option value="">Seleccionar insumo</option>';

    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.nombre;
        option.dataset.unidad = item.unidad;
        option.textContent = `${item.nombre} (${item.cantidad} ${item.unidad})`;
        select.appendChild(option);
    });
}

async function handleAddItem(e) {
    e.preventDefault();

    const data = {
        nombre: document.getElementById('item-name').value,
        cantidad: parseFloat(document.getElementById('item-quantity').value),
        unidad: document.getElementById('item-unit').value,
        stock_minimo: parseFloat(document.getElementById('item-min').value)
    };

    try {
        const response = await fetch('api/insumos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Insumo agregado exitosamente', 'success');
            e.target.reset();
            loadInventory();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al agregar el insumo', 'error');
    }
}

async function deleteItem(id, nombre) {
    if (!confirm(`¿Estás seguro de eliminar el insumo "${nombre}"?`)) return;

    try {
        const response = await fetch(`api/insumos.php?id=${id}`, {
            method: 'DELETE'
        });

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

function displayTools(tools) {
    const tbody = document.getElementById('tools-table');
    tbody.innerHTML = '';

    tools.forEach(tool => {
        const statusClass = tool.cantidad === 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
        const statusText = tool.cantidad === 0 ? 'Agotado' : 'Disponible';

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

function updateToolMovementSelect(tools) {
    const select = document.getElementById('tool-movement-item');
    select.innerHTML = '<option value="">Seleccionar herramienta</option>';

    tools.forEach(tool => {
        const option = document.createElement('option');
        option.value = tool.nombre;
        option.textContent = `${tool.nombre} (${tool.cantidad} unidades)`;
        select.appendChild(option);
    });
}

function updateLocationCounts(tools) {
    // Contar por ubicación
    const counts = {
        todas: tools.length,
        bodega: 0,
        taller: 0,
        pañol: 0
    };

    tools.forEach(tool => {
        if (counts.hasOwnProperty(tool.ubicacion)) {
            counts[tool.ubicacion]++;
        }
    });

    // Actualizar contadores en las tarjetas
    Object.keys(counts).forEach(ubicacion => {
        const element = document.getElementById(`count-${ubicacion}`);
        if (element) {
            element.textContent = counts[ubicacion];
        }
    });
}

function filterByLocation(ubicacion) {
    currentLocationFilter = ubicacion;

    // Actualizar UI de las tarjetas
    document.querySelectorAll('.location-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');

    // Actualizar texto del filtro
    const filterText = document.getElementById('location-filter-text');
    if (filterText) {
        filterText.textContent = ubicacion !== 'todas' ? `- Ubicación: ${ubicacion}` : '';
    }

    // Cargar herramientas filtradas
    loadTools(ubicacion);
}

async function handleAddTool(e) {
    e.preventDefault();

    const data = {
        nombre: document.getElementById('tool-name').value,
        cantidad: parseInt(document.getElementById('tool-quantity').value),
        categoria: document.getElementById('tool-category').value,
        ubicacion: document.getElementById('tool-location').value
    };

    try {
        const response = await fetch('api/herramientas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

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

async function deleteTool(id, nombre) {
    if (!confirm(`¿Estás seguro de eliminar la herramienta "${nombre}"?`)) return;

    try {
        const response = await fetch(`api/herramientas.php?id=${id}`, {
            method: 'DELETE'
        });

        const result = await response.json();

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
async function handleMovement(e) {
    e.preventDefault();

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

async function loadMovements(filter = 'todos') {
    currentMovementFilter = filter;

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

function displayMovements(movements) {
    const tbody = document.getElementById('movement-table');
    tbody.innerHTML = '';

    movements.forEach(mov => {
        const typeClass = mov.tipo_movimiento === 'entrada'
            ? 'bg-green-100 text-green-700'
            : 'bg-red-100 text-red-700';
        const typeIcon = mov.tipo_movimiento === 'entrada' ? 'fa-arrow-down' : 'fa-arrow-up';

        const categoryClass = mov.categoria === 'insumo'
            ? 'bg-blue-100 text-blue-700'
            : 'bg-purple-100 text-purple-700';

        const fecha = new Date(mov.fecha_movimiento);
        const fechaFormateada = fecha.toLocaleString('es-CL');

        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
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

function filterMovements(filter) {
    // Actualizar botones activos
    const buttons = document.querySelectorAll('#tab-historial button');
    buttons.forEach(btn => {
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
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-fade-in`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-xl"></i>
            <span class="font-semibold">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}