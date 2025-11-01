<?php
// server.php

$host = '0.0.0.0'; // Escucha en todas las interfaces de red
$port = 8080;      // Puerto personalizado

$docRoot = __DIR__; // Carpeta actual como raíz del proyecto

// Comando para iniciar el servidor embebido de PHP
$cmd = sprintf(
    'php -S %s:%d -t %s',
    $host,
    $port,
    escapeshellarg($docRoot)
);

echo "Servidor iniciado en http://$host:$port\n";
echo "Presiona Ctrl+C para detenerlo.\n";

// Ejecuta el servidor
passthru($cmd);