<?php
require_once __DIR__ . '/../config/Auth.php';

header('Content-Type: application/json');

Auth::logout();

echo json_encode([
    'success' => true,
    'message' => 'Sesión cerrada exitosamente'
]);
?>