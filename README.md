Requisitos Previos para usar la aplicación.
    • Antes de comenzar, asegúrate de tener instalados en tu sistema:
        ◦ Docker 
        ◦ Docker Compose 
        ◦ Git 
Instalación y Despliegue
    1. Clonar el repositorio.
        ◦ Ejecuta el siguiente comando para clonar el proyecto:
    2. Levantar los contenedores.
        ◦ Para iniciar los servicios en segundo plano, ejecuta:
          docker-compose up -d
          
        ◦ Notas: 
            ▪ La primera vez que inicies los servicios, puede tardar unos minutos en configurarse completamente.
            ▪ Una vez que los contenedores se han levantado, ya se puede iniciar la aplicación.
            ▪ La base de datos se instala automáticamente con este comando al venir incorporada en el proyecto

    3. Verificar que los contenedores están corriendo:
        ◦ Comprueba el estado de los contenedores con:
	      docker ps

    4. Acceder a la aplicación
        ◦ Para acceder a la aplicación web solo se debe acceder la siguiente URL desde el navegador.
	      http://localhost:8080
       
Detener y reiniciar los contenedores:
    • Si deseas detener los contenedores en ejecución:
      docker compose down.

    • Para volver a iniciarlos:
      docker compose up -d

Eliminar los contenedores y datos persistentes:
    • Si quieres eliminar los contenedores junto con los volúmenes y datos almacenados:
      docker compose down -v
