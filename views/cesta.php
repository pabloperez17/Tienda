<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/estilo.css">
    <?php require "../util/conexion.php" ?>
    <?php require "./Objetos/Producto.php" ?>
</head>

<body>
    <?php
    session_start();
    if (isset($_SESSION["usuario"])) {
        $usuario = $_SESSION["usuario"];
        $rol = $_SESSION["rol"];
    } else {
        $_SESSION["usuario"] = "invitado";
        $usuario = $_SESSION["usuario"];
        $_SESSION["rol"] = "cliente";
        $rol = $_SESSION["rol"];
        $_SESSION["usuario"] = "invitado";
        $usuario = $_SESSION["cliente"];
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

    <div class="container">
        <h2 class="text-center mb-3">Cesta</h2>
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT pc.idProducto, p.nombreProducto, p.precio, p.descripcion, pc.cantidad, p.imagen FROM productoscestas pc JOIN productos p ON pc.idProducto = p.idProducto WHERE pc.idCesta = (SELECT idCesta FROM cestas WHERE usuario = '$usuario')";
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
                    <?php
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
            $sql = "SELECT precioTotal FROM cestas WHERE usuario = '$usuario'";
            $resultado = $conexion->query($sql);
            $fila = $resultado->fetch_assoc();
            $precioTotal = $fila['precioTotal'];
            ?>
            <h4>El precio total del carrito es: <?php echo $precioTotal ?>€</h4>
        </div>
</body>

</html>