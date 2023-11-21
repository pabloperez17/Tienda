<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <?php require "../util/conexion.php" ?>
    <?php require "./Objetos/producto.php" ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
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
    #Preparamos el boton de añadir a la cesta para que segun la cantidad que se seleccione, se añada a la cesta ese numero de productos
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_producto = $_POST["idProducto"];
        $cantidad_selec = $_POST["cantidad"];
        if ($cantidad_selec != "") {
            $sql = "select cantidad from productos where idProducto = '$id_producto'";
            $cantidad_prod = $conexion->query($sql)->fetch_assoc()["cantidad"];
            if ($cantidad_prod != "0") {
                $sql = "select idCesta from cestas where usuario = '$usuario'";
                $idCesta = $conexion->query($sql)->fetch_assoc()["idCesta"];
                $sql = "update productos set cantidad = (cantidad - '$cantidad_selec') where idProducto = '$id_producto'";
                $pillamos_id = "select * from productoscestas where idProducto = '$id_producto' and idCesta = '$idCesta'";
                $conexion->query($sql);
                if ($conexion->query($pillamos_id)->num_rows == 0) {
                    $sql = "insert into productoscestas values ('$id_producto', '$idCesta', '$cantidad_selec')";
                    $conexion->query($sql);
                } else {
                    $sql = "select cantidad from productoscestas where idProducto = '$id_producto' and idCesta = '$idCesta'";
                    $cantidad_cesta = $conexion->query($sql)->fetch_assoc()["cantidad"];
                    $sql = "update productoscestas set cantidad = (cantidad + '$cantidad_cesta') where idProducto = '$id_producto' and idCesta = '$idCesta'";
                    $conexion->query($sql);
                }
                $sql = "select precio from productos where idProducto = '$id_producto'";
                $precio = $conexion->query($sql)->fetch_assoc()["precio"];
                $sql = "update cestas set precioTotal = (precioTotal + '$precio' * '$cantidad_selec') where idCesta = '$idCesta'";
                $conexion->query($sql);
            }
        }
    }
    ?>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand"><img src="../views/imagenes/logo.PNG" class="logo"></a>
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
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="principal.php">Ver stock</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cesta.php">Cesta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">Bienvenid@ <?php echo $usuario ?></a>
                    </li>
                </ul>
                <a class="btn btn-secondary" href="cerrar_sesion.php">Cerrar sesión</a>
            </div>
        </div>
    </nav>
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
                            <?php if (($usuario != "invitado")) { ?>
                                <input type="hidden" name="idProducto" value="<?php echo $producto->idProducto ?>">
                                <label for="cantidad">Cantidad:</label>
                                <select name="cantidad">
                                    <?php
                                    $sql = "SELECT cantidad FROM productos where idProducto = '$producto->idProducto'";
                                    $cantidadActual = $conexion->query($sql)->fetch_assoc()["cantidad"];
                                    $maxCantidad = min(5, $cantidadActual);
                                    for ($i = 1; $i <= $maxCantidad; $i++) {
                                    ?>
                                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <?php
                                if ($cantidadActual > 0) {
                                ?>
                                    <input class="btn btn-primary" type="submit" value="Añadir">
                                <?php
                                } else {
                                ?>
                                    <input class="btn btn-primary" type="submit" value="Añadir" disabled>
                                <?php
                                }
                            } else { ?>
                                <input class="btn btn-primary" type="submit" value="Añadir" disabled>
                            <?php } ?>
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