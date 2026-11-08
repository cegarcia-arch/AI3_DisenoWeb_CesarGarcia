# Actividad Integradora 3 - Registro de productos

Proyecto básico en PHP y MariaDB. Permite registrar productos y verlos en una tabla. Usa PDO para la conexión, sentencias preparadas para guardar y `htmlspecialchars()` al mostrar los datos.

## Ejecutar en Ubuntu

Se necesita Docker con Docker Compose.

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
