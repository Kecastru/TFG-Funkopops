// Ejecuta cuando el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
    // Botón para cerrar sesión
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            window.location.href = '/api.php?action=logout';
        });
    }

    // Botón para registrarse desde index.php
    const registrarseBtn = document.getElementById('btnRegistrarse');
    if (registrarseBtn) {
        registrarseBtn.addEventListener('click', () => {
            window.location.href = 'registro.php';
        });
    }

        // Botón para volver a index.php desde el registro
    const backIndex = document.getElementById('backindex');
    if (backIndex) {
        backIndex.addEventListener('click', () => {
            window.location.href = 'index.php';
        });
    }
    // Botón para  de añadir Funko
    const iraddfunko = document.getElementById('buttoniradd');
    if (iraddfunko) {
        iraddfunko.addEventListener('click', () => {
            window.location.href = '/api.php?action=addfunko';
        });
    }
    // Botón para volver al menú principal 
    const volvermenuBtn = document.getElementById('volvermenu');
    if (volvermenuBtn) {
        volvermenuBtn.addEventListener('click', () => {
            window.location.href = 'menu.php';
        });
    }

   // Botón para ir a otras colecciones 
    const otrascoleccionesir = document.getElementById('otrascolecciones');
    if (otrascoleccionesir) {
        otrascoleccionesir.addEventListener('click', () => {
            window.location.href = 'colecciones.php';
        });
    }

    // Botón para ir a la lista de deseos
    const irlistadeseos = document.getElementById('listadeseos');
    if (irlistadeseos) {
        irlistadeseos.addEventListener('click', () => {
            window.location.href = 'listadeseos.php';
        });
    }

    // Modal de imágenes

    // Referencia al contenedor del modal
    const modal = document.getElementById("imagenModal");
    // Referencia a la imagen ampliada dentro del modal
    const imgAmpliada = document.getElementById("imagenAmpliada");
    // Botón para cerrar el modal
    const cerrarBtn = document.getElementById("cerrarModal");

// Solo se ejecuta si los elementos del modal existen en el DOM
    if (modal && imgAmpliada && cerrarBtn) {
        // Selecciona todas las imágenes dentro de la tabla con clase "tabla-funko"
        document.querySelectorAll(".tabla-funko img").forEach(img => {
            img.addEventListener("click", function () {
                imgAmpliada.src = this.src;
                imgAmpliada.alt = this.alt;
                modal.style.display = "block";
            });
        });
        // Cierra el modal al hacer clic en la "X"
        cerrarBtn.addEventListener("click", function () {
            modal.style.display = "none";
            imgAmpliada.src = ""; 
        });
        // Cierra el modal si se hace clic fuera de la imagen
        window.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
                imgAmpliada.src = "";
            }
        });
    }
});


