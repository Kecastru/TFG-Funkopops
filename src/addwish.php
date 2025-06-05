<?php
// Función para añadir un Funko a la lista de deseos del usuario
function añadirWish() {
    // Inicia la sesión si aún no ha sido iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Incluye la conexión a la base de datos
    include 'conexiondb.php';

    // Verifica si el usuario ha iniciado sesión
    if (!isset($_SESSION['username_id'])) {
        // Si no ha iniciado sesión, guarda un mensaje y redirige al login
        $_SESSION['mensaje'] = "Debe iniciar sesión.";
        header("Location: ../public/login.php");
        exit;
    }

    // Recupera el ID del usuario desde la sesión
    $id_usuario = $_SESSION['username_id'];

    // Verifica que la solicitud sea POST 
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['mensaje'] = "Método no permitido.";
        header("Location: ../public/listadeseos.php");
        exit;
    }

    // Obtiene y limpia los valores del formulario (nombre y número del Funko)
    $nombrefunko = trim($_POST['nombrefunko'] ?? '');
    $numerofunko = trim($_POST['numerofunko'] ?? '');

    // Verifica que los campos no estén vacíos
    if (empty($nombrefunko) || empty($numerofunko)) {
        $_SESSION['mensaje'] = "Nombre y número del Funko son obligatorios.";
        header("Location: ../public/listadeseos.php");
        exit;
    }

    // Verifica que se haya subido una imagen correctamente
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['mensaje'] = "Error al subir la imagen.";
        header("Location: ../public/listadeseos.php");
        exit;
    }

    // Obtiene los datos de la imagen subida
    $imagen_temp = $_FILES['imagen']['tmp_name'];        // Ruta temporal del archivo
    $tipo_imagen = $_FILES['imagen']['type'];            // Tipo MIME (image/jpeg, etc.)
    $imagen_datos = file_get_contents($imagen_temp);     // Convierte la imagen a binario

    // Valida que el tipo de imagen sea uno de los aceptados
    $tipos_validos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($tipo_imagen, $tipos_validos)) {
        $_SESSION['mensaje'] = "Tipo de imagen no válido. Solo se aceptan JPEG, PNG o GIF.";
        header("Location: ../public/listadeseos.php");
        exit;
    }

    // Prepara la sentencia SQL para insertar el Funko en la base de datos
    $stmt = $conn->prepare("
        INSERT INTO lista_deseos (nombrefunko, numerofunko, imagen, tipo_imagen, id_usuario)
        VALUES (?, ?, ?, ?, ?)
    ");

    // Si falla la preparación, guarda mensaje de error y redirige
    if (!$stmt) {
        $_SESSION['mensaje'] = "Error al preparar la consulta: " . $conn->error;
        header("Location: ../public/listadeseos.php");
        exit;
    }

    // Asocia los valores a los parámetros de la consulta preparada
    $stmt->bind_param("ssssi", $nombrefunko, $numerofunko, $imagen_datos, $tipo_imagen, $id_usuario);

    // Ejecuta la consulta y guarda un mensaje en la sesión dependiendo del resultado
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Funko añadido a la lista de deseos correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al insertar en la base de datos: " . $stmt->error;
    }

    // Cierra la consulta y la conexión con la base de datos
    $stmt->close();
    $conn->close();

    // Redirige nuevamente a la lista de deseos
    header("Location: /listadeseos.php");
    exit;
}







