<?php
session_start();
require_once 'db.php'; // Incluir el archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $dni = $conn->real_escape_string($_POST['dni']);
    $contraseña = $_POST['contraseña'];

    // SQL para seleccionar el usuario con el DNI dado
    $sql = "SELECT CodCliente, Nombre, Contraseña FROM clientes WHERE NIFCIF = '$dni'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener los datos del usuario
        $row = $result->fetch_assoc();
        if ($contraseña === $row['Contraseña']) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['usuario'] = $row['Nombre'];
            $_SESSION['cod_cliente'] = $row['CodCliente'];
            header("Location: index.php");
            exit();
        } else {
            // Contraseña incorrecta
            $error = "Contraseña incorrecta";
        }
    } else {
        // Usuario no encontrado
        $error = "Usuario no encontrado";
    }

    // Cerrar la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EcoLiving Productions - Iniciar Sesión</title>
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
        <?php if (!isset($_SESSION['usuario'])): ?>
            <section class="sectionAlta">
                <form action="IniciarSesion.php" method="post">
                    <fieldset id="FieldSetAlta">
                        <legend>Iniciar Sesión</legend>
                        <?php
                        if (isset($error)) {
                            echo "<p style='color:red;'>$error</p>";
                        }
                        ?>
                        <label for="dni">DNI/NIF</label>
                        <input type="text" id="dni" name="dni" required/>
                        <br>
                        <label for="contraseña">Contraseña</label>
                        <input type="password" id="contraseña" name="contraseña" required/>
                        <br><br>
                        <input type="submit" class="botonesAlta" id="buttonEntrega" value="Iniciar sesión"/>
                        <input type="reset" class="botonesAlta" id="buttonReinicio" value="Reiniciar formulario" />
                        <br><br><br>
                        <a class="botonAmarillo" href="RegistroUsuario.php" id="enlaceRegistro">Registrar usuario</a>
                    </fieldset>
                </form>
            </section>
        <?php else: ?>
            <section class="sectionMensaje">
                <p class="mensajeSesion">Ya estás iniciado sesión.</p>
            </section>
        <?php endif; ?>
    </main>
    <footer>
        <p>SIE 23/24 Grupo Nº9 EcoLivingProductions<br></p>
    </footer>
</body>
</html>
