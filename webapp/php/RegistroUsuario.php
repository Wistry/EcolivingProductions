<?php
require_once 'db.php'; // Incluir el archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellidos = $conn->real_escape_string($_POST['apellidos']);
    $nifcif = $conn->real_escape_string($_POST['dni']);
    $contraseña = $conn->real_escape_string($_POST['contraseña2']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $email = $conn->real_escape_string($_POST['correo']);
    $domicilio = $conn->real_escape_string($_POST['direccion']);
    $fechaNacimiento = $conn->real_escape_string($_POST['fecha']);

    // SQL para insertar los datos en la tabla 'clientes'
    $sql = "INSERT INTO clientes (Nombre, Apellidos, Contraseña, NIFCIF, Domicilio, Email, Telefono, FechaNacimiento)
            VALUES ('$nombre', '$apellidos', '$contraseña', '$nifcif', '$domicilio', '$email', '$telefono', '$fechaNacimiento')";

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        // Obtener el ID del nuevo cliente
        $cod_cliente = $conn->insert_id;
        
        // SQL para crear una instancia en la tabla 'carrito'
        $sql_carrito = "INSERT INTO carrito (CodCliente) VALUES ('$cod_cliente')";

        if ($conn->query($sql_carrito) === TRUE) {
            echo "Registro exitoso y carrito creado";
            // Redirigir al usuario a una página de éxito o inicio de sesión
            header("Location: index.php");
            exit();
        } else {
            echo "Error al crear el carrito: " . $sql_carrito . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EcoLiving Productions</title>
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
    </header>
    <main>
        <section class="sectionAlta">
            <article id="articleAlta">
                <form action="RegistroUsuario.php" method="post">
                    <fieldset id="FieldSetAlta">
                        <legend>Registro</legend>
                        <ul>
                            <li>
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form" name="nombre" id="nombre" required/>
                            </li>
                            <li>
                                <label for="apellidos">Apellidos:</label>
                                <input type="text" class="form" name="apellidos" id="apellidos" required/>
                            </li>
                            <li>
                                <label for="dni">DNI/NIF:</label>
                                <input type="text" class="form" name="dni" id="dni" required/>
                            </li>
                            <li>
                                <label for="contraseña2">Contraseña:</label>
                                <input type="password" class="form" name="contraseña2" id="contraseña2" required/>
                            </li>
                            <li>
                                <label for="telefono">Teléfono:</label>
                                <input type="tel" class="form" name="telefono" id="telefono" required/>
                            </li>  
                            <li>
                                <label for="correo">Correo:</label>
                                <input type="email" class="form" name="correo" id="correo" required/>
                            </li>
                            <li>
                                <label for="direccion">Dirección:</label>
                                <input type="text" class="form" name="direccion" id="direccion" required/>
                            </li>
                            <li>
                                <label for="fecha">Fecha de Nacimiento:</label>
                                <input type="date" class="form" name="fecha" id="fecha" required/>
                            </li>
                        </ul>
                        <input type="submit" class="botonesAlta" id="buttonEntrega" value="Registrarse"/>
                        <input type="reset" class="botonesAlta" id="buttonReinicio" value="Reiniciar formulario" />
                    </fieldset>
                </form>
            </article>
        </section>
    </main>
    <footer>
        <p>SIE 23/24 Grupo Nº9 EcoLivingProductions<br></p>
    </footer>
</body>
</html>
