<?php
//Función de eliminar funko de una colección
function eliminarFunko(): array {
    // Inicia sesión si aún no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica que el usuario esté autenticado, si no, devuelve error
    if (!isset($_SESSION['username_id'])) {
        return ['success' => false, 'mensaje' => 'No autenticado'];
    }

    // Verifica que el método HTTP sea POST, si no, devuelve error
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['success' => false, 'mensaje' => 'Método no permitido'];
    }

    // Verifica que se reciba un parámetro 'id' válido en POST 
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        return ['success' => false, 'mensaje' => 'ID no válido'];
    }

    // Incluye la conexión a la base de datos
    include 'conexiondb.php';

    // Convierte el id recibido a entero para mayor seguridad
    $idFunko = intval($_POST['id']);

    // Consulta para obtener el id de la colección a la que pertenece el Funko
    $stmt = $conn->prepare("SELECT idcoleccion FROM funkopop WHERE idfunkopop = ?");
    $stmt->bind_param("i", $idFunko);
    $stmt->execute();
    $result = $stmt->get_result();
    $funko = $result->fetch_assoc(); // Obtiene el registro encontrado
    $stmt->close();

    // Si no se encontró el Funko, devuelve error
    if (!$funko) {
        return ['success' => false, 'mensaje' => 'Funko no encontrado'];
    }

    // Guarda el id de la colección para devolverlo después
    $idColeccion = $funko['idcoleccion'];

    // Prepara la eliminación del Funko por su id
    $stmt = $conn->prepare("DELETE FROM funkopop WHERE idfunkopop = ?");
    $stmt->bind_param("i", $idFunko);
    $stmt->execute();

    // Verifica si la eliminación afectó alguna fila
    if ($stmt->affected_rows > 0) {
        // Éxito: devuelve true y el id de la colección 
        return ['success' => true, 'mensaje' => 'Funko eliminado correctamente', 'id' => $idColeccion];
    } else {
        // No se eliminó ningún registro 
        return ['success' => false, 'mensaje' => 'No se pudo eliminar'];
    }
}











