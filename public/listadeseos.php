<?php
session_start(); // Inicia la sesión para acceder a las variables de sesión

// Incluye el archivo que contiene la conexión a la base de datos
include '../src/conexiondb.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['username_id'])) {
    // Si no está autenticado, guarda un mensaje en la sesión y redirige a la página de login
    $_SESSION['mensaje'] = "Inicia sesión para ver la lista de deseos";
    header("Location: login.php");
    exit;
}

// Si el usuario está autenticado, obtiene su ID de la sesión
$id_usuario = $_SESSION['username_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Deseos</title>
    <!-- Enlaces a los estilos CSS -->
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/styles_img.css">
    <link rel="stylesheet" href="styles/styles_tabla.css">
</head>
<body>
<header>
    <div class="banner">
        <!-- Logo de la aplicación -->
        <div class="logo">
            <img src="images/logo.png" alt="logo">
        </div>
        <br><br>
        <!-- Menú de navegación -->
        <div class="btnmenu">
            <button id="volvermenu">Volver al menú</button>
            <button id="otrascolecciones">Otras Colecciones</button>
            <button id="listadeseos">Lista de deseos</button>
            <button id="logoutBtn">Cerrar sesión</button>
        </div>
    </div>
</header>

<h2>Agregar Funko a la Lista de Deseos</h2>
<div id="contenedor-principal">
    <!-- Formulario para añadir un nuevo Funko a la lista -->
    <form class="addfunko" action="/api.php?action=addwish" method="POST" enctype="multipart/form-data">
        <label for="nombrefunko">Nombre del Funko:</label>
        <input type="text" name="nombrefunko" id="nombrefunko" required><br><br>

        <label for="numerofunko">Número del Funko:</label>
        <input type="number" name="numerofunko" id="numerofunko" required><br><br>

        <label for="imagen">Imagen:</label>
        <input type="file" name="imagen" id="imagen" accept="image/*" required><br><br>

        <button type="submit">Añadir a Lista de Deseos</button>
    </form>
</div>

<h2>Lista de Deseos</h2>
<div class="tabla-funko">
    <!-- Tabla que muestra todos los Funkos deseados -->
    <table>
        <tr>
            <th>Nombre</th>
            <th>Número</th>
            <th>Imagen</th>
            <th> Acciones </th>
        </tr>
        <?php
        // Consulta preparada para obtener todos los Funkos deseados del usuario actual
        $stmt = $conn->prepare("SELECT idlista_deseos, nombrefunko, numerofunko, imagen, tipo_imagen FROM lista_deseos WHERE id_usuario = ? ORDER BY idlista_deseos DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        // Itera por cada registro y lo muestra en una fila de la tabla
        while ($row = $result->fetch_assoc()):
            $imgData = base64_encode($row['imagen']); // Codifica la imagen en base64
            $src = "data:{$row['tipo_imagen']};base64,{$imgData}"; // Crea una URL de datos para mostrar la imagen
        ?>
        <tr data-wish="<?= $row['idlista_deseos'] ?>">
            <!-- Celda de nombre con vista y modo edición oculto -->
            <td>
                <span class="nombre-view"><?= htmlspecialchars($row['nombrefunko']) ?></span>
                <input class="nombre-edit" type="text" value="<?= htmlspecialchars($row['nombrefunko']) ?>" style="display:none;">
            </td>
            <!-- Celda de número con vista y edición -->
            <td>
                <span class="numero-view"><?= htmlspecialchars($row['numerofunko']) ?></span>
                <input class="numero-edit" type="number" value="<?= htmlspecialchars($row['numerofunko']) ?>" style="display:none;">
            </td>
            <!-- Celda de imagen con vista y edición -->
            <td>
                <img class="imagen-view" src="<?= $src ?>" width="100" />
                <input class="imagen-edit" type="file" accept="image/*" style="display:none;">
            </td>
            <!-- Botones de acciones -->
            <td>
                <button class="btn-editar-deseo" data-id="<?= $row['idlista_deseos'] ?>">Editar</button>
                <button class="btn-guardar-deseo" data-id="<?= $row['idlista_deseos'] ?>" style="display:none;">Guardar</button>
                <button class="btn-cancelar-deseo" style="display:none;">Cancelar</button>
                <button class="btn-eliminar-deseo" data-id="<?= $row['idlista_deseos'] ?>">Eliminar</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Scripts -->
<script src="js/scriptbtn.js"></script> <!-- Controla los botones de navegación -->
<script src="js/eliminar_funko.js"></script> <!-- Maneja la eliminación de deseos -->
<script src="js/edit_funko.js"></script> <!-- Maneja la edición y guardado de deseos -->

<!-- Modal para ver imagen en tamaño ampliado -->
<div id="imagenModal">
    <!-- Botón para cerrar el modal -->
    <span id="cerrarModal">&times;</span>
    <!-- Imagen ampliada que se muestra en el modal -->
    <img id="imagenAmpliada" src="" alt="Funko ampliado">
</div>
</body>
</html>
