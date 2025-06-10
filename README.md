# Funko pops Collection

## Despliegue de la aplicación.
 ### Requisitos Previos para usar la aplicación.
 Antes de comenzar, asegúrate de tener instalados en tu sistema:
 
 - Docker
 - Docker Compose
 - Git

### Instalación y Despliegue.
1. Clonar el repositorio. <br>
   - Ejecuta el siguiente comando para clonar el proyecto:<br>
2. Levantar los contenedores.<br>
   - Para iniciar los servicios en segundo plano, ejecuta:<br>
     - docker-compose up -d<br>
   - Notas:<br>
     - La primera vez que inicies los servicios, puede tardar unos minutos en configurarse completamente.<br>
     - Una vez que los contenedores se han levantado, ya se puede iniciar la aplicación.<br>
     - La base de datos se instala automáticamente con este comando al venir incorporada en el proyecto.<br>
3. Verificar que los contenedores están corriendo:<br>
    - Comprueba el estado de los contenedores con:<br>
      - docker ps<br>
4. Acceder a la aplicación<br>
    - Para acceder a la aplicación web solo se debe acceder la siguiente URL desde el navegador.<br>
       - http://localhost:8080<br>
       
### Detener y reiniciar los contenedores:<br>
   - Si deseas detener los contenedores en ejecución:
      - docker compose down.<br>
   - Para volver a iniciarlos:<br>
      - docker compose up -d<br>
   - Eliminar los contenedores y datos persistentes:<br>
     - Si quieres eliminar los contenedores junto con los volúmenes y datos almacenados:<br>
       - docker compose down -v<br>
