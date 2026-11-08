# Actividad Integradora 3 - Registro de productos

Proyecto básico en PHP y MariaDB. Permite registrar productos y verlos en una tabla. Usa PDO para la conexión, sentencias preparadas para guardar y `htmlspecialchars()` al mostrar los datos.

Repositorio público: https://github.com/cegarcia-arch/AI3_DisenoWeb_CesarGarcia

## Ejecutar en Ubuntu

Se necesita Docker con Docker Compose.

Primero clonar el repositorio y entrar a su carpeta:

```bash
git clone https://github.com/cegarcia-arch/AI3_DisenoWeb_CesarGarcia.git
cd AI3_DisenoWeb_CesarGarcia
```

1. Copiar `.env.example` como `.env` y cambiar las dos contraseñas de ejemplo.
2. Iniciar los contenedores:

   ```bash
   docker compose up -d --build
   ```

3. Crear la tabla a partir del archivo SQL:

   ```bash
   docker compose exec -T db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD"' < base_datos.sql
   ```

4. Abrir `http://localhost:8080` para usar la aplicación. phpMyAdmin está en `http://localhost:8081`; el usuario es `ai3` y la contraseña es `DB_PASSWORD` del archivo `.env`.

Para detener los contenedores:

```bash
docker compose down
```

El archivo `.env` contiene las contraseñas locales y no se incluye en Git.

## Fuentes consultadas

- [PHP: elegir una API para MySQL](https://www.php.net/manual/es/mysqlinfo.api.choosing.php)
- [PHP: PDO](https://www.php.net/manual/es/book.pdo.php)
- [PHP: sentencias preparadas](https://www.php.net/manual/es/pdo.prepared-statements.php)

## Archivos del proyecto

- `public/conexion.php`: conexión PDO y manejo de errores.
- `public/index.php`: formulario, validaciones, inserción preparada y consulta con salida escapada.
- `public/estilos.css`: estilos y desplazamiento horizontal de la tabla en pantallas estrechas.
- `base_datos.sql`: creación de la base de datos y la tabla de seis campos.
- `evidencias/`: capturas del formulario, los registros y la estructura en phpMyAdmin.

El módulo implementa registro y consulta, conforme a las operaciones Create y Read solicitadas en la actividad.

## Comprobaciones realizadas

Se verificaron la sintaxis PHP, el registro y la lectura de productos, los límites de longitud con tildes y el rechazo de campos vacíos, precios negativos, exceso de decimales y cantidades inválidas. También se probó que una cadena con sintaxis SQL se almacena como texto y que las etiquetas de script se muestran escapadas. Los registros temporales de estas pruebas fueron eliminados.

Las contraseñas se mantienen en `.env`, excluido de Git. La base de datos conserva sus datos en un volumen de Docker; el archivo SQL solo contiene la estructura. Las fechas de creación de los productos se muestran según la zona horaria del servidor de base de datos (UTC en el entorno comprobado).
