//Script para editar los funko pops de las colecciones
// Espera que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {

    // Recorre cada fila que tenga el atributo data-funko
    document.querySelectorAll('tr[data-funko]').forEach(row => {
        const accionCell = row.querySelector('td:last-child'); 

        // Obtiene los botones esperados 
        const botones = ['btn-editar-funko', 'btn-guardar-funko', 'btn-cancelar-funko', 'btn-eliminar-funko']
            .map(clase => accionCell.querySelector(`.${clase}`)) // Busca cada botón
            .filter(btn => btn !== null); // Elimina nulos si no existe alguno

        // Si solo hay botones y no se ha estructurado aún
        if (botones.length > 0 && !accionCell.querySelector('.btns-contenedor')) {
            // Crear contenedor para los botones
            const contBotones = document.createElement('div');
            contBotones.classList.add('btns-contenedor');
            contBotones.style.display = 'flex';
            contBotones.style.gap = '6px';
            contBotones.style.alignItems = 'center';
            contBotones.style.verticalAlign = 'middle';

            // Mueve los botones dentro del nuevo contenedor
            botones.forEach(btn => contBotones.appendChild(btn));

            // Limpia la celda y añade el contenedor
            accionCell.textContent = '';
            accionCell.style.padding = '4px 8px';
            accionCell.style.whiteSpace = 'nowrap';
            accionCell.appendChild(contBotones);
        }
    });

    const tabla = document.querySelector('table'); // Selecciona la tabla principal

    // Escucha dentro de la tabla
    tabla.addEventListener('click', (event) => {
        const target = event.target;

        // Botón para editar
        if (target.classList.contains('btn-editar-funko')) {
            const row = target.closest('tr');

            // Oculta elementos de vista
            row.querySelector('.nombre-view').style.display = 'none';
            row.querySelector('.numero-view').style.display = 'none';
            row.querySelector('.imagen-view').style.display = 'none';

            // Muestra campos de edición
            row.querySelector('.nombre-edit').style.display = 'inline';
            row.querySelector('.numero-edit').style.display = 'inline';
            row.querySelector('.imagen-edit').style.display = 'inline';

            // Muestra/oculta botones
            target.style.display = 'none';
            row.querySelector('.btn-guardar-funko').style.display = 'inline';
            row.querySelector('.btn-cancelar-funko').style.display = 'inline';
        }

        // Botón para cancelar editar
        if (target.classList.contains('btn-cancelar-funko')) {
            const row = target.closest('tr');

            // Oculta campos de edición
            row.querySelector('.nombre-edit').style.display = 'none';
            row.querySelector('.numero-edit').style.display = 'none';
            row.querySelector('.imagen-edit').style.display = 'none';

            // Muestra elementos originales
            row.querySelector('.nombre-view').style.display = 'inline';
            row.querySelector('.numero-view').style.display = 'inline';
            row.querySelector('.imagen-view').style.display = 'inline';

            // Ajusta botones
            row.querySelector('.btn-editar-funko').style.display = 'inline';
            row.querySelector('.btn-guardar-funko').style.display = 'none';
            target.style.display = 'none';
        }

        //  Botón para guardar lo editado
        if (target.classList.contains('btn-guardar-funko')) {
            const row = target.closest('tr');
            const idFunko = target.dataset.id;

            console.log('Click guardar, idFunko:', idFunko); 

            if (!idFunko) {
                alert('Error: No se encontró el ID del Funko.');
                return;
            }

            // Obtiene los valores editados
            const nombreNuevo = row.querySelector('.nombre-edit').value;
            const numeroNuevo = row.querySelector('.numero-edit').value;
            const archivoImagen = row.querySelector('.imagen-edit').files[0];

            // Envia datos al servidor
            const formData = new FormData();
            formData.append('id', idFunko);
            formData.append('nombre', nombreNuevo);
            formData.append('numero', numeroNuevo);
            if (archivoImagen) {
                formData.append('imagen', archivoImagen);
            }

            fetch('/api.php?action=editfunko', {
                method: 'POST',
                body: formData,
            })
            .then(res => res.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);

                    if (data.success) {
                        alert('Funko guardado correctamente');

                        // Actualiza la vista con nuevos valores
                        row.querySelector('.nombre-view').textContent = nombreNuevo;
                        row.querySelector('.numero-view').textContent = numeroNuevo;

                        if (data.nuevaImagen) {
                            const imgView = row.querySelector('.imagen-view');
                            if (imgView) imgView.src = data.nuevaImagen;
                        }

                        // Regresa a modo vista
                        row.querySelector('.nombre-view').style.display = 'inline';
                        row.querySelector('.numero-view').style.display = 'inline';
                        row.querySelector('.imagen-view').style.display = 'inline';

                        row.querySelector('.nombre-edit').style.display = 'none';
                        row.querySelector('.numero-edit').style.display = 'none';
                        row.querySelector('.imagen-edit').style.display = 'none';

                        row.querySelector('.btn-editar-funko').style.display = 'inline';
                        row.querySelector('.btn-guardar-funko').style.display = 'none';
                        row.querySelector('.btn-cancelar-funko').style.display = 'none';
                    } else {
                        alert(data.mensaje || 'Error al guardar');
                    }
                } catch (e) {
                    console.error('Respuesta no es JSON válido:', text);
                    alert('Error al procesar la respuesta del servidor');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error en la petición');
            });
        }
    });
});



