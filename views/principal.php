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
    // Inicia la sesión y recupera información del usuario si está logueado
    session_start();
    if (isset($_SESSION["usuario"])) {
        $usuario = $_SESSION["usuario"];
        $rol = $_SESSION["rol"];
    } else {
        // Si no está logueado, se establecen valores predeterminados
        $_SESSION["usuario"] = "invitado";
        $usuario = $_SESSION["usuario"];
        $_SESSION["rol"] = "cliente";
        $rol = $_SESSION["rol"];
    }

    // Procesamiento del formulario cuando se envía
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_producto = $_POST["idProducto"];
        $cantidad_selec = $_POST["cantidad"];
        
        // Verifica si la cantidad seleccionada no está vacía
        if ($cantidad_selec != "") {
            $sql = "SELECT cantidad FROM productos WHERE idProducto = '$id_producto'";
            $cantidad_prod = $conexion->query($sql)->fetch_assoc()["cantidad"];

            // Verifica si hay suficiente cantidad en stock
            if ($cantidad_prod != "0") {
                // Obtiene el ID de la cesta del usuario
                $sql = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
                $idCesta = $conexion->query($sql)->fetch_assoc()["idCesta"];

                // Actualiza la cantidad de productos en la base de datos
                $sql = "UPDATE productos SET cantidad = (cantidad - '$cantidad_selec') WHERE idProducto = '$id_producto'";
                $pillamos_id = "SELECT * FROM productoscestas WHERE idProducto = '$id_producto' AND idCesta = '$idCesta'";
                $conexion->query($sql);

                // Si el producto no está en la cesta, lo inserta; de lo contrario, actualiza la cantidad
                if ($conexion->query($pillamos_id)->num_rows == 0) {
                    $sql = "INSERT INTO productoscestas VALUES ('$id_producto', '$idCesta', '$cantidad_selec')";
                    $conexion->query($sql);
                } else {
                    $sql = "SELECT cantidad FROM productoscestas WHERE idProducto = '$id_producto' AND idCesta = '$idCesta'";
                    $cantidad_cesta = $conexion->query($sql)->fetch_assoc()["cantidad"];
                    $sql = "UPDATE productoscestas SET cantidad = ('$cantidad_selec' + '$cantidad_cesta') WHERE idProducto = '$id_producto' AND idCesta = '$idCesta'";
                    $conexion->query($sql);
                }

                // Actualiza el precio total de la cesta
                $sql = "SELECT precio FROM productos WHERE idProducto = '$id_producto'";
                $precio = $conexion->query($sql)->fetch_assoc()["precio"];
                $sql = "UPDATE cestas SET precioTotal = (precioTotal + '$precio' * '$cantidad_selec') WHERE idCesta = '$idCesta'";
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
                    // Opcion adicional para el administrador
                    if ($_SESSION["rol"] == "admin") {
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="productos.php">Insertar productos</a>
                        </li>
                    <?php
                    }
                    ?>
                    <!-- Opciones comunes para todos los usuarios -->
                    <li class="nav-item">
                        <a class="nav-link" href="principal.php">Almacen</a>
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
    // Recupera la lista de productos desde la base de datos
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
    <!-- Lista de productos -->
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
                    // Muestra cada producto en la tabla
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
                                    <!-- Formulario para añadir productos a la cesta -->
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
                                    <!-- Botón deshabilitado si el usuario es un invitado -->
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
