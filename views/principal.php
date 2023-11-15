<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrarse</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <?php require "../util/conexion.php" ?>
        <?php require "./Objetos/producto.php" ?>
        <link rel="stylesheet" href="./css/estilo.css">
    </head>
    
    <body>
        <?php
        session_start();
        if (isset($_SESSION["usuario"])) {
            $usuario = $_SESSION["usuario"];
            $rol = $_SESSION["rol"];
        } else {
            //header("Location: iniciar_sesion.php");
            $_SESSION["usuario"] = "invitado";
            $usuario = $_SESSION["usuario"];
            $_SESSION["rol"] = "cliente";
            $rol = $_SESSION["rol"];
            $_SESSION["usuario"] = "invitado";
            $usuario = $_SESSION["cliente"];
        }
        ?>
        <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand">Pablo shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    if ($_SESSION["rol"] == "admin") {
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="productos.php">Insertar productos</a>
                        </li>
                    <?php
                    }
                    
                    else{
                    ?>
                        <li>
                        Bienvenid@ <?php echo $usuario ?></a> 
                        </li>
                    <?php
                    }
                    ?>
                </ul>
                <a class="btn btn-secondary" href="cerrar_sesion.php">Cerrar sesión</a>
            </div>
        </div>
    </nav>
    <img src="./logo.PNG" class="logo">
    <?php

    $sql = "SELECT * from productos";
    $resultado = $conexion->query($sql);
    $productos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $nuevo_producto = new Producto(
            $fila["idProducto"],
            $fila["nombreProducto"],
            $fila["precio"],
            $fila["descripcion"],
            $fila["cantidad"],
            $fila["imagen"]
        );
        array_push($productos, $nuevo_producto);
    }

    ?>
        <div class="container">
            <h2 class="text-center mb-3">Lista de productos</h2>

            <div>
                <table class=" container table table-striped table-hover">
                    <thead class="table table-dark">
                        <tr>
                            <th>ID Producto</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Imagen</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM productos";
                        $resultado = $conexion->query($sql);

                        foreach ($productos as $producto) {
                            echo "<tr>";
                            echo "<td>" . $producto->idProducto . "</td>";
                            echo "<td>" . $producto->nombreProducto . "</td>";
                            echo "<td>" . $producto->precio . "</td>";
                            echo "<td>" . $producto->descripcion . "</td>";
                            echo "<td>" . $producto->cantidad . "</td>";
                        ?>
                            <td>
                                <img witdh="50" height="100" src="<?php echo $producto->imagen ?>">
                            </td>
                            <td>
                                <form action="" method="post">
                                    <input class="btn btn-primary" type="submit" value="Añadir">
                                </form>
                            </td>
                        <?php
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>