<?php
// Función que añade un Funko a la base de datos
function añadirFunko() {
    // Inicia la sesión si aún no ha sido iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica si el usuario está autenticado
    if (!isset($_SESSION['username_id'])) {
        // Retorna un error si no está autenticado
        return ['error' => 'No autenticado'];
    }

    // Incluye el archivo de conexión a la base de datos
    include 'conexiondb.php';

    // Solo permite peticiones POST para evitar llamadas no válidas
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['error' => 'Método no permitido'];
    }

    // Obtiene los datos enviados por POST
    $idColeccion = intval($_POST['idcoleccion'] ?? 0); // Convierte a entero para mayor seguridad
    $nombre = $_POST['nombrefunko'] ?? '';             // Nombre del Funko
    $numero = $_POST['numerofunko'] ?? '';             // Número del Funko

    // Valida que nombre y número no estén vacíos
    if (empty($nombre) || empty($numero)) {
        return [
            'error' => 'Nombre y número son obligatorios',
            'mensaje' => 'Nombre y número son obligatorios',
            'id' => $idColeccion
        ];
    }

    // Verifica si se ha subido una imagen y no hay error
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        return [
            'error' => 'Error al subir la imagen',
            'mensaje' => 'Error al subir la imagen',
            'id' => $idColeccion
        ];
    }

    // Obtiene el contenido del archivo y su tipo MIME
    $imagen = file_get_contents($_FILES['imagen']['tmp_name']); // Imagen en binario
    $tipoImagen = $_FILES['imagen']['type'];                    // Tipo de archivo

    // Validación del tipo de imagen permitido
    $tipos_validos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($tipoImagen, $tipos_validos)) {
        return [
            'error' => 'Tipo de imagen no válido',
            'mensaje' => 'Tipo de imagen no válido. Solo JPEG, PNG o GIF.',
            'id' => $idColeccion
        ];
    }

    // Prepara la consulta SQL para insertar el nuevo Funko
    $stmt = $conn->prepare("
        INSERT INTO funkopop (nombrefunko, numerofunko, tipo_imagen, imagen, idcoleccion)
        VALUES (?, ?, ?, ?, ?)
    ");

    // Verifica si la preparación falló
    if (!$stmt) {
        return [
            'error' => 'Error en la preparación de la consulta',
            'mensaje' => 'Error en la preparación de la consulta: ' . $conn->error,
            'id' => $idColeccion
        ];
    }

    // Asocia los parámetros a la consulta preparada
    $stmt->bind_param("sissi", $nombre, $numero, $tipoImagen, $imagen, $idColeccion);

    // Ejecuta la consulta y verifica el resultado
    if ($stmt->execute()) {
        // Éxito: retorna un array con éxito y el ID de la colección
        return ['success' => true, 'id' => $idColeccion];
    } else {
        // Error al ejecutar la consulta
        return [
            'error' => 'Error al añadir Funko',
            'mensaje' => 'Error al añadir Funko: ' . $stmt->error,
            'id' => $idColeccion
        ];
    }
}