//Script para editar los funkos de la lista de deseos.

document.addEventListener('DOMContentLoaded', () => {

    // Agrupa los botones en las filas con deseos
    document.querySelectorAll('tr[data-wish]').forEach(row => {
        const accionCell = row.querySelector('td:last-child');

        if (!accionCell.querySelector('.btns-contenedor')) {
            const contBotones = document.createElement('div');
            contBotones.classList.add('btns-contenedor');
            contBotones.style.display = 'flex';
            contBotones.style.gap = '6px';
            contBotones.style.alignItems = 'center';

            // Mueve los botones al contenedor
            ['btn-editar-deseo', 'btn-guardar-deseo', 'btn-cancelar-deseo', 'btn-eliminar-deseo'].forEach(clase => {
                const btn = accionCell.querySelector(`.${clase}`);
                if (btn) contBotones.appendChild(btn);
            });

            accionCell.textContent = '';
            accionCell.style.padding = '4px 8px';
            accionCell.style.whiteSpace = 'nowrap';
            accionCell.appendChild(contBotones);
        }
    });

    const tabla = document.querySelector('table');

    // Controla los clics en la tabla
    tabla.addEventListener('click', (event) => {
        const target = event.target;
        const row = target.closest('tr');
        if (!row) return;

        // Botón para editar el funko en la lista de deseos
        if (target.classList.contains('btn-editar-deseo')) {
            row.querySelector('.nombre-view').style.display = 'none';
            row.querySelector('.numero-view').style.display = 'none';
            row.querySelector('.imagen-view').style.display = 'none';

            row.querySelector('.nombre-edit').style.display = 'inline';
            row.querySelector('.numero-edit').style.display = 'inline';
            row.querySelector('.imagen-edit').style.display = 'inline';

            target.style.display = 'none';
            row.querySelector('.btn-guardar-deseo').style.display = 'inline';
            row.querySelector('.btn-cancelar-deseo').style.display = 'inline';
        }

        // Botón para cancelar la edición de la lista de deseos
        if (target.classList.contains('btn-cancelar-deseo')) {
            row.querySelector('.nombre-edit').style.display = 'none';
            row.querySelector('.numero-edit').style.display = 'none';
            row.querySelector('.imagen-edit').style.display = 'none';

            row.querySelector('.nombre-view').style.display = 'inline';
            row.querySelector('.numero-view').style.display = 'inline';
            row.querySelector('.imagen-view').style.display = 'inline';

            row.querySelector('.btn-editar-deseo').style.display = 'inline';
            row.querySelector('.btn-guardar-deseo').style.display = 'none';
            target.style.display = 'none';
        }

        // Botón para guardar los cambios de la lista de deseos
        if (target.classList.contains('btn-guardar-deseo')) {
            const idWish = target.dataset.id;
            if (!idWish) return alert('ID no encontrado');

            const nombreNuevo = row.querySelector('.nombre-edit').value;
            const numeroNuevo = row.querySelector('.numero-edit').value;
            const archivoImagen = row.querySelector('.imagen-edit').files[0];

            const formData = new FormData();
            formData.append('id', idWish);
            formData.append('nombre', nombreNuevo);
            formData.append('numero', numeroNuevo);
            if (archivoImagen) formData.append('imagen', archivoImagen);

            fetch('/api.php?action=editwish', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Funko editado correctamente');

                    row.querySelector('.nombre-view').textContent = nombreNuevo;
                    row.querySelector('.numero-view').textContent = numeroNuevo;

                    if (data.nuevaImagen) {
                        row.querySelector('.imagen-view').src = data.nuevaImagen;
                    }

                    // Cambiar de vuelta a modo vista
                    row.querySelector('.nombre-edit').style.display = 'none';
                    row.querySelector('.numero-edit').style.display = 'none';
                    row.querySelector('.imagen-edit').style.display = 'none';

                    row.querySelector('.nombre-view').style.display = 'inline';
                    row.querySelector('.numero-view').style.display = 'inline';
                    row.querySelector('.imagen-view').style.display = 'inline';

                    row.querySelector('.btn-editar-deseo').style.display = 'inline';
                    row.querySelector('.btn-guardar-deseo').style.display = 'none';
                    row.querySelector('.btn-cancelar-deseo').style.display = 'none';
                } else {
                    alert(data.mensaje || 'Error al guardar');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error en la petición');
            });
        }
    });
});
