-- ============================================
-- Base de Datos: Gestión de Cocina con Sistema de Usuarios
-- ============================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS gestion_cocina2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gestion_cocina2;

-- ============================================
-- Tabla: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre_usuario (nombre_usuario),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: insumos
-- ============================================
CREATE TABLE IF NOT EXISTS insumos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cantidad int  NOT NULL DEFAULT 0,
    unidad VARCHAR(20) NOT NULL,
    stock_minimo int  OT NULL DEFAULT 0,
    usuario_creacion INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_nombre (nombre),
    FOREIGN KEY (usuario_creacion) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: herramientas
-- ============================================
CREATE TABLE IF NOT EXISTS herramientas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    categoria VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(50) NOT NULL,
    usuario_creacion INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_nombre (nombre),
    FOREIGN KEY (usuario_creacion) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: movimientos
-- ============================================
CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    elemento VARCHAR(100) NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
    categoria ENUM('insumo', 'herramienta') NOT NULL,
    cantidad int  NOT NULL,
    unidad VARCHAR(20) NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_fecha (fecha_movimiento),
    INDEX idx_usuario (usuario_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Datos de prueba
-- ============================================

-- Insertar usuarios de prueba
-- Password para ambos: "123456"
INSERT INTO usuarios (nombre_usuario, email, password, nombre_completo, rol) VALUES
('admin', 'admin@cocina.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador del Sistema', 'admin'),
('usuario1', 'usuario1@cocina.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario Normal', 'usuario');

-- Insertar algunos insumos de ejemplo
INSERT INTO insumos (nombre, cantidad, unidad, stock_minimo, usuario_creacion) VALUES
('Arroz', 50.00, 'kg', 10.00, 1),
('Aceite', 20.00, 'lt', 5.00, 1),
('Sal', 15.00, 'kg', 3.00, 1),
('Azúcar', 25.00, 'kg', 5.00, 1),
('Harina', 30.00, 'kg', 10.00, 1);

-- Insertar algunas herramientas de ejemplo
INSERT INTO herramientas (nombre, cantidad, categoria, ubicacion, usuario_creacion) VALUES
('Cuchillos Chef', 10, 'cubiertos', 'bodega', 1),
('Ollas Grandes', 5, 'ollas', 'taller', 1),
('Sartenes', 8, 'ollas', 'bodega', 1),
('Cucharones', 15, 'utensilios', 'pañol', 1),
('Tablas de Cortar', 12, 'utensilios', 'taller', 1);

-- Insertar algunos movimientos de ejemplo
INSERT INTO movimientos (elemento, tipo_movimiento, categoria, cantidad, unidad, motivo, usuario_id) VALUES
('Arroz', 'entrada', 'insumo', 50.00, 'kg', 'Compra inicial', 1),
('Aceite', 'entrada', 'insumo', 20.00, 'lt', 'Reposición', 1),
('Cuchillos Chef', 'entrada', 'herramienta', 10.00, 'unid', 'Inventario inicial', 1),
('Arroz', 'salida', 'insumo', 5.00, 'kg', 'Preparación de almuerzo', 2),
('Sartenes', 'salida', 'herramienta', 2.00, 'unid', 'Préstamo a cocina externa', 2);