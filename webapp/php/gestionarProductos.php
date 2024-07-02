<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'Admin') {
    header("Location: IniciarSesion.php");
    exit();
}

require_once 'db.php'; // Incluir el archivo de conexión a la base de datos

// Manejar la actualización de cantidades
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['CodProducto']) && isset($_POST['cantidad'])) {
    var_dump($_POST); // Añadir esta línea para depuración
    $cod_producto = $_POST['CodProducto'];
    $cantidad = $_POST['cantidad'];

    echo $cod_producto;
    echo $cantidad;

    $sql_update = "UPDATE productos SET Cantidad = Cantidad + $cantidad WHERE CodProducto = $cod_producto";
    echo $sql_update; // Añadir esta línea para depuración
    if ($conn->query($sql_update) !== TRUE) {
        die("Error al actualizar la cantidad del producto: " . $conn->error);
    } else {
        echo "<script>alert('Cantidad actualizada correctamente');</script>";
    }

    // Redirigir para evitar reenvío del formulario
    header("Location: gestionarProductos.php");
    exit();
}

// Obtener los productos para el formulario
$sql = "SELECT CodProducto, NombreProducto FROM productos";
$result = $conn->query($sql);
$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Productos - EcoLiving Productions</title>
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <meta charset="UTF-8">
</head>
<body>
    <header>
        <h1>EcoLiving Productions</h1>
        <img class="logo" src="../images/logoSinFondo.png" alt="Logo">
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
        <?php
        if (isset($_SESSION['usuario'])) {
            echo "<p class='bienvenido'>Bienvenido, " . htmlspecialchars($_SESSION['usuario']) . " <a href='logout.php'>Cerrar sesión</a></p>";
        }
        ?>
    </header>
    <main>

        <section class="sectionGestionar">
            <form action="gestionarProductos.php" method="post">
                <fieldset class="fieldsetGestionar">
                    <legend>Actualizar Cantidad</legend>
                    <label for="productoSeleccionado">Seleccionar Producto:</label>
                    <br>
                    <select id="productoSeleccionado" name="CodProducto" required>
                        <option value="" disabled selected>Seleccione un producto</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo htmlspecialchars($producto['CodProducto']); ?>">
                                <?php echo htmlspecialchars($producto['NombreProducto']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="cantidadAgregar">Cantidad a Añadir:</label>
                    <input type="number" id="cantidadAgregar" name="cantidad" min="1" required>
                        
                    <button class="botonGestionar" id="botonActualizar" type="submit">Actualizar Cantidad</button>
                    <br>
                    <input type="reset" class="botonGestionar" id="botonReiniciar" value="Reiniciar formulario" />
                </fieldset>
            </form>
        </section>

    </main>
    <footer>
        <p>SIE 23/24 Grupo Nº9 EcoLivingProductions<br></p>
    </footer>
</body>
</html>
