<?php
session_start();
error_reporting(E_ALL);

require_once 'db.php'; // Incluir el archivo de conexión a la base de datos

// Parámetros de paginación
$productos_por_pagina = 7;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $productos_por_pagina;

// Parámetro de búsqueda
$busqueda = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

// Verificar si el formulario de agregar al carrito ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['CodProducto'])) {
    if (!isset($_SESSION['cod_cliente'])) {
        header("Location: IniciarSesion.php");
        exit();
    }

    $cod_cliente = $_SESSION['cod_cliente'];
    $cod_producto = $_POST['CodProducto'];

    // Obtener el carrito del cliente
    $sql = "SELECT CodCarrito FROM carrito WHERE CodCliente = $cod_cliente";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Cliente tiene un carrito
        $row = $result->fetch_assoc();
        $cod_carrito = $row['CodCarrito'];

        // Verificar si el producto ya está en el carrito
        $sql_check = "SELECT cantidad FROM carrito_productos WHERE CodCarrito = $cod_carrito AND CodProducto = $cod_producto";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // El producto ya está en el carrito, incrementar la cantidad
            $row_check = $result_check->fetch_assoc();
            $cantidad = $row_check['cantidad'] + 1;
            $sql_update = "UPDATE carrito_productos SET cantidad = $cantidad WHERE CodCarrito = $cod_carrito AND CodProducto = $cod_producto";
            $conn->query($sql_update);
        } else {
            // El producto no está en el carrito, agregarlo
            $sql_insert = "INSERT INTO carrito_productos (CodCarrito, CodProducto, cantidad) VALUES ($cod_carrito, $cod_producto, 1)";
            if ($conn->query($sql_insert) !== TRUE) {
                die("Error al agregar el producto al carrito: " . $conn->error);
            }
        }
    } else {
        die("Error: No se encontró un carrito para este cliente.");
    }

    // Redirigir a la misma página para evitar reenvío del formulario
    header("Location: productos.php?pagina=$pagina_actual&q=" . urlencode($busqueda));
    exit();
}

// Construir la consulta SQL con paginación y búsqueda
$sql = "SELECT CodProducto, NombreProducto, TipoProducto, imagen, precio FROM productos";
if ($busqueda) {
    $sql .= " WHERE NombreProducto LIKE '%$busqueda%'";
}
$sql .= " LIMIT $inicio, $productos_por_pagina";
$result = $conn->query($sql);

$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Obtener el número total de productos para la paginación
$sql_total = "SELECT COUNT(*) as total FROM productos";
if ($busqueda) {
    $sql_total .= " WHERE NombreProducto LIKE '%$busqueda%'";
}
$result_total = $conn->query($sql_total);
$total_productos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_productos / $productos_por_pagina);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>EcoLiving Productions</title>
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
            // Verificar si el usuario es administrador
            if ($_SESSION['usuario'] === 'Admin') {
                echo "<br><a href='gestionarProductos.php' class='boton-gestionar'>Gestionar productos</a>";
            }
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
            <h2>Productos</h2>
        </section>

        <section id="buscador">
            <form action="productos.php" method="get">
                <input type="text" id="campoBusqueda" name="q" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit" id="botonBusqueda">Buscar</button>
            </form>
        </section>

        <section id="productos">
            <?php if (count($productos) > 0): ?>
                <?php foreach ($productos as $producto): ?>
                    <article class="producto">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="imagen del producto">
                        <h4><?php echo htmlspecialchars($producto['NombreProducto']); ?></h4>
                        <p class="precio">Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
                        <form action="productos.php?pagina=<?php echo $pagina_actual; ?>&q=<?php echo htmlspecialchars($busqueda); ?>" method="post">
                            <input type="hidden" name="CodProducto" value="<?php echo $producto['CodProducto']; ?>">
                            <button type="submit" class="boton-carrito">Añadir al carrito</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos disponibles.</p>
            <?php endif; ?>
        </section>

        <section id="navegacion">
            <?php if ($pagina_actual > 1): ?>
                <a href="productos.php?pagina=<?php echo $pagina_actual - 1; ?>&q=<?php echo htmlspecialchars($busqueda); ?>">
                    <button id="botonAnterior">Anterior</button>
                </a>
            <?php endif; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="productos.php?pagina=<?php echo $pagina_actual + 1; ?>&q=<?php echo htmlspecialchars($busqueda); ?>">
                    <button id="botonSiguiente">Siguiente</button>
                </a>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>SIE 23/24 Grupo Nº9 EcoLivingProductions<br></p>
    </footer>
</body>
</html>
