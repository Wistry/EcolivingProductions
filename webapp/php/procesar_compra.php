<?php
session_start();
require_once 'db.php'; // Incluir el archivo de conexión a la base de datos

if (!isset($_SESSION['cod_cliente'])) {
    header("Location: IniciarSesion.php");
    exit();
}

$cod_cliente = $_SESSION['cod_cliente'];

// Obtener el carrito del cliente
$sql = "SELECT CodCarrito FROM carrito WHERE CodCliente = $cod_cliente";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cod_carrito = $row['CodCarrito'];

    // Obtener los productos en el carrito
    $sql = "SELECT cp.CodProducto, cp.cantidad, p.Cantidad as cantidad_disponible
            FROM carrito_productos cp
            INNER JOIN productos p ON cp.CodProducto = p.CodProducto
            WHERE cp.CodCarrito = $cod_carrito";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $stock_suficiente = true; // Variable para verificar si hay suficiente stock
        while ($row = $result->fetch_assoc()) {
            $cod_producto = $row['CodProducto'];
            $cantidad_carrito = $row['cantidad'];
            $cantidad_disponible = $row['cantidad_disponible'];

            // Verificar si hay suficiente stock para el producto en el carrito
            if ($cantidad_carrito > $cantidad_disponible) {
                $stock_suficiente = false;
                break; // Salir del bucle si no hay suficiente stock para al menos un producto
            }
        }

        if ($stock_suficiente) {
            // Si hay suficiente stock para todos los productos en el carrito, proceder con la compra
            // Restar la cantidad de productos en la base de datos y limpiar el carrito
        
            // Volver a ejecutar la consulta para obtener los productos en el carrito
            $result = $conn->query($sql);
        
            // Actualizar la cantidad de productos en la base de datos restando la cantidad comprada
            while ($row = $result->fetch_assoc()) {
                $cod_producto = $row['CodProducto'];
                $cantidad_carrito = $row['cantidad'];
        
                // Restar la cantidad comprada del stock disponible
                $sql_restar_cantidad = "UPDATE productos SET Cantidad = Cantidad - $cantidad_carrito WHERE CodProducto = $cod_producto";
                if ($conn->query($sql_restar_cantidad) !== TRUE) {
                    die("Error al restar la cantidad de productos: " . $conn->error);
                }
            }
        
            // Eliminar los productos del carrito en la base de datos
            $sql_eliminar_productos = "DELETE FROM carrito_productos WHERE CodCarrito = $cod_carrito";
            if ($conn->query($sql_eliminar_productos) !== TRUE) {
                die("Error al eliminar productos del carrito: " . $conn->error);
            }
        
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Compra realizada exitosamente',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location = 'carrito.php'; // Redirigir al usuario de vuelta a la página del carrito
                    });
                };
                </script>";
        }else {
            // Si no hay suficiente stock, mostrar un mensaje de advertencia
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        title: 'No hay suficiente stock',
                        text: 'Se están añadiendo 50 unidades al inventario',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location = 'carrito.php'; // Redirigir al usuario de vuelta a la página del carrito
                    });
                };
                </script>";

                 // Actualizar la cantidad de productos en la base de datos añadiendo 50 unidades
                $sql_actualizar_stock = "UPDATE productos SET Cantidad = Cantidad + 50 WHERE CodProducto = $cod_producto";
                if ($conn->query($sql_actualizar_stock) !== TRUE) {
                    die("Error al actualizar el stock de productos: " . $conn->error);
                }

        }
    } 
} 

$conn->close();
?>
