<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

$nombre = '';
$descripcion = '';
$precio = '';
$cantidad = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $precio = trim((string) ($_POST['precio'] ?? ''));
    $cantidad = trim((string) ($_POST['cantidad'] ?? ''));

    if ($nombre === '' || strlen($nombre) > 120) {
        $errores[] = 'El nombre es obligatorio y debe tener como máximo 120 caracteres.';
    }
    if (strlen($descripcion) > 500) {
        $errores[] = 'La descripción debe tener como máximo 500 caracteres.';
    }
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $precio) || (float) $precio > 99999999.99) {
        $errores[] = 'El precio debe ser un número positivo con hasta dos decimales.';
    }
    if (!ctype_digit($cantidad) || (float) $cantidad > 4294967295) {
        $errores[] = 'La cantidad debe ser un número entero igual o mayor que cero.';
    }

    if ($errores === []) {
        try {
            $conexion = conectarBaseDatos();
            $consulta = $conexion->prepare(
                'INSERT INTO productos (nombre, descripcion, precio, cantidad)
                 VALUES (:nombre, :descripcion, :precio, :cantidad)'
            );
            $consulta->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion === '' ? null : $descripcion,
                'precio' => $precio,
                'cantidad' => $cantidad,
            ]);
            header('Location: /?guardado=1', true, 303);
            exit;
        } catch (RuntimeException | PDOException $error) {
            error_log($error->getMessage());
            $errores[] = 'No se pudo guardar el producto. Inténtalo más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de productos</title>
</head>
<body>
    <main>
        <h1>Registro de productos</h1>
        <p>Ingresa los datos de un producto para guardarlo en la base de datos.</p>

        <?php if (isset($_GET['guardado'])): ?>
            <p role="status">Producto guardado correctamente.</p>
        <?php endif; ?>

        <?php if ($errores !== []): ?>
            <div role="alert">
                <?php foreach ($errores as $error): ?>
                    <p><?= escapar($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" maxlength="120" required value="<?= escapar($nombre) ?>">

            <label for="descripcion">Descripción (opcional)</label>
            <textarea id="descripcion" name="descripcion" maxlength="500"><?= escapar($descripcion) ?></textarea>

            <label for="precio">Precio</label>
            <input id="precio" name="precio" type="number" min="0" max="99999999.99" step="0.01" required value="<?= escapar($precio) ?>">

            <label for="cantidad">Cantidad</label>
            <input id="cantidad" name="cantidad" type="number" min="0" max="4294967295" step="1" required value="<?= escapar($cantidad) ?>">

            <button type="submit">Guardar producto</button>
        </form>
    </main>
</body>
</html>
