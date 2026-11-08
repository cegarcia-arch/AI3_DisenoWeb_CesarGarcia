<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

    if (!preg_match('/^.{1,120}$/us', $nombre)) {
        $errores[] = 'El nombre es obligatorio y debe tener como máximo 120 caracteres.';
    }
    if (!preg_match('/^.{0,500}$/us', $descripcion)) {
        $errores[] = 'La descripción debe tener como máximo 500 caracteres.';
    }
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $precio) || (float) $precio > 99999999.99) {
        $errores[] = 'El precio debe ser un número igual o mayor que cero con hasta dos decimales.';
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

$productos = [];
$errorConsulta = '';
try {
    $conexion = conectarBaseDatos();
    $productos = $conexion->query(
        'SELECT id, nombre, descripcion, precio, cantidad, fecha_creacion
         FROM productos ORDER BY id DESC'
    )->fetchAll();
} catch (RuntimeException | PDOException $error) {
    error_log($error->getMessage());
    $errorConsulta = 'No se pudieron cargar los productos. Inténtalo más tarde.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de productos</title>
    <link rel="stylesheet" href="/estilos.css">
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

        <section aria-labelledby="titulo-lista">
            <h2 id="titulo-lista">Productos registrados</h2>
            <?php if ($errorConsulta !== ''): ?>
                <p role="alert"><?= escapar($errorConsulta) ?></p>
            <?php elseif ($productos === []): ?>
                <p>Todavía no hay productos registrados.</p>
            <?php else: ?>
                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Fecha de creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= escapar((string) $producto['id']) ?></td>
                                    <td><?= escapar((string) $producto['nombre']) ?></td>
                                    <td><?= escapar((string) ($producto['descripcion'] ?? '')) ?></td>
                                    <td>$<?= escapar((string) $producto['precio']) ?></td>
                                    <td><?= escapar((string) $producto['cantidad']) ?></td>
                                    <td><?= escapar((string) $producto['fecha_creacion']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
