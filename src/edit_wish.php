<?php
// Función para editar un Funko en la lista de deseos
function editarWish(): array {
    // Inicia sesión si aún no está activa
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Incluye el archivo de conexión a la base de datos
    include 'conexiondb.php';

    // Verifica si el usuario ha iniciado sesión
    if (!isset($_SESSION['username_id'])) {
        return ['success' => false, 'message' => 'No autorizado'];
    }

    // Solo procesa la solicitud si es de tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_usuario = $_SESSION['username_id']; // ID del usuario actual desde la sesión
        $id = intval($_POST['id']); // ID del Funko a editar
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : ''; // Nuevo nombre
        $numero = isset($_POST['numero']) ? intval($_POST['numero']) : 0; // Nuevo número

        // Validación básica: nombre no vacío y número mayor a 0
        if (empty($nombre) || $numero <= 0) {
            return ['success' => false, 'message' => 'Datos inválidos'];
        }

        // Si se ha subido una nueva imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
            $imagen = file_get_contents($_FILES['imagen']['tmp_name']); // Contenido binario de la imagen
            $tipo = $_FILES['imagen']['type']; // Tipo MIME de la imagen

            // Prepara la consulta para actualizar nombre, número e imagen
            $stmt = $conn->prepare("UPDATE lista_deseos 
                                    SET nombrefunko = ?, numerofunko = ?, imagen = ?, tipo_imagen = ? 
                                    WHERE idlista_deseos = ? AND id_usuario = ?");
            $stmt->bind_param("sissii", $nombre, $numero, $imagen, $tipo, $id, $id_usuario);
        } else {
            // Si no se subió imagen, solo se actualiza nombre y número
            $stmt = $conn->prepare("UPDATE lista_deseos 
                                    SET nombrefunko = ?, numerofunko = ? 
                                    WHERE idlista_deseos = ? AND id_usuario = ?");
            $stmt->bind_param("siii", $nombre, $numero, $id, $id_usuario);
        }

        // Ejecuta la consulta preparada
        if ($stmt->execute()) {
            $nuevaImagen = null;

            // Si hubo nueva imagen, se prepara en base64 para actualizarla en el frontend dinámicamente
            if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
                $nuevaImagen = "data:{$tipo};base64," . base64_encode($imagen);
            }

            // Cierra recursos y devuelve éxito
            $stmt->close();
            $conn->close();
            return [
                'success' => true,
                'message' => 'Funko actualizado',
                'nuevaImagen' => $nuevaImagen
            ];
        } else {
            // Error al ejecutar la consulta
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Error al actualizar'];
        }
    }

    // Si no se recibió una solicitud POST, devuelve error
    return ['success' => false, 'message' => 'Método no permitido'];
}

