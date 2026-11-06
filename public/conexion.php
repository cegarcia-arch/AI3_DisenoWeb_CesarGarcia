<?php
declare(strict_types=1);

function conectarBaseDatos(): PDO
{
    $servidor = getenv('DB_HOST') ?: 'db';
    $baseDatos = getenv('DB_NAME') ?: 'db_sistema_web';
    $usuario = getenv('DB_USER') ?: '';
    $contrasena = getenv('DB_PASSWORD') ?: '';

    try {
        return new PDO(
            "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4",
            $usuario,
            $contrasena,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $error) {
        error_log($error->getMessage());
        throw new RuntimeException('No se pudo conectar con la base de datos. Inténtalo más tarde.');
    }
}
