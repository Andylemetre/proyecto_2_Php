<?php
/**
 * Script para verificar que todos los archivos estén en su lugar
 * y mostrar instrucciones si falta alguno.
 */

$archivosRequeridos = [
    // Archivos raíz
    'index.php',
    'login.php',
    'registro.php',
    'admin_usuarios.php',
    
    // Carpeta config
    'config/db.php',
    'config/Auth.php',
    
    // Carpeta api
    'api/login.php',
    'api/registro.php',
    'api/logout.php',
    'api/insumos.php',
    'api/herramientas.php',
    'api/movimientos.php',
    
    // Carpeta assets
    'assets/css/styles.css',
    'assets/js/app.js',
    'Encargado/crear_admin.php',
    'Encargado/eliminar_admin.php',
];

$carpetasRequeridas = [
    'config',
    'api',
    'assets',
    'assets/css',
    'assets/js',
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Estructura</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            font-size: 32px;
        }
        
        h2 {
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .item {
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            font-family: monospace;
        }
        
        .ok {
            background: #d1fae5;
            color: #065f46;
        }
        
        .error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .icon {
            margin-right: 12px;
            font-weight: bold;
            font-size: 18px;
        }
        
        .summary {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #10b981;
            color: white;
        }
        
        .badge-error {
            background: #ef4444;
            color: white;
        }
        
        .instructions {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
        }
        
        .instructions h3 {
            color: #92400e;
            margin-bottom: 10px;
        }
        
        .instructions ul {
            margin-left: 20px;
            color: #78350f;
        }
        
        .instructions li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificación de Estructura del Proyecto</h1>
        
        <h2>📁 Carpetas</h2>
        <?php
        $carpetasOK = 0;
        $carpetasError = 0;
        
        foreach ($carpetasRequeridas as $carpeta) {
            $existe = is_dir($carpeta);
            if ($existe) {
                $carpetasOK++;
                echo "<div class='item ok'><span class='icon'>✓</span> $carpeta/</div>";
            } else {
                $carpetasError++;
                echo "<div class='item error'><span class='icon'>✗</span> $carpeta/ - <strong>NO EXISTE</strong></div>";
            }
        }
        ?>
        
        <h2>📄 Archivos</h2>
        <?php
        $archivosOK = 0;
        $archivosError = 0;
        
        foreach ($archivosRequeridos as $archivo) {
            $existe = file_exists($archivo);
            if ($existe) {
                $archivosOK++;
                echo "<div class='item ok'><span class='icon'>✓</span> $archivo</div>";
            } else {
                $archivosError++;
                echo "<div class='item error'><span class='icon'>✗</span> $archivo - <strong>NO EXISTE</strong></div>";
            }
        }
        ?>
        
        <div class="summary">
            <h2 style="margin-top: 0; border: none;">📊 Resumen</h2>
            <div class="summary-item">
                <span>Carpetas correctas:</span>
                <span class="badge <?php echo $carpetasError === 0 ? 'badge-success' : 'badge-error'; ?>">
                    <?php echo $carpetasOK; ?> / <?php echo count($carpetasRequeridas); ?>
                </span>
            </div>
            <div class="summary-item">
                <span>Archivos correctos:</span>
                <span class="badge <?php echo $archivosError === 0 ? 'badge-success' : 'badge-error'; ?>">
                    <?php echo $archivosOK; ?> / <?php echo count($archivosRequeridos); ?>
                </span>
            </div>
            <div class="summary-item">
                <span><strong>Estado General:</strong></span>
                <span class="badge <?php echo ($carpetasError === 0 && $archivosError === 0) ? 'badge-success' : 'badge-error'; ?>">
                    <?php echo ($carpetasError === 0 && $archivosError === 0) ? '✓ TODO OK' : '✗ FALTAN ARCHIVOS'; ?>
                </span>
            </div>
        </div>
        
        <?php if ($carpetasError > 0 || $archivosError > 0): ?>
        <div class="instructions">
            <h3>⚠️ Instrucciones para corregir:</h3>
            <ul>
                <li>Crea las carpetas faltantes en tu proyecto</li>
                <li>Copia los archivos que te proporcioné en las ubicaciones correctas</li>
                <li>Asegúrate de mantener la estructura de carpetas exactamente como se muestra</li>
                <li>Una vez completado, recarga esta página para verificar nuevamente</li>
            </ul>
        </div>
        <?php else: ?>
        <div class="instructions" style="background: #d1fae5; border-left-color: #10b981;">
            <h3 style="color: #065f46;">✓ ¡Estructura Correcta!</h3>
            <p style="color: #047857; margin-top: 10px;">
                Todos los archivos y carpetas están en su lugar. Ahora puedes:
            </p>
            <ul style="color: #047857;">
                <li>Importar el archivo <code>schema_con_usuarios.sql</code> en tu base de datos</li>
                <li>Configurar las credenciales en <code>config/db.php</code></li>
                <li>Acceder a <a href="login.php" style="color: #059669; font-weight: bold;">login.php</a> para comenzar</li>
            </ul>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center; color: #6b7280;">
            <small>Ruta del proyecto: <?php echo __DIR__; ?></small>
        </div>
    </div>
</body>
</html>