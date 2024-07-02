<?php
session_start();
require_once 'db.php'; // Incluir el archivo de conexión a la base de datos

if (!isset($_SESSION['cod_cliente'])) {
    header("Location: IniciarSesion.php");
    exit();
}

$cod_cliente = $_SESSION['cod_cliente'];

// Verificar si el formulario de eliminar ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['CodProductoEliminar']) || isset($_POST['CodProductoReducir']))) {
    if (isset($_POST['CodProductoEliminar'])) {
        $cod_producto_eliminar = $_POST['CodProductoEliminar'];

        // Obtener el carrito del cliente
        $sql = "SELECT CodCarrito FROM carrito WHERE CodCliente = $cod_cliente";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cod_carrito = $row['CodCarrito'];

            // Eliminar producto del carrito
            $sql = "DELETE FROM carrito_productos WHERE CodCarrito = $cod_carrito AND CodProducto = $cod_producto_eliminar";
            if ($conn->query($sql) !== TRUE) {
                die("Error al eliminar el producto del carrito: " . $conn->error);
            }
        }
    } elseif (isset($_POST['CodProductoReducir'])) {
        $cod_producto_reducir = $_POST['CodProductoReducir'];

        // Obtener el carrito del cliente
        $sql = "SELECT CodCarrito FROM carrito WHERE CodCliente = $cod_cliente";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cod_carrito = $row['CodCarrito'];

            // Reducir cantidad del producto en el carrito
            $sql = "UPDATE carrito_productos SET cantidad = cantidad - 1 WHERE CodCarrito = $cod_carrito AND CodProducto = $cod_producto_reducir AND cantidad > 1";
            if ($conn->query($sql) !== TRUE) {
                die("Error al reducir la cantidad del producto en el carrito: " . $conn->error);
            }
        }
    }
}

// Consulta para obtener los productos en el carrito del cliente
$sql = "SELECT p.CodProducto, p.NombreProducto, p.TipoProducto, p.imagen, cp.cantidad, p.precio
        FROM productos p
        INNER JOIN carrito_productos cp ON p.CodProducto = cp.CodProducto
        INNER JOIN carrito c ON cp.CodCarrito = c.CodCarrito
        WHERE c.CodCliente = $cod_cliente";

$result = $conn->query($sql);

$productos = [];
$total_precio = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
        $total_precio += $row['precio'] * $row['cantidad'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>EcoLiving Productions - Carrito</title>
    <link rel="stylesheet" type="text/css" href="../css/PyC.css">
    <meta charset="UTF-8">
</head>
<body>
    <header>
        <h1>EcoLiving Productions</h1>
        <img class="logo" src="../images/logoSinFondo.png" alt="Logo">
        <br>
        <?php
        if (isset($_SESSION['usuario'])) {
            echo "<p class='bienvenido'>Bienvenido, " . htmlspecialchars($_SESSION['usuario']) . " <a href='logout.php'>Cerrar sesión</a></p>";
        }
        ?>
        <section id="menuDesplegable">
            <article class="TodoMenu">
                <article class="content">
                    <nav role="navigation">
                        <article id="AjustesMenu">
                            <input type="checkbox" />
                            <span></span>
                            <span></span>
                            <span></span>
                            <ul id="menu">
                                <li><a class="aMenu" href="index.php">Inicio</a></li>
                                <li><a class="aMenu" href="productos.php">Productos</a></li>
                                <li><a class="aMenu" href="quienessomos.php">¿Quiénes somos?</a></li>
                                <li><a class="aMenu" href="IniciarSesion.php">Iniciar sesión</a></li>
                                <li><a class="aMenu" href="carrito.php">Carrito</a></li>
                            </ul>
                        </article>
                    </nav>
                </article>
            </article>
        </section>
    </header>
    <main>
        <section style="text-align: center;">
            <h2>Carrito</h2>
            <section id="productos">
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <article class="producto">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="imagen del producto">
                            <h4><?php echo htmlspecialchars($producto['NombreProducto']); ?></h4>
                            <p class="tipo">Tipo: <?php echo htmlspecialchars($producto['TipoProducto']); ?></p>
                            <p class="precio">Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
                            <p class="cantidad">Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?></p>
                            <form action="carrito.php" method="post" style="display: inline;">
                                <input type="hidden" name="CodProductoEliminar" value="<?php echo $producto['CodProducto']; ?>">
                                <button type="submit" class="boton-carrito">Eliminar del carrito</button>
                            </form>
                            <?php if ($producto['cantidad'] > 1): ?>
                                <form action="carrito.php" method="post" style="display: inline;">
                                    <input type="hidden" name="CodProductoReducir" value="<?php echo $producto['CodProducto']; ?>">
                                    <button type="submit" class="boton-reducir">Reducir cantidad</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                    <article class="total">
                        <h4>Total del carrito: $<?php echo number_format($total_precio, 2); ?></h4>
                        <form action="procesar_compra.php" method="post">
                            <button type="submit" class="boton-reducir">Procesar Compra</button>
                        </form>
                    </article>
                <?php else: ?>
                    <p>No hay productos en el carrito.</p>
                <?php endif; ?>
            </section>
        </section>
    </main>
    <footer>
        <p>SIE 23/24 Grupo Nº9 EcoLivingProductions<br></p>
    </footer>
</body>
</html>