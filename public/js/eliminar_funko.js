// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", () => {

  // ELIMINAR FUNKOS DE UNA COLECCIÓN 

  // Selecciona todos los botones que eliminan Funkos de una colección
  document.querySelectorAll(".btn-eliminar-funko").forEach(btn => {
    btn.addEventListener("click", function () {
      // Muestra un mensaje de confirmación al usuario
      if (!confirm("¿Seguro que quieres eliminar este Funko?")) return;

      // Obtiene el id del funko y el id de la colección desde los atributos `data-id` y `data-coleccion`
      const id = this.dataset.id;
      const idcoleccion = this.dataset.coleccion;

      // Realiza una petición POST al backend para eliminar el Funko
      fetch("/api.php?action=delfunko", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}&idcoleccion=${encodeURIComponent(idcoleccion)}`
      })
      .then(res => res.json()) 
      .then(data => {
        // Muestra un mensaje al usuario
        alert(data.mensaje);

        // Si la eliminación fue exitosa, elimina la fila de la tabla correspondiente al Funko
        if (data.success) {
          // Selecciona la fila usando el atributo data-funko
          const fila = document.querySelector(`tr[data-funko="${id}"]`);
          if (fila) fila.remove();
        }
      })
      .catch(() => {
        // Manejo de errores en caso de fallo de red o del servidor
        alert("Error al intentar eliminar el Funko.");
      });
    });
  });

  //  ELIMINAR FUNKOS DE LA LISTA DE DESEOS

  // Selecciona todos los botones que eliminan Funkos de la lista de deseos
  document.querySelectorAll(".btn-eliminar-deseo").forEach(btn => {
    btn.addEventListener("click", function () {
      // Muestra una confirmación antes de eliminar
      if (!confirm("¿Estás seguro de que deseas eliminar este Funko de tu lista de deseos?")) return;

      // Obtiene el ID del deseo desde el atributo data-id
      const id = this.dataset.id;

      // Realiza una petición POST al backend para eliminar el deseo
      fetch("/api.php?action=delwish", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}`
      })
      .then(res => res.json()) 
      .then(data => {
        // Muestra el mensaje devuelto por el servidor
        alert(data.message);

        if (data.success) {
          // Intenta eliminar la fila correspondiente al deseo del DOM
          const fila = document.querySelector(`tr[data-deseo="${id}"]`);
          if (fila) {
            fila.remove();
          } else {
            // Si no encuentra la fila, recarga la página como alternativa
            location.reload();
          }
        }
      })
      .catch(() => {
        // Muestra un error si la petición falla
        alert("Error al intentar eliminar el Funko.");
      });
    });
  });
});










