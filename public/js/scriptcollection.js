// Ejecuta cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    const mostrarColeccionesBtn = document.getElementById('mostrarColeccionesBtn');

    // Si el botón existe, se le añade el evento para cargar las colecciones al hacer clic
    if (mostrarColeccionesBtn) {
        mostrarColeccionesBtn.addEventListener('click', cargarSeccionCrearColeccion);
    }
});

// Función auxiliar para obtener un elemento por su ID y mostrar error si no existe
const getElem = (id) => {
    const el = document.getElementById(id);
    if (!el) console.error(`No se encontró el elemento con ID "${id}".`);
    return el;
};

// Limpia el contenido del contenedor principal
const limpiarContenedor = () => {
    const cont = getElem('contenedor-principal');
    if (cont) cont.innerHTML = ''; 
    return cont;
};

// Carga la sección donde se muestran y gestionan las colecciones
function cargarSeccionCrearColeccion() {
    const cont = limpiarContenedor(); 
    if (!cont) return;

    // Añade un título a la sección
    cont.appendChild(document.createElement('h1')).textContent = 'Tus colecciones Funko Pops';

    // Crea el div que contendrá los botones de las colecciones
    const contenedorColecciones = document.createElement('div');
    contenedorColecciones.id = 'contenedor-botones-colecciones';
    cont.appendChild(contenedorColecciones);

    // Carga las colecciones ya existentes desde el backend
    cargarColeccionesExistentes(() => {
        // Botón para crear una nueva colección
        const btnCrear = document.createElement('button');
        btnCrear.id = 'crear-coleccion-btn';
        btnCrear.textContent = 'Crear nueva colección';
        btnCrear.onclick = mostrarFormularioNuevaColeccion;
        btnCrear.className = 'coleccion-boton crear-nueva-coleccion-boton';
        contenedorColecciones.appendChild(btnCrear);
    });
}

// Muestra el formulario para crear una nueva colección
function mostrarFormularioNuevaColeccion() {
    const cont = getElem('contenedor-principal');
    if (!cont) return;

    // Inserta el formulario HTML dentro del contenedor
    cont.insertAdjacentHTML('beforeend', `
        <div id="form-nueva-coleccion">
            <h3>Añadir nueva colección</h3>
            <form id="nuevaColeccionForm">
                <label for="nombre_coleccion">Nombre de la colección:</label>
                <input type="text" id="nombre_coleccion" name="nombre_coleccion" required>
                <button type="submit">Añadir Colección</button>
            </form>
            <div id="mensaje-coleccion"></div>
        </div>
    `);

    // Agrega manejador para enviar el formulario sin recargar la página
    document.getElementById('nuevaColeccionForm').onsubmit = (e) => {
        e.preventDefault(); // Previene el envío por defecto
        agregarNuevaColeccion(); // Llama a la función para procesar la creación
    };
}

// Función para manejar la respuesta JSON y lanza un error si falla
const manejarRespuestaJson = (response) => {
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
};

// Carga las colecciones existentes desde el backend
function cargarColeccionesExistentes(callback) {
    fetch('/api.php?action=colecciones')
        .then(manejarRespuestaJson)
        .then(data => {
            console.log('Respuesta de colecciones:', data);
            const contenedor = getElem('contenedor-botones-colecciones');
            if (!contenedor) return;

            contenedor.innerHTML = ''; // Limpia el contenido anterior

            // Si hay error en la respuesta
            if (data.error) {
                contenedor.innerHTML = `<p>${data.error}</p>`;
            }
            // Si hay colecciones, se muestran como botones
            else if (data.colecciones && data.colecciones.length > 0) {
                data.colecciones.forEach(c => {
                    const btn = document.createElement('button');
                    btn.className = 'coleccion-boton';
                    btn.textContent = c.nombre;
                    btn.onclick = () => {
                        // Redirige al usuario a la página de la colección
                        window.location.href = `ver_collection.php?id=${c.idcolecciones}`;
                    };
                    contenedor.appendChild(btn);
                });
            }

            // Si se pasó un callback, se ejecuta
            if (callback) callback();

            // Si no hay colecciones, muestra mensaje
            if (!data.colecciones || data.colecciones.length === 0) {
                const mensaje = document.createElement('p');
                mensaje.textContent = 'Aún no has creado ninguna colección.';
                mensaje.className = 'mensaje-sin-colecciones';
                contenedor.appendChild(mensaje);
            }
        })
        .catch(err => {
            // Manejo de errores 
            console.error('Error al cargar las colecciones:', err);
            const contenedor = getElem('contenedor-botones-colecciones');
            if (contenedor) contenedor.innerHTML = '<p>Error al cargar tus colecciones.</p>';
            if (callback) callback();
        });
}

// Envía el nuevo nombre de colección al servidor y actualiza la vista
function agregarNuevaColeccion() {
    const nombreInput = document.getElementById('nombre_coleccion');
    const mensajeDiv = document.getElementById('mensaje-coleccion');

    const formData = new FormData();
    formData.append('nombre_coleccion', nombreInput.value);

    fetch('/api.php?action=colecciones', {
        method: 'POST',
        body: formData
    })
    .then(manejarRespuestaJson)
    .then(data => {
        if (data.success) {
            mensajeDiv.textContent = data.message;
            mensajeDiv.style.color = 'green';
            cargarSeccionCrearColeccion(); // Recarga las colecciones
        } else {
            mensajeDiv.textContent = data.error || 'Error al crear colección';
            mensajeDiv.style.color = 'red';
        }
    })
    .catch(error => {
        mensajeDiv.textContent = 'Error al conectar con el servidor';
        mensajeDiv.style.color = 'red';
        console.error('Error:', error);
    });
}


// Elimina el formulario del DOM
function eliminarFormulario() {
    const formulario = document.getElementById('form-nueva-coleccion');
    if (formulario) formulario.remove();
}

// Crea el botón "Crear nueva colección" si no existe
function crearBotonNuevaColeccion() {
    const contenedor = document.getElementById('contenedor-botones-colecciones');

    // Solo lo crea si el botón no existe ya
    if (contenedor && !document.getElementById('crear-coleccion-btn')) {
        const boton = document.createElement('button');
        boton.id = 'crear-coleccion-btn';
        boton.textContent = 'Crear nueva colección';
        boton.onclick = mostrarFormularioNuevaColeccion;
        boton.className = 'coleccion-boton crear-nueva-coleccion-boton';
        contenedor.appendChild(boton);
    }
}
