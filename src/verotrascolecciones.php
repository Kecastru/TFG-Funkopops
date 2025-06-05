<?php
function obtenerColeccionesOtrosUsuarios($usuarioActual, $conn) {
    // Consulta SQL para obtener las colecciones de todos los usuarios excepto el actual.
    // Se selecciona el id de la colección, el nombre de la colección y el nombre de usuario del dueño.
    $sql = "SELECT 
                c.idcolecciones, 
                c.nombre AS nombre_coleccion, 
                u.username AS nombre_usuario
            FROM colecciones c
            INNER JOIN usuarios u ON c.id_usuario = u.id
            WHERE u.id != ?";

    // Prepara la consulta para evitar inyección SQL
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Si falla la preparación, devuelve un error
        return ['error' => 'Error en la preparación de la consulta: ' . $conn->error];
    }

    // Vincula el parámetro usuarioActual a la consulta 
    $stmt->bind_param('i', $usuarioActual);

    // Ejecuta la consulta
    if (!$stmt->execute()) {
        // Si falla la ejecución, devuelve un  error
        return ['error' => 'Error al ejecutar la consulta: ' . $stmt->error];
    }

    // Obtiene el resultado de la consulta
    $result = $stmt->get_result();

    // Inicializa un array para almacenar las colecciones encontradas
    $colecciones = [];

    // Recorre cada fila del resultado y la añade al array
    while ($row = $result->fetch_assoc()) {
        $colecciones[] = $row;
    }

    // Cierra la sentencia preparada para liberar recursos
    $stmt->close();

    // Devuelve el array con las colecciones (o vacío si no encontró ninguna)
    return $colecciones;
}
