<?php
// Inicia la sesión si no está iniciada para poder acceder a variables de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluye la conexión a la base de datos para poder hacer consultas
include 'conexiondb.php';

// Configura el encabezado HTTP para que la respuesta sea en formato JSON
header('Content-Type: application/json');

// Verifica que el usuario esté autenticado (exista el id en sesión)
if (!isset($_SESSION['username_id'])) {
    // Si no está autenticado, responde con código 403 Forbidden y mensaje JSON
    http_response_code(403);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit; // Termina la ejecución para no seguir procesando
}

// Obtiene el id del usuario desde la sesión para usarlo en consultas
$user_id = $_SESSION['username_id'];

// Función para responder con JSON y código HTTP, luego terminar ejecución
function respond($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

// Función para obtener las colecciones del usuario actual
function obtenerColecciones() {
    // Verifica de nuevo la autenticación 
    if (!isset($_SESSION['username_id'])) {
        respond(['error' => 'Usuario no autenticado'], 403);
    }

    // Incluye conexión a base de datos 
    include 'conexiondb.php';

    // Obtiene el id de usuario actual desde sesión
    $user_id = $_SESSION['username_id'];

    // Prepara la consulta para obtener las colecciones del usuario
    $stmt = $conn->prepare("SELECT idcolecciones, nombre FROM colecciones WHERE id_usuario = ?");
    if (!$stmt) {
        // Si hay error preparando la consulta, responde con error 500
        respond(['error' => "Error en la consulta: " . $conn->error], 500);
    }

    // Vincula el parámetro id_usuario en la consulta
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Obtiene los resultados de la consulta
    $result = $stmt->get_result();
    $colecciones = [];

    // Recorre cada fila y la añade al array de colecciones
    while ($row = $result->fetch_assoc()) {
        $colecciones[] = $row;
    }

    // Cierra la consulta y conexión a la base de datos
    $stmt->close();
    $conn->close();

    // Responde con JSON que incluye el array de colecciones
    respond(['colecciones' => $colecciones]);
}

// Función para crear una nueva colección
function crearColeccion() {
    // Verifica autenticación
    if (!isset($_SESSION['username_id'])) {
        respond(['error' => 'Usuario no autenticado'], 403);
    }

    // Incluye conexión a base de datos
    include 'conexiondb.php';

    // Obtiene id del usuario y nombre de la colección desde POST 
    $user_id = $_SESSION['username_id'];
    $nombre = trim($_POST['nombre_coleccion'] ?? ''); //trim para limpiar espacios

    // Valida que el nombre no esté vacío
    if ($nombre === '') {
        respond(['error' => 'El nombre de la colección no puede estar vacío'], 400);
    }

    // Prepara la consulta para insertar nueva colección
    $stmt = $conn->prepare("INSERT INTO colecciones (nombre, id_usuario) VALUES (?, ?)");
    if (!$stmt) {
        respond(['error' => "Error en la consulta: " . $conn->error], 500);
    }

    // Vincula parámetros nombre y id_usuario
    $stmt->bind_param("si", $nombre, $user_id);

    // Ejecuta la consulta y verifica si fue exitosa
    if ($stmt->execute()) {
        respond(['success' => true, 'message' => "Colección añadida correctamente"]);
    } else {
        respond(['error' => "No se pudo añadir la colección: " . $stmt->error], 500);
    }

    // Cierra la consulta y la conexión a la base de datos
    $stmt->close();
    $conn->close();
}

