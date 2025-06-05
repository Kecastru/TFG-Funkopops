<?php
// Función para editar un Funko existente en una colección
function editarFunko() {
    // Inicia la sesión si aún no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Incluye la conexión a la base de datos
    include 'conexiondb.php';

    // Verifica que el usuario haya iniciado sesión
    if (!isset($_SESSION['username_id'])) {
        return ['success' => false, 'mensaje' => 'No autorizado'];
    }

    // Solo se permite el método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['success' => false, 'mensaje' => 'Método no permitido'];
    }

    // Obtiene los datos del formulario con validación y limpieza básica
    $idFunko = intval($_POST['id'] ?? 0);
    $nuevoNombre = trim($_POST['nombre'] ?? '');
    $nuevoNumero = intval($_POST['numero'] ?? 0);
    $idUsuario = $_SESSION['username_id'];

    // Valida que los campos no estén vacíos o incorrectos
    if ($idFunko <= 0 || $nuevoNombre === '' || $nuevoNumero <= 0) {
        return ['success' => false, 'mensaje' => 'Datos incompletos o inválidos'];
    }

    // Verifica que el Funko pertenece al usuario actual (evita editar Funkos de otros usuarios)
    $check = $conn->prepare("SELECT f.idfunkopop FROM funkopop f 
                             JOIN colecciones c ON f.idcoleccion = c.idcolecciones 
                             WHERE f.idfunkopop = ? AND c.id_usuario = ?");
    $check->bind_param("ii", $idFunko, $idUsuario);
    $check->execute();
    $checkResult = $check->get_result();

    // Si no pertenece, se deniega la edición
    if ($checkResult->num_rows === 0) {
        return ['success' => false, 'mensaje' => 'No tienes permiso para editar este Funko'];
    }

    // Si se envía una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $tipoImagen = $_FILES['imagen']['type'];
        $imagenBinaria = file_get_contents($_FILES['imagen']['tmp_name']);

        // Actualiza nombre, número e imagen
        $stmt = $conn->prepare("UPDATE funkopop SET nombrefunko = ?, numerofunko = ?, tipo_imagen = ?, imagen = ? WHERE idfunkopop = ?");
        $stmt->bind_param("sissi", $nuevoNombre, $nuevoNumero, $tipoImagen, $imagenBinaria, $idFunko);
    } else {
        // Si no se envió imagen, actualiza solo nombre y número
        $stmt = $conn->prepare("UPDATE funkopop SET nombrefunko = ?, numerofunko = ? WHERE idfunkopop = ?");
        $stmt->bind_param("sii", $nuevoNombre, $nuevoNumero, $idFunko);
    }

    // Ejecuta la consulta de actualización
    if ($stmt->execute()) {
        // Si se actualizó con imagen, prepara el base64 para mostrar en frontend sin recargar
        if (isset($imagenBinaria)) {
            $nuevaImagen = "data:$tipoImagen;base64," . base64_encode($imagenBinaria);
            $stmt->close();
            $conn->close();
            return ['success' => true, 'id' => $idFunko, 'nuevaImagen' => $nuevaImagen];
        } else {
            // Actualización sin imagen
            $stmt->close();
            $conn->close();
            return ['success' => true, 'id' => $idFunko];
        }
    } else {
        // Fallo en la actualización
        $stmt->close();
        $conn->close();
        return ['success' => false, 'mensaje' => 'Error al actualizar el Funko'];
    }
}



