<?php
session_start(); // Inicia la sesión

// Importación de archivos con funciones o controladores
require_once __DIR__ . '/../src/delete_wish.php';
require_once __DIR__ . '/../src/addwish.php';
require_once __DIR__ . '/../src/verotrascolecciones.php';
require_once __DIR__ . '/../src/edit_funko.php';
require_once __DIR__ . '/../src/eliminar_funko.php';
require_once __DIR__ . '/../src/addfunko.php';
require_once __DIR__ . '/../src/Tucollection.php';
require_once __DIR__ . '/../src/logout.php';

// Obtiene la acción desde la URL (GET) y el método de la petición (GET o POST)
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';


//  Cierra sesión 

if ($action === 'logout') {
    // Limpia la sesión
    $_SESSION = [];
    // Destruye la sesión y redirige al inicio
    session_destroy();
    header('Location: /index.php');
    exit;
}


 // Operaciones con colecciones
 
// Obtiene colecciones del usuario (GET)
if ($method === 'GET' && $action === 'colecciones') {
    obtenerColecciones();
    exit;
}

// Crea una nueva colección (POST)
elseif ($method === 'POST' && $action === 'colecciones') {
    crearColeccion();
    exit;
}


 // Añade un Funko a una colección
 
if ($method === 'POST' && $action === 'addfunko') {
    $resultado = añadirFunko();

    // Almacena mensaje en la sesión
    if (isset($resultado['success'])) {
        $_SESSION['registro_mensaje'] = 'Funko añadido correctamente';
    } else {
        $_SESSION['registro_mensaje'] = $resultado['mensaje'] ?? 'Error desconocido';
    }

    // Redirige a la colección correspondiente
    $idColeccion = $resultado['id'] ?? 0;
    header("Location: ver_collection.php?id=" . intval($idColeccion));
    exit;
}


 // Elimina un Funko de una colección
 
if ($method === 'POST' && $action === 'delfunko') {
    header('Content-Type: application/json; charset=utf-8');
    $resultado = eliminarFunko();
    echo json_encode($resultado);
    exit;
}


 // Edita un Funko
 
if ($method === 'POST' && $action === 'editfunko') {
    header('Content-Type: application/json; charset=utf-8');
    $resultado = editarFunko();
    echo json_encode($resultado);
    exit;
}


 // Obtiene colecciones de otros usuarios
 
if ($method === 'GET' && $action === 'obtener_colecciones') {
    include __DIR__ . '/../src/conexiondb.php';

    // Verifica autenticación del usuario
    if (!isset($_SESSION['username_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $usuarioActual = intval($_SESSION['username_id']);
    $colecciones = obtenerColeccionesOtrosUsuarios($usuarioActual, $conn);

    header('Content-Type: application/json; charset=utf-8');

    if (isset($colecciones['error'])) {
        echo json_encode(['error' => $colecciones['error']]);
    } else {
        echo json_encode(['colecciones' => $colecciones]);
    }

    $conn->close();
    exit;
}


 // Añade Funkos a la lista de deseos

if ($method === 'POST' && $action === 'addwish') {
    $resultado = añadirWish();

    // Mensaje de éxito o error
    if (isset($resultado['success'])) {
        $_SESSION['registro_mensaje'] = 'Funko añadido correctamente';
    } else {
        $_SESSION['registro_mensaje'] = $resultado['mensaje'] ?? 'Error desconocido';
    }

    
}


 //Edita un Funko de la lista de deseos
 
if ($method === 'POST' && $action === 'editwish') {
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../src/edit_wish.php';
    $resultado = editarWish();
    echo json_encode($resultado);
    exit;
}


 //Elimina funkos de la lista de deseos
 
if ($method === 'POST' && $action === 'delwish') {
    header('Content-Type: application/json; charset=utf-8');
    $resultado = eliminarWish();
    echo json_encode($resultado);
    exit;
}


 // Acción no válida
 http_response_code(400); // Código de error 400: Bad Request
echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
exit;
