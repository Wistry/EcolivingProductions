<?php
session_start(); // Iniciar sesión
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
        <?php
        if (isset($_SESSION['usuario'])) {
            echo "<p class='bienvenido'>Bienvenido, " . htmlspecialchars($_SESSION['usuario']) . " <a href='logout.php'>Cerrar sesión</a></p>";
        }
        ?>
    </header>
    <main>
        <section id="Introduccion">
            <h2>Acerca de nosotros</h2>
            <article>
                <p>EcoLiving Productions es una empresa dedicada a la producción de muebles ecológicos. Nuestro compromiso es con el medio ambiente y con la calidad de nuestros productos. Creemos en un futuro sostenible y trabajamos cada día para hacerlo realidad.</p>
                <p>Nuestros muebles están diseñados con materiales reciclados y sostenibles, sin comprometer la calidad y la estética. Creemos que el diseño y la sostenibilidad pueden ir de la mano.</p>
                <p>Con cada compra, estás apoyando a una empresa que se preocupa por nuestro planeta y está comprometida con prácticas comerciales éticas y sostenibles.</p>
                <p>Únete a nosotros en nuestro viaje para hacer del mundo un lugar más verde y sostenible.</p>
            </article>
        </section>
    </main>
    <footer>
        <p>SIE 23/24 Grupo Nº9 EcoLivingProductions<br></p>
    </footer>
</body>
</html>
