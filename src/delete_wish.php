<?php
// Función para eliminar un Funko de la lista de deseos
function eliminarWish(): array {
    // Inicia la sesión si aún no se ha iniciado
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Incluye el archivo de conexión a la base de datos
    include 'conexiondb.php';

    // Verifica que el usuario esté autenticado
    if (!isset($_SESSION['username_id'])) {
        // Si no lo está, devuelve una respuesta de error
        return ['success' => false, 'message' => 'No autorizado'];
    }

    // Verifica que la solicitud sea de tipo POST y que se haya enviado un ID válido
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        // Recupera el ID del usuario desde la sesión y el ID del deseo desde POST
        $id_usuario = $_SESSION['username_id'];
        $id_deseo = intval($_POST['id']);  // Convierte a entero por seguridad

        // Prepara una consulta SQL para eliminar el deseo de ese usuario
        $stmt = $conn->prepare("
            DELETE FROM lista_deseos 
            WHERE idlista_deseos = ? AND id_usuario = ?
        ");

        // Enlaza los parámetros a la consulta preparada
        $stmt->bind_param("ii", $id_deseo, $id_usuario);

        // Ejecuta la consulta y devuelve el resultado correspondiente
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Funko eliminado correctamente'];
        } else {
            // En caso de error al ejecutar
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Error al eliminar'];
        }
    }

    // Si la solicitud no es válida o no contiene ID, devuelve error
    return ['success' => false, 'message' => 'Solicitud inválida'];
}

