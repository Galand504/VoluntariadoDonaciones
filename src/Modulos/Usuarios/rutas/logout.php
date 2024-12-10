<?php
session_start();
session_destroy();
echo json_encode([
    'status' => 'OK',
    'message' => 'Sesión cerrada correctamente'
]);
header('Location: http://localhost:3000/public/index.php');
exit;
?>