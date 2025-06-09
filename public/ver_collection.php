<?php
// Inicia sesión para mantener los datos del usuario
session_start();

// Captura un mensaje y lo elimina para que no se repita
$mensajeRegistro = '';
if (isset($_SESSION['registro_mensaje'])) {
    $mensajeRegistro = $_SESSION['registro_mensaje'];
    unset($_SESSION['registro_mensaje']); // Elimina el mensaje tras capturarlo
}

// Conecta a la base de datos
include '../src/conexiondb.php';

// Redirige al login si el usuario no ha iniciado sesión
if (!isset($_SESSION['username_id'])) {
    header('Location: index.php');
    exit;
}

// Recupera el ID del usuario autenticado desde la sesión
$user_id = $_SESSION['username_id'];

// Recupera el ID de la colección desde la URL
$idColeccion = $_GET['id'] ?? null;

// Si no se proporciona un ID de colección, termina el script
if (!$idColeccion) {
    die('ID de colección no especificado.');
}

// Consulta para obtener los datos de la colección (nombre y usuario propietario)
$stmtCol = $conn->prepare("SELECT nombre, id_usuario FROM colecciones WHERE idcolecciones = ?");
$stmtCol->bind_param("i", $idColeccion);
$stmtCol->execute();
$resultCol = $stmtCol->get_result();

// Si no se encuentra la colección, muestra un error
if ($resultCol->num_rows === 0) {
    die('Colección no encontrada.');
}

// Guarda los datos de la colección
$coleccion = $resultCol->fetch_assoc();

// Consulta para obtener los Funkos de la colección
$stmtFunkos = $conn->prepare("SELECT idfunkopop, nombrefunko, numerofunko, tipo_imagen, imagen FROM funkopop WHERE idcoleccion = ?");
$stmtFunkos->bind_param("i", $idColeccion);
$stmtFunkos->execute();
$resultFunkos = $stmtFunkos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Colección <?= htmlspecialchars($coleccion['nombre']) ?> </title>
    <!-- Archivos CSS -->
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/styles_img.css">
    <link rel="stylesheet" href="styles/styles_tabla.css">
</head>
<body>
<header>
    <div class="banner">
        <!-- Logo de la página -->
        <div class="logo">
            <img src="images/logo.png" alt="logo">
        </div>
        <!-- Botones de navegación -->
        <div class="btnmenu">
            <button id="volvermenu">Volver al menú</button>
            <button id="otrascolecciones">Otras Colecciones</button>
            <button id="listadeseos">Lista de deseos</button>
            <button id="logoutBtn">Cerrar sesión</button>
        </div>
    </div>
</header>

<!-- Muestra alerta si hay mensaje de registro -->
<?php if ($mensajeRegistro): ?>
<script>
    alert("<?= addslashes($mensajeRegistro) ?>");
</script>
<?php endif; ?>

<!-- Nombre de la colección -->
<h2>Colección: <?= htmlspecialchars($coleccion['nombre']) ?></h2>

<!-- Botón para añadir Funkos solo si el usuario es dueño de la colección -->
<div id="contenedor-principal">
    <?php if ($user_id == $coleccion['id_usuario']): ?>
        <button class="ir_registro" onclick="window.location.href='registrofunko.php?id=<?= $idColeccion ?>'">
            Añadir Funkopop
        </button>
    <?php endif; ?>
</div>

<!-- Mensaje que se mostrará al eliminar un Funko -->
<div id="mensaje-eliminado" style="display:none;" class="alerta-mensaje"></div>

<?php if ($resultFunkos->num_rows === 0): ?>
    <!-- Mensaje si no hay Funkos en la colección -->
    <h2>No hay Funkos en esta colección.</h2>
<?php else: ?>
    <!-- Tabla con los Funkos de la colección -->
    <div class="tabla-funko">
        <table border="1">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Número</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($funko = $resultFunkos->fetch_assoc()): 
                    // Codifica la imagen en base64 para mostrarla directamente
                    $imgSrc = "data:{$funko['tipo_imagen']};base64," . base64_encode($funko['imagen']);
                ?>
                <tr data-funko="<?= $funko['idfunkopop'] ?>">
                    <!-- Campo nombre -->
                    <td>
                        <span class="nombre-view"><?= htmlspecialchars($funko['nombrefunko']) ?></span>
                        <input type="text" class="nombre-edit" value="<?= htmlspecialchars($funko['nombrefunko']) ?>" style="display:none;">
                    </td>
                    <!-- Campo número -->
                    <td>
                        <span class="numero-view"><?= htmlspecialchars($funko['numerofunko']) ?></span>
                        <input type="number" class="numero-edit" value="<?= htmlspecialchars($funko['numerofunko']) ?>" style="display:none;">
                    </td>
                    <!-- Campo imagen -->
                    <td>
                        <img class="imagen-view" src="<?= $imgSrc ?>" alt="Funko" width="50">
                        <input type="file" class="imagen-edit" style="display:none;">
                    </td>
                    <!-- Acciones -->
                    <td>
                        <?php if ($user_id == $coleccion['id_usuario']): ?>
                            <div class="botones-acciones">
                                <button class="btn-editar-funko" data-id="<?= $funko['idfunkopop'] ?>" data-coleccion="<?= $idColeccion ?>">Editar</button>
                                <button class="btn-eliminar-funko" data-id="<?= $funko['idfunkopop'] ?>" data-coleccion="<?= $idColeccion ?>">Eliminar</button>
                                <button class="btn-guardar-funko" data-id="<?= $funko['idfunkopop'] ?>" style="display:none;">Guardar</button>
                                <button class="btn-cancelar-funko" data-id="<?= $funko['idfunkopop'] ?>" style="display:none;">Cancelar</button>
                            </div>
                        <?php else: ?>
                            <em>Sin permisos</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Modal para mostrar imagen en grande al hacer clic -->
<div id="imagenModal">
    <span id="cerrarModal">&times;</span>
    <img id="imagenAmpliada" src="" alt="Funko ampliado">
</div>

<!-- Scripts -->
<script src="js/scriptbtn.js"></script>
<script src="js/scriptcollection.js"></script>
<script src="js/eliminar_funko.js"></script> 
<script src="js/edit_funko.js"></script> 

</body>
</html>
