<?php
//Función para cerrar sesión
function cerrarSesion() {
    // Destruye la sesión
    $_SESSION = [];
    
    session_destroy();

    // Devuelve JSON correcto y termina ejecución
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}



